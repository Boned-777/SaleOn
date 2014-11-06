<?php

class Application_Model_Ad
{
	public $id;
	public $name;
	public $description;
    public $full_description;
	public $public_dt;
	public $end_dt;
	public $region;
	public $category;
	public $brand;
    public $brand_name;
    public $product;
    public $product_name;
	public $address;
	public $phone;
    public $paid;
    public $phone1;
    public $phone2;
	public $fax;
	public $url;
    public $video;
    public $image;
    public $banner;
    public $owner;
    public $email;
    public $geo;
    public $geo_name;
    public $status;
    public $order_index;
    public $location;
    public $addresses;

    public function load($data) {
        $vars = get_class_vars(get_class());
        foreach ($vars as $key => $value) {
            switch ($key) {
                case "brand_name":
                    if ($data['brand']) {
                        $item = new Application_Model_DbTable_Brand();
                        $this->brand_name = $item->getNameById($data['brand']);
                    }
                    break;

                case "product_name":
                    if ($data['product']) {
                        $item = new Application_Model_DbTable_Product();
                        $this->product_name = $item->getNameById($data['product']);
                    }
                    break;

                case "location":
                    if (!$this->location) {
                        $collection = new Application_Model_AdLocationCollection();
                        $collection->getByAdId($this->id);
                        $this->location = $collection;
                    }
                    break;

                case "addresses":
                    $addresses = new Application_Model_AdAddressCollection();
                    $addresses->getByAdId($data["id"], $data["owner"]);
                    $this->addresses = $addresses;
                    break;

                default:
                    if (isset($data[$key]))
                        $this->$key = $data[$key];
                    break;
            }
        }
    }

    public function loadIfEmpty($data) {
        $vars = get_class_vars(get_class());
        foreach ($vars as $key => $value) {
            switch ($key) {
                case "brand_name":
                    $item = new Application_Model_DbTable_Brand();
                    $this->brand_name = $item->getNameById($this->brand);
                    break;

                case "product_name":
                    $item = new Application_Model_DbTable_Product();
                    $this->product_name = $item->getNameById($this->product);
                    break;

                case "url":
                    if (!empty($this->$key))
                        break;
                    $this->url = $data["web"];
                    break;

                default:
                    if (!empty($this->$key))
                        break;
                    if (isset($data[$key]))
                        $this->$key = $data[$key];
                    break;
            }
        }
    }

    public function isValid() {
        $vars = get_class_vars(get_class());
        foreach ($vars as $key => $value) {
            switch ($key) {
                case "brand_name":
                case "full_description":
                case "product":
                case "product_name":
                case "paid":
                case "phone1":
                case "phone2":
                case "fax":
                case "url":
                case "video":
                case "image":
                case "geo":
                case "geo_name":
                case "status":
                case "order_index":
                case "region":
                case "addresses":
                    break;

                default:
                    if (empty($this->$key)) {
                        return false;
                    }
                    break;
            }
        }
        return true;
    }

    public function save() {
        $isNew = false;
        if (!$this->id) {
            $isNew = true;
        }
        $vars = get_class_vars(get_class());
        $data = array();
        foreach ($vars as $key => $value) {
            switch ($key) {
                case 'brand_name' :
                case 'product_name':
                case "geo":
                case "addresses":
                    break;

                case 'location':
                    if (!$isNew) {
                        $this->location->save();
                    }
                    break;

                case "status":
                    if (!empty($this->status)) {
                        $data[$key] = $this->status;
                    } else {
                        $data[$key] = Application_Model_DbTable_Ad::STATUS_DRAFT;
                    }
                    break;

                default:
                    $data[$key] = $this->$key;
                    break;
            }

        }

        $dbItem = new Application_Model_DbTable_Ad();
        if ($this->id) {
            $this->finishAllOrders();
        }

        $res = $dbItem->save($data, $this->id);
        if ($res !== false) {
            $this->id = $res;
        }

        if ($isNew) {
            $this->location->getByAdId($this->id);
            $this->location->setLocationsList(array("1"));
            $this->location->save();
        }

        return $res;
    }

    public function finishAllOrders() {
        $order = new Application_Model_Order();
        if ($order->getByAd($this->id)) {
            $order->status = Application_Model_Order::STATUS_CANCELED;
            $order->save();
        }
    }

    public function get($id) {
        $item = new Application_Model_DbTable_Ad();
        $data = $item->get($id);
        if ($data !== false) {
            $this->load($data);
            return $data;
        } else {
            return false;
        }
    }

    public function getRegularList($params=null) {
        $item = new Application_Model_DbTable_Ad();
        $select = $item->select();
        $select->setIntegrityCheck(false);
        $select
            ->distinct()
            ->from(array("a" => "ads"), array("a.*"))
            ->where("(a.end_dt >= NOW() - INTERVAL 1 DAY) AND a.public_dt <= NOW() AND a.status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE);
        if (!is_null($params)) {
            foreach ($params as $key => $val) {
                switch ($key) {
                    case "sort":
                    case "favorites_list" :
                        break;
                    case "geo" :
                        $item->select()->setIntegrityCheck(false);
                        $select->join(array("al" => "AdLocation"), "a.id = al.ad_id");
                        $geoWhere = Application_Model_DbTable_AdLocation::prepareWhereStatement($val, "al");
                        $select->where($geoWhere);
                        break;

                    default :
                        $select->where("$key = ?", $val);
                        break;
                }
            }
        }
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns("a.*");

        switch (isset($params["sort"]) ? isset($params["sort"]) : null) {
            case "new" :
                $select->order("a.public_dt DESC");
                break;

            default:
                $select->order("a.order_index DESC");

        }

        $data = $item->fetchAll($select);

        if ($data !== false) {
            $res = array();
            $data = $data->toArray();
            foreach ($data AS $val) {
                $tmp = new Application_Model_Ad();
                $tmp->load($val);
                $res[] = $tmp;
            }
            return $res;
        } else {
            return false;
        }
    }

    public function getList($params=null) {
        $sort = isset($params["sort"]) ? $params["sort"] : "regular";

        switch ($sort) {
            case "favorite" :
                $items = $this->getFavorites($params["favorites_list"]);
                break;

            default :
                $items = $this->getRegularList($params);
        }

        return $items;
    }

    public function getNewsList($params=null) {
        $item = new Application_Model_DbTable_Ad();
        $select = $item->select();
        $select->setIntegrityCheck(false);
        $select
            ->distinct()
            ->from(array("a" => "ads"), array("a.*"))
            ->where("(a.end_dt >= NOW() - INTERVAL 1 DAY) AND a.public_dt <= NOW() AND a.status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE)
            ->order("a.public_dt DESC");
        if (!is_null($params)) {
            foreach ($params as $key => $val) {
                switch ($key) {
                    case "geo" :
                        $item->select()->setIntegrityCheck(false);
                        $select->join(array("al" => "AdLocation"), "a.id = al.ad_id");
                        $geoWhere = Application_Model_DbTable_AdLocation::prepareWhereStatement($val, "al");
                        $select->where($geoWhere);
                        break;

                    default :
                        $select->where("$key = ?", $val);
                        break;
                }
            }
        }
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns("a.*");

        $data = $item->fetchAll($select);
        if ($data !== false) {
            $res = array();
            $data = $data->toArray();
            foreach ($data AS $val) {
                $tmp = new Application_Model_Ad();
                $tmp->load($val);
                $res[] = $tmp;
            }
            return $res;
        } else {
            return false;
        }
    }

    public function getFavorites($favorites_ads) {
        if (!empty($favorites_ads)) {
            $item = new Application_Model_DbTable_Ad();
            $stmt = $item->select()
                ->where("end_dt > NOW() AND status = ? AND id IN (" . $favorites_ads . ")", Application_Model_DbTable_Ad::STATUS_ACTIVE);
            $data = $item->fetchAll($stmt);
            if ($data !== false) {
                $res = array();
                $data = $data->toArray();
                foreach ($data AS $val) {
                    $tmp = new Application_Model_Ad();
                    $tmp->load($val);
                    $res[] = $tmp;
                }
                return $res;
            } else {
                return false;
            }
        } else {
            return array();
        }
    }

    public function setStatus($value) {
        $this->status = $value;
        return $this->save();
    }

    public function toListArray($user) {
        $vars = array(
            "post_id" => "id",
            "post_full_url" => "url",
            "brand_name" => "brand_name",
            "name" => "name",
            "photoimg" => "banner",
        );

        $data = array();
        foreach ($vars AS $key => $value) {
            $data[$key] = $this->$value;
        }

        if (is_null($user)) {
            $data["favorites_link"] = '/auth';
            $data["is_favorite"] = 0;
        } else {
            $favoritesAdsList = "";
            if (isset($user->favorites_ads))
                $favoritesAdsList = $user->favorites_ads;
            if (!in_array($this->id, explode(",",$favoritesAdsList))) {
                $data["is_favorite"] = 0;
                $data["favorites_link"] = '/user/favorites?ad_id=' . $this->id . '&act=add';
            } else {
                $data["is_favorite"] = 1;
                $data["favorites_link"] = '/user/favorites?ad_id=' . $this->id . '&act=remove';
            }
        }
        $data["days"] = $this->getDaysLeft();
        $translite = new Zend_Filter_Transliteration();
        $data["seo_name"] = $this->id . "_" . $translite->filter($this->name);
        return $data;
    }

    public function getDaysLeft() {
        if (strtotime($this->public_dt) < time())
            return ceil((strtotime($this->end_dt) - time()) / 86400) + 1;
        else
            return $this->getDaysCount();
    }

    public function getDaysCount() {
        return ceil((strtotime($this->end_dt) - strtotime($this->public_dt)) / 86400) + 1;
    }

    public function toArray() {
        $vars = get_class_vars(get_class());
        $data = array();
        foreach ($vars as $key => $value) {
            $data[$key] = $this->$key;
        }
        return $data;
    }

    public function randomizeAll() {
        $item = new Application_Model_DbTable_Ad();
        $item->clearOrderIndexes();
        $item->archiveAllFinished();
        $select = $item->select()
            ->where("status = ? AND end_dt > NOW() AND public_dt <= NOW()", Application_Model_DbTable_Ad::STATUS_ACTIVE)
            ->order("RAND()");
        $data = $item->fetchAll($select);
        $index = 1;

        foreach ($data as $value) {
            $value->order_index = $index;
            $value->save();
            $index++;
        }
        die("Randomize finished");
    }

    public function getNeighborhood($params=null) {
        $item = new Application_Model_DbTable_Ad();
        $select = $item->select();
        $select->setIntegrityCheck(false);
        $select
            ->distinct()
            ->from(array("a" => "ads"), array("a.*"))
            ->where("(a.end_dt >= NOW() - INTERVAL 1 DAY) AND a.public_dt <= NOW() AND a.status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE)
            ->order("a.order_index");
        if (!is_null($params)) {
            foreach ($params as $key => $val) {
                switch ($key) {
                    case "geo" :
                        $item->select()->setIntegrityCheck(false);
                        $select->join(array("al" => "AdLocation"), "a.id = al.ad_id");
                        $geoWhere = Application_Model_DbTable_AdLocation::prepareWhereStatement($val, "al");
                        $select->where($geoWhere);
                        break;

                    default :
                        $select->where("$key = ?", $val);
                        break;
                }
            }
        }
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns("a.*");

        $data = $item->fetchAll($select);
        $data = $data->toArray();
        $previousItem = null;
        $nextItem = null;
        foreach ($data as $key => $adItem) {
            if ($adItem["order_index"] == $this->order_index) {
                $nextItem = isset($data[$key+1]) ? $data[$key+1] : null;
                break;
            }
            $previousItem = $adItem;
        }
        $previousItemObj = null;
        $nextItemObj = null;
        if (!is_null($previousItem)) {
            $previousItemObj = new Application_Model_Ad();
            $previousItemObj->load($previousItem);
        }

        if (!is_null($nextItem)) {
            $nextItemObj = new Application_Model_Ad();
            $nextItemObj->load($nextItem);
        }

        $res = array(
            "previous" => $previousItemObj,
            "next" => $nextItemObj
        );
        return $res;
    }

    public function createUrl() {
        return "/ad/index/id/" . $this->id;
    }

    public function checkFavorites($user, $template="", &$url=null) {
        if (is_null($user)) {
            $url = '/auth';
            return false;
        } else {
            $favoritesAdsList = "";
            if (isset($user->favorites_ads))
                $favoritesAdsList = $user->favorites_ads;
            if (!in_array($this->id, explode(",",$favoritesAdsList))) {
                $url = $template . 'add';
                return false;
            } else {
                $url = $template . 'remove';
                return true;
            }
        }
    }

    public function getFavoritesUrl($user=null, $operation=null) {
        $template = '/user/favorites?ad_id=' . $this->id . '&act=';
        if (!is_null($operation))
            return $template . $operation;

        $this->checkFavorites($user, $template, $resultUrl);
        return $resultUrl;
    }

    public function getPrice() {
//        $basePrice = array(
//            1 => 10,
//            2 => 5,
//            3 => 2
//        );

        $basePrice = array(
            1 => 0,
            2 => 0,
            3 => 0
        );
        $daysCount = $this->getDaysLeft();
        $geo = explode("-", $this->geo);
        foreach ($geo as $key=>$val) {
            if ($val == 0) {
                unset($geo[$key]);
            }
        }
        return 0;
        //return $basePrice[count($geo)] * $daysCount;
    }

    public function addAddress($adAddress) {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $partner = new Application_Model_Partner();
        if ($partner->getByUserId($identity->id)) {
            $id = $partner->addAddress($adAddress);
            $this->_helper->json(array("success" => true, "id"=>$id));
        };

        $db = new Application_Model_DbTable_AdAddress();
        $raw = $db->createRow();
        $raw->ad_id = $this->id;
        $raw->address_id = $adAddress;
    }
}
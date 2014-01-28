<?php

class Application_Model_Ad
{
	public $id;
	public $name;
	public $description;
    public $full_description;
	public $public_dt;
	public $start_dt;
	public $end_dt;
	public $region;
	public $category;
	public $brand;
    public $brand_name;
    public $product;
    public $product_name;
	public $address;
	public $phone;
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
                case "phone1":
                case "phone2":
                case "fax":
                case "url":
                case "video":
                case "image":
                case "geo":
                case "geo_name":
                case "status":
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
        $vars = get_class_vars(get_class());
        $data = array();
        foreach ($vars as $key => $value) {
            switch ($key) {
                case 'brand_name' :
                case 'product_name':
                    break;

                case "status":
                    if (!empty($this->status)) {
                        $data[$key] = $this->status;
                    } else {
                        $data[$key] = Application_Model_DbTable_Ad::STATUS_DRAFT;
                    }
                    break;

                case "geo":
                    if (!empty($this->geo)) {
                        $data[$key] = "1";
                    }
                    break;

                default:
                    $data[$key] = $this->$key;
                    break;
            }

        }
        $dbItem = new Application_Model_DbTable_Ad();
        $res = $dbItem->save($data, $this->id);
        if ($res !== false)
            $this->id = $res;
        return $res;
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

    public function getList($params) {
        $item = new Application_Model_DbTable_Ad();
        $stmt = $item->select()
            ->where("end_dt >= NOW() AND status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE)
            ->order("order_index DESC");
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
            //"region" => "geo_name",
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
        return $data;
    }

    public function getDaysLeft() {
        return ceil((strtotime($this->end_dt) - time()) / 86400) + 1;
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
            ->where("status = ? AND end_dt > NOW()", Application_Model_DbTable_Ad::STATUS_ACTIVE)
            ->order("RAND()");
        $data = $item->fetchAll($select);
        $index = 1;
        foreach ($data->toArray() as $value) {
            $item->save(array("order_index" => $index), $value["id"]);
            $index++;
        }
        die("Randomize finished");
    }

    public function getNeighborhood() {
        $item = new Application_Model_DbTable_Ad();
        $select = $item->select()
            ->where("order_index IN (?,?) AND end_dt >= NOW()", array($this->order_index-1, $this->order_index+1))
            ->order("order_index");
        $data = $item->fetchAll($select);
        $data = $data->toArray();

        if (isset($data[0]) && $this->order_index > $data[0]["order_index"]) {
            $previousItem = new Application_Model_Ad();
            $previousItem->load($data[0]);
        } else {
            $previousItem = null;
        }

        if (isset($data[1]) || $this->order_index < $data[0]["order_index"]) {
            $i = 1;
            if ($this->order_index < $data[0]["order_index"])
                $i = 0;
            $nextItem = new Application_Model_Ad();
            $nextItem->load($data[$i]);
        } else {
            $nextItem = null;
        }

        $res = array(
            "previous" => $previousItem,
            "next" => $nextItem
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
}


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
            ->where("status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE);
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
            if (!in_array($this->id, explode(",",$user->favorites_ads))) {
                $data["is_favorite"] = 0;
                $data["favorites_link"] = '/user/favorites?ad_id=' . $this->id . '&act=add';
            } else {
                $data["is_favorite"] = 1;
                $data["favorites_link"] = '/user/favorites?ad_id=' . $this->id . '&act=remove';
            }
        }
        $data["days"] = ceil((strtotime($this->end_dt) - time()) / 86400) + 1;
        return $data;
    }

    public function toArray() {
        $vars = get_class_vars(get_class());
        $data = array();
        foreach ($vars as $key => $value) {
            $data[$key] = $this->$key;
        }
        return $data;
    }

}


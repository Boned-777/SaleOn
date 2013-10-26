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
	public $address;
	public $phone;
	public $fax;
	public $url;
    public $video;
    public $image;
    public $banner;
    public $owner;


    public function load($data) {
        $vars = get_class_vars(get_class());
        foreach ($vars as $key => $value) {
            if (isset($data[$key]))
                $this->$key = $data[$key];
        }
    }

    public function save() {
        $vars = get_class_vars(get_class());
        $data = array();
        foreach ($vars as $key => $value) {
            $data[$key] = $this->$key;
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

    public function toArray() {
        $vars = get_class_vars(get_class());
        $data = array();
        foreach ($vars as $key => $value) {
            $data[$key] = $this->$key;
        }
        return $data;
    }

}


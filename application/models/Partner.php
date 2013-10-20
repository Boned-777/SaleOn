<?php

class Application_Model_Partner
{
    public $id;
    public $user_id;
    public $phone;
    public $enterprise;
    public $brand;
    public $address;
    public $web;

    public function create($data) {
        $dbItem = new Application_Model_DbTable_User();
        $res = $dbItem->create(array(
            "password" => $data["password"],
            "username" => $data["username"]
        ));
        if (!$res)
            return false;
        $this->load($data);
        $this->user_id = $res;
        $this->save();
        return true;
    }

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
        $dbItem = new Application_Model_DbTable_Partner();
        $res = $dbItem->save($data, $this->id);
        return (bool)$res;
    }

    public function get($id) {
        $dbItem = new Application_Model_DbTable_Partner();
        $data = $dbItem->get($id);
        if ($data !== false)
            $this->load($data);

        return $data;
    }


}


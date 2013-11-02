<?php

class Application_Model_Partner
{
    public $id;
    public $user_id;
    public $phone;
    public $enterprise;
    public $brand;
    public $brand_name;
    public $address;
    public $web;
    public $email;


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
            switch ($key) {
                case "brand_name":
                    if ($data['brand']) {
                        $item = new Application_Model_DbTable_Brand();
                        $this->brand_name = $item->getNameById($data['brand']);
                    }
                    break;

                default:
                    if (isset($data[$key]))
                        $this->$key = $data[$key];
                    break;
            }
        }
    }

    public function save() {
        $vars = get_class_vars(get_class());
        $data = array();
        foreach ($vars as $key => $value) {
            switch ($key) {
                case 'brand_name' :
                    break;

                default:
                    $data[$key] = $this->$key;
                    break;
            }

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

    public function getByUserId($id) {
        $dbItem = new Application_Model_DbTable_Partner();
        $stmt = $dbItem->select()->where("user_id = ?", $id);
        $stmt = $stmt->query();
        $result = $stmt->fetchAll();
        if (isset($result[0])) {
            $this->load($result[0]);
        } else {
            return false;
        }

        return $result[0];
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


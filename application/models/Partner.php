<?php

class Application_Model_Partner
{
    public $id;
    public $user_id;
    public $phone;
    public $phone1;
    public $phone2;
    public $enterprise;
    public $brand;
    public $brand_name;
    public $address;
    public $web;
    public $email;
    public $addresses;

    public $user;

    public function create($data) {
        $dbItem = new Application_Model_DbTable_User();
        $res = $dbItem->create(array(
            "password" => $data["password"],
            "username" => $data["username"],
            "role" => Application_Model_User::PARTNER
        ));
        if (!$res) {
            return false;
        }
        $this->load($data);
        $this->id = null;
        $this->user = new Application_Model_User();
        $this->user->getByUserId($res);
        $this->user_id = $res;
        $this->save();
        return true;
    }

    public function load($data) {
        $vars = get_class_vars(get_class());
        foreach ($vars as $key => $value) {
            switch ($key) {
                case "brand_name":
                    $this->brand = null;
                    $this->brand_name = null;
                    break;

                case "addresses":
                    $this->addresses = new Application_Model_PartnerAddressCollection();
                    $this->addresses->get();
                    break;

                case "brand":
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
                case 'addresses' :
                case 'user' :
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

    public function getByUsername($username) {
        if ($username) {
            $user = new Application_Model_User();
            if ($user->getByUsername($username)) {
                $data = $this->getByUserId($user->id);
                return $data;
            }
        }
        return false;
    }

    public function getByUserId($id) {
        $dbItem = new Application_Model_DbTable_Partner();
        $stmt = $dbItem->select()->where("user_id = ?", $id);
        $result = $dbItem->fetchRow($stmt);

        if (is_null($result)) {
            return false;
        }

        $data = $result->toArray();

        if ($data) {
            $this->load($data);
        } else {
            return false;
        }

        $this->user = new Application_Model_User();
        $this->user->getByUserId($id);
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

    public function addAddress($addressValue) {
        $item = new Application_Model_DbTable_PartnerAddress();
        $raw = $item->createRow();
        $raw->user_id = $this->user_id;
        $raw->name = $addressValue;
        return $raw->save();
    }

    public function removeAddress($addressId) {
        $raw = $this->checkAddress($addressId);
        if ($raw) {
            if ($raw->delete()) {
                return true;
            }
        }
        return false;
    }

    public function checkAddress($addressId) {
        $db = new Application_Model_DbTable_PartnerAddress();
        $select = $db->select()
            ->where("user_id = ?", $this->user_id)
            ->where("id = ?", $addressId);
        $raw = $db->fetchRow($select);
        if ($raw) {
            return $raw;
        }
        return false;
    }

    public function getBrands() {
        $brands = new Application_Model_Brand();
        return $brands->getByPartnerId($this->id);

    }

}


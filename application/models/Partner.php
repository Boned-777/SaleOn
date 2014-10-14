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
                    $this->brand = null;
                    $this->brand_name = null;

                    $item = new Application_Model_DbTable_Brand();
                    $res = $item->getOrCreate($data['brand'], !empty($data['brand_name']) ? $data['brand_name'] : null);

                    if ($res !== false) {
                        $this->brand = $res->id;
                        $this->brand_name = $res->name;
                    }
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


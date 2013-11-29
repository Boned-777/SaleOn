<?php

class Application_Model_User
{
    public $id;
    public $username;
    public $recovery;
    public $favorites_ads;

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
            switch ($key) {
                case 'brand_name' :
                    break;

                default:
                    $data[$key] = $this->$key;
                    break;
            }

        }
        $dbItem = new Application_Model_DbTable_User();
        $res = $dbItem->save($data, $this->id);
        return (bool)$res;
    }

    public function get($id) {
        $dbItem = new Application_Model_DbTable_Partner();
        $data = $dbItem->get($id);
        if ($data !== false)
            $this->load($data);
            print_r($data); die();

        return $data;
    }

    public function getUser($id) {
        $dbItem = new Application_Model_DbTable_User();
        $data = $dbItem->get($id);
        if ($data !== false)
            $this->load($data);

        return $data;
    }

    public function getByUsername($username) {
        $dbItem = new Application_Model_DbTable_User();
        $data = $dbItem->getByUsername($username);
        if ($data !== false)
            $this->load($data);
        else
            return false;

        return $data;
    }

    public function getByRecoveryCode($code) {
        $dbItem = new Application_Model_DbTable_User();
        $data = $dbItem->getByRecoveryCode($code);
        if ($data !== false)
            $this->load($data);
        else
            return false;

        return $data;
    }

    public function addFavoriteAd($adId) {
        $list = explode(",",$this->favorites_ads);
        if (empty($list[0]))
            $list = array();
        $list[] = $adId;
        $list = array_unique($list);
        $this->favorites_ads = implode(",",$list);
    }

    public function removeFavoriteAd($adId) {
        $list = explode(",",$this->favorites_ads);
        $res = array();
        foreach ($list as $value) {
            if ($value != $adId)
                $res[] = $value;
        }
        $this->favorites_ads = implode(",",$res);
    }
}


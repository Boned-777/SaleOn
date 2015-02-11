<?php
class Application_Model_UniversalUser
{
    const  PARTNER = "PARTNER";
    const  USER = "USER";
    const  ADMIN = "ADMIN";

    public $id;
    public $username;
    public $recovery;
    public $role;
    public $favorites_ads;
    public $locale;

    public function load($data) {
        $vars = get_class_vars(get_class());
        foreach ($vars as $key => $value) {
            if (isset($data[$key]))
                $this->$key = $data[$key];
        }
    }

    public function save() {
        $data = $this->toArray();
        $dbItem = new Application_Model_DbTable_User();
        $res = $dbItem->save($data, $this->id);
        return (bool)$res;
    }

    public function toArray() {
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
        return $data;
    }

    public function loadExtras($data) {
        return true;
    }

    public function saveExtras() {
        return true;
    }

    public function extrasToArray() {
        return array();
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

    public function create($data) {
        $dbItem = new Application_Model_DbTable_User();
        $res = $dbItem->create(array(
            "password" => $data["password"],
            "username" => $data["username"],
            "role" => $data["role"],
        ));
        if (!$res)
            return false;
        return $res;
    }

    public function getBySocial($socialId, $socialType) {
        $dbItem = new Application_Model_DbTable_User();
        $data = $dbItem->getBySocial($socialId, $socialType);
        if ($data === false) {
            $createUserRes = $dbItem->createSocial($socialId, $socialType);
            if ($createUserRes) {
                $data = $dbItem->get($createUserRes["id"]);
            } else {
                return false;
            }
        }
        $this->load($data);
        return true;
    }

    function setGlobalLocale($lang) {
        $langList = array("ru", "ua", "en", "pl");
        $session = new Zend_Session_Namespace();
        if (in_array($lang, $langList)) {
            $session->locale = $lang;
            return true;
        } else {
            return false;
        }
    }


}

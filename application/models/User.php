<?php
require_once APPLICATION_PATH.'/../library/Google/Google_Client.php';
require_once APPLICATION_PATH.'/../library/Google/contrib/Google_PlusService.php';
require_once APPLICATION_PATH.'/../library/Google/contrib/Google_Oauth2Service.php';

class Application_Model_User
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

    public function toObject() {
        $vars = get_class_vars(get_class());
        $data = new stdClass();
        foreach ($vars as $key => $value) {
            switch ($key) {
                case 'brand_name' :
                    break;

                default:
                    $data->$key = $this->$key;
                    break;
            }
        }
        return $data;
    }

    public function get($id) {
        $dbItem = new Application_Model_DbTable_Partner();
        $data = $dbItem->get($id);
        if ($data !== false)
            $this->load($data);

        return $data;
    }

    public function getByUserId($id) {
        $dbItem = new Application_Model_DbTable_User();
        $data = $dbItem->get($id);
        if ($data !== false)
            $this->load($data);

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

    public function create($data) {
        $itemData = array(
            "password" => $data["password"],
            "username" => $data["username"],
            "role" => $data["role"]
        );

        $dbItem = new Application_Model_DbTable_User();
        $res = $dbItem->create($itemData);
        if (!$res) {
            return false;
        }

        $this->id = $res;
        $this->load($itemData);
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

    static public function prepareGoogleLink()
    {
        $client = new Google_Client();
        $client->setApplicationName("WantLook");
        $plus = new Google_PlusService($client);

        if ($client->getAccessToken()) {
            $client->setUseBatch(true);
            $batch = new Google_BatchRequest();
            $batch->add($plus->people->get('me'), 'key1');
            $batch->add($plus->people->get('me'), 'key2');
            $result = $batch->execute();
            $_SESSION['token'] = $client->getAccessToken();
        } else {
            $authUrl = $client->createAuthUrl();
            return $authUrl;
        }
    }

    public function getAuthObject() {
        $obj = new stdClass();
        $obj->id = $this->id;
        $obj->username = $this->username;
        $obj->locale = $this->locale;
        $obj->role = $this->role;
        $obj->username = $this->username;
        $obj->favorites_ads = $this->favorites_ads;

        return $obj;
    }
}


<?php

class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{

    protected $_name = 'users';

	public function create($vars, $sendHello=true) {
        if ($itemData = $this->getByUsername($vars["username"])) {
            return $itemData["id"];
        }

		$data = array();
		$password = $vars["password"];
		$data["salt"] = md5($this->_passwordGenerator(6));
		$data["password"] = sha1($password.$data["salt"]);
		$data["date_created"] = date('Y-m-d H:i:s');
		$data["username"] = $vars["username"];
		$data["role"] = $vars["role"];
        $data["social_id"] = "";
        $data["social_type"] = "";

        try {
            $item = $this->createRow($data);
            $res = $item->save();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

		if ($res === false) {
			return false;
		}
        if ($sendHello) {
            $this->sendHelloMsg($data);
        }
		return $res;
	}
	
	public function save($data, $id=NULL) {
		try {
			if ($id)
				$res = $this->update($data, 'id = '. (int)$id);
			else {
				if (isset($data["id"]))
					unset($data["id"]);
				$res = $this->insert($data);
                $this->sendHelloMsg($data);
			}
			return $res;
		} catch (Exception $e) {
			return FALSE;
		}
	}

	public function delete($id) {
		$this->save(array("deleted" => 1), $id);
	}
	
	private function _passwordGenerator($symbols_count = 6) {
		$string = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";
		
		$res = "";
		for ($i=0; $i < $symbols_count; $i++) {
			$res .= substr($string, rand(0, strlen($string)-1), 1);
		}
		
		return $res;
	}
	
	public function getUserByID($id)
    {
		$res = $this->find($id);
		$res = $res->getRow(0)->toArray();
		return $res["name"] . " (" . $res["id"] .")";
    }

    public function getByUsername($val)
    {
        try {
            $stmt = $this->select()->where('username = ?', $val);
            $res = $this->fetchAll($stmt);
        } catch (Exception $e) {
            return false;
        }
        if ($res->count()) {
            $res = $res->getRow(0)->toArray();
            return $res;
        } else
            return false;
    }

    public function get($val)
    {
        try {
            $stmt = $this->select()->where('id = ?', $val);
            $res = $this->fetchAll($stmt);
        } catch (Exception $e) {
            return false;
        }
        if ($res->count()) {
            $res = $res->getRow(0)->toArray();
            return $res;
        } else
            return false;
    }

    public function getByRecoveryCode($val)
    {
        try {
            $stmt = $this->select()->where('recovery = ?', $val);
            $res = $this->fetchAll($stmt);
        } catch (Exception $e) {
            return false;
        }
        if ($res->count()) {
            $res = $res->getRow(0)->toArray();
            return $res;
        } else
            return false;
    }

    public function getBySocial($val, $socialType)
    {
        try {
            $stmt = $this->select()->where("social_id = '$val' AND social_type = '$socialType'");
            $res = $this->fetchAll($stmt);
        } catch (Exception $e) {
            return false;
        }
        if ($res->count()) {
            $res = $res->getRow(0)->toArray();
            return $res;
        } else
            return false;
    }

    public function createSocial($socialId, $socialType) {
        $data = array();
        $password = $this->_passwordGenerator(6);
        $data["salt"] = md5($this->_passwordGenerator(6));
        $data["password"] = sha1($password.$data["salt"]);
        $data["date_created"] = "NOW()";
        $data["username"] = $socialId;
        $data["social_id"] = $socialId;
        $data["social_type"] = $socialType;
        $data["role"] = Application_Model_User::USER;
        $res = $this->save($data);

        if ($res === false) {
            return false;
        }

        return $data;
    }

    public function sendHelloMsg($data) {

        $text = "Рады приветствовать Вас на сайте saleon.info".
            "\n\n\nWelcome on WantLook.info";

        $email = new Application_Model_MandrillAdapter();
        $res = $email->sendText('Регистрация на saleon.info', $text, array($data["username"] => ""));

        return $res;
    }
}


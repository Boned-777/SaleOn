<?php

class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{

    protected $_name = 'users';

	public function create($vars) {
		$data = array();
		$password = $vars["password"];
		$data["salt"] = md5($this->_passwordGenerator(6));
		$data["password"] = sha1($password.$data["salt"]);
		$data["date_created"] = "NOW()";
		$data["username"] = $vars["username"];
		$data["role"] = $vars["role"];;
		$res = $this->save($data);
		
		if ($res === false) {
			return false;
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
        $mailhost= '127.0.0.1';
        $mailconfig = array(
            'port'    =>  '25',
        );

        $transport = new Zend_Mail_Transport_Smtp ($mailhost, $mailconfig);
        Zend_Mail::setDefaultTransport($transport);

        $text = "Рады приветствовать Вас на сайте WantLook.info".
            "\n\n\nWelcome on WantLook.info";

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText($text);
        $mail->setFrom('no-reply@wantlook.info', 'WantLook.info');
        $mail->addTo($data["username"], '');
        $mail->setSubject('Регистрация на WantLook.info');
        try {
            $mail->send();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}


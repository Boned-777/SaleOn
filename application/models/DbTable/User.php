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
		$data["role"] = "PARTNER";
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


}


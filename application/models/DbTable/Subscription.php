<?php

class Application_Model_DbTable_Subscription extends Zend_Db_Table_Abstract
{
    protected $_name = 'subscription';

    public function save($data, $id) {
        try {
            if (!empty($id)) {
                unset($data["owner"]);
                $res = $this->update($data, 'id = '. (int)$id);
                $res = $id;
            } else {
                if (isset($data["id"]))
                    unset($data["id"]);
                $res = $this->insert($data);
            }
            return $res;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public function get($id)
    {
        try {
            $res = $this->find($id);
        } catch (Exception $e) {
            return false;
        }

        $res = $res->getRow(0)->toArray();
        return $res;
    }

    public function send() {

    }

}


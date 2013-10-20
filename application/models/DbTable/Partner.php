<?php

class Application_Model_DbTable_Partner extends Zend_Db_Table_Abstract
{
    protected $_name = 'partners';

    public function save($data, $id) {
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

    public function getByUserId($id)
    {
        try {
            $res = $this->fetchAll("WHERE user_id = " . $id);
        } catch (Exception $e) {
            return false;
        }

        $res = $res->getRow(0)->toArray();
        return $res;
    }
}
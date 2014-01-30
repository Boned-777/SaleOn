<?php

class Application_Model_DbTable_Ad extends Zend_Db_Table_Abstract
{
    const STATUS_DRAFT = "DRAFT";
    const STATUS_READY = "READY";
    const STATUS_ACTIVE = "ACTIVE";
    const STATUS_ARCHIVE = "ARCHIVE";

    protected $_name = 'ads';

    public function save($data, $id) {
        try {
            if (!empty($id)) {
                $res = $this->update($data, 'id = '. (int)$id);
                $res = $id;
            } else {
                if (isset($data["id"]))
                    unset($data["id"]);
                echo $res = $this->insert($data);
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

    public function clearOrderIndexes() {
        $sql = "UPDATE $this->_name SET order_index=NULL";
        $db = $this->getAdapter();
        $db->query($sql);
    }

    public function archiveAllFinished() {
        $sql = "UPDATE $this->_name SET status='".self::STATUS_ARCHIVE."' WHERE end_dt < NOW()";
        $db = $this->getAdapter();
        $db->query($sql);
    }

}


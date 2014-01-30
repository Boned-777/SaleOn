<?php
class Application_Model_DbTable_Order extends Zend_Db_Table_Abstract
{
    protected $_name = 'orders';

    public function save($data, $id) {
        try {
            if (!empty($id)) {
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

    public function getByAd($id)
    {
        try {
            $select = $this->select()
                ->where("ad = ? AND status NOT IN ('" . implode("','", array(
                    Application_Model_Order::STATUS_CANCELED,
                    Application_Model_Order::STATUS_FAILED,
                    Application_Model_Order::STATUS_PAID,
                    Application_Model_Order::STATUS_WAIT_SECURE
                )) . "')", $id)
                ->order("created_dt DESC");
            $data = $this->fetchAll($select);
            if (!$data->count())
                return false;
            $data=$data->toArray();
            return $data[0];
        } catch (Exception $e) {
            return false;
        }

        $res = $res->getRow(0)->toArray();
        return $res;
    }

}


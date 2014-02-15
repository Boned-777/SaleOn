<?php

class Application_Model_DbTable_Product extends Zend_Db_Table_Abstract
{

    protected $_name = 'products';

    public function autocompleteSearch($condition) {
        $select = $this->select(array("name", "id"))
            ->where('name LIKE ? ', $condition . '%')
            ->limit(15);

        $res = $this->fetchAll($select)->toArray();
        $result = array();

        foreach ($res as $value) {
            $result[] = array(
                "label" => $value["name"],
                "value" => $value["id"]
            );
        }
        return $result;
    }

    public function search($condition) {
        $dbItem = new Application_Model_DbTable_Category();
        $select = $this->select(array("name", "id"))
            ->where('name LIKE ? ', $condition . '%');

        $res = $this->fetchAll($select)->toArray();
        $result = array();

        $db = $dbItem->getAdapter();
        $query = $db->query("SELECT product, COUNT(*) count FROM ads WHERE end_dt >= NOW() AND public_dt <= NOW() AND status = 'ACTIVE' GROUP BY product");
        $data = $query->execute();

        $countsList = array();
        foreach ($query->fetchAll() as $countVal) {
            $countsList[$countVal["product"]] = $countVal["count"];
        }

        foreach ($res as $value) {
            $result[] = array(
                "name" => $value["name"],
                "value" => $value["id"],
                "count" => isset($countsList[$value["id"]])?$countsList[$value["id"]]:0
            );
        }
        return $result;
    }

    public function save($data, $id=null) {
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

    public function getNameById($id) {
        if (empty($id))
            return false;
        $select = $this->select(array("name", "id"))
            ->where('id = ? ', $id)
            ->limit(1);
        $res = $this->fetchAll($select)->toArray();
        if (isset($res[0]))
            return $res[0]["name"];
        else
            return false;
    }
}


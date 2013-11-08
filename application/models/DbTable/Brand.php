<?php

class Application_Model_DbTable_Brand extends Zend_Db_Table_Abstract
{

    protected $_name = 'brands';

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


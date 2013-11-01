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
                "value" => $value["name"] . " (" . $value["id"] . ")"
            );
        }
        return $result;
    }
}


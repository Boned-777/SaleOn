<?php

class Application_Model_DbTable_Category extends Zend_Db_Table_Abstract
{

    protected $_name = 'categories';

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


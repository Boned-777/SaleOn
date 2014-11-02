<?php
class Application_Model_Product extends Application_Model_FilterMapper
{
    function __construct() {
        $this->dbItem = new Application_Model_DbTable_Brand();
    }

    public function getByAlias ($alias = "")
    {
        $aliasParts = explode("_", $alias);
        $id = (int)$aliasParts[0];
        if ($id) {
            $res = $this->dbItem->find($id);
            if ($res->count()) {
                $this->loadData($res->current());
                return $this->id;
            }
        }
        return false;
    }
}

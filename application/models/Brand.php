<?php
class Application_Model_Brand extends Application_Model_FilterMapper
{
    const ACTIVE = "ACTIVE";
    const INACTIVE = "INACTIVE";
    const NEW_BRAND = "NEW_BRAND";

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

<<<<<<< HEAD
    public function getByName($name = "")
=======
    public function getByName ($name = "")
>>>>>>> e2ca5030a331087dc7e2c5665ac522d291068c9a
    {
        $select = $this->dbItem->select()
            ->where("name = ?", $name);
        $res = $this->dbItem->fetchRow($select);

        if ($res) {
            $this->loadData($res);
            return $res->id;
        }

        return false;
    }

    public function create($data) {
        $existingItem = $this->getByName($data["brand_name"]);
        if ($existingItem !== false) {
            return $existingItem;
        }

        $item = $this->dbItem->createRow();
        $item->name = $data["brand_name"];
        $item->status = self::NEW_BRAND;
        $item->description = $data["description"];

        try {
            $id = $item->save();
        } catch (Exception $e) {
            return false;
        }

        return $id;
    }
}

<?php

class Application_Model_Category
{
    public $id;
    public $name_uk;
    public $name_ru;
    public $name_en;

    public $name;

    public function getAll($lang="uk") {
        $dbItem = new Application_Model_DbTable_Category();
        $res = $dbItem->fetchAll();

        $itemsArr = $res->toArray();

        $resArr = array();
        foreach ($itemsArr as $value) {
            $resArr[$value["id"]] = $value["name_" . $lang];
        }

        return $resArr;
    }
}


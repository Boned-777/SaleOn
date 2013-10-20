<?php

class Application_Model_Geo
{
    public $id;
    public $code;
    public $name_uk;
    public $name_ru;
    public $name_en;

    public $name;

    public function getAll($pattern = "%", $lang="uk") {
        $dbItem = new Application_Model_DbTable_Geo();
        $res = $dbItem->fetchAll('code LIKE "' . $pattern . '"');

        $itemsArr = $res->toArray();

        $resArr = array();
        foreach ($itemsArr as $value) {
            $resArr[$value["code"]] = $value["name_" . $lang];
        }

        return $resArr;
    }
}


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

    public function listAll() {
        global $translate;

        $dbItem = new Application_Model_DbTable_Category();
        $res = $dbItem->fetchAll();
        $itemsArr = $res->toArray();

        $resArray = array();
        foreach($itemsArr as $item) {
            if ($item["parent"] == 0) {
                $resArray[$item["id"]]["name"] = $translate->getAdapter()->translate($item["name"]);
            } else {
                $tmp = array(
                    "id" => $item["id"],
                    "name" => $translate->getAdapter()->translate($item["name"])
                );
                $resArray[$item["parent"]]["sub"][] = $tmp;
            }
        }
        return $resArray;
    }

}


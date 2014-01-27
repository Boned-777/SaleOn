<?php

class Application_Model_Category
{
    public $id;
    public $name_uk;
    public $name_ru;
    public $name_en;

    public $name;

    public function getAll() {
        global $translate;

        $dbItem = new Application_Model_DbTable_Category();
        $res = $dbItem->fetchAll();
        $itemsArr = $res->toArray();

        $resArray = array();
        foreach($itemsArr as $item) {
            if ($item["parent"] == 0) {
                $resArray[$item["id"]]["name"] = $translate->getAdapter()->translate($item["name"]);
            } else {
                $resArray[$item["parent"]]["sub"][$item["id"]] = $translate->getAdapter()->translate($item["name"]);
            }
        }

        $result = array(
            $resArray[1]["name"] => $resArray[1]["sub"],
            $resArray[2]["name"] => $resArray[2]["sub"],
        );

        return $result;
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


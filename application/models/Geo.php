<?php

class Application_Model_Geo
{
    public $id;
    public $code;
    public $name_uk;
    public $name_ru;
    public $name_en;

    public $name;

    public function getAll($pattern = "") {
        global $translate;
        $dbItem = new Application_Model_DbTable_Geo();
        $originalPattern = $pattern;
        if ($pattern !== "")
            $pattern .= ".";
        $res = $dbItem->fetchAll('code LIKE "' . $pattern . '_" OR code LIKE "' . $pattern . '__"');

        $itemsArr = $res->toArray();

        $resArr = array();
        if ($originalPattern !== "")
            $resArr[$originalPattern . ".0"] = $translate->getAdapter()->translate("any");
        foreach ($itemsArr as $value) {
            $resArr[$value["code"]] = $translate->getAdapter()->translate($value["name"]);
        }

        return $resArr;
    }

    public function getAllChild($pattern = "") {
        global $translate;
        $dbItem = new Application_Model_DbTable_Geo();
        $originalPattern = $pattern;
        if ($pattern !== "")
            $pattern .= ".";
        $res = $dbItem->fetchAll('code LIKE "' . $pattern . '_" OR code LIKE "' . $pattern . '__"');

        $itemsArr = $res->toArray();

        $resArr = array(array(
            "value" => $originalPattern,
            "option" => $translate->getAdapter()->translate("any")
        ));
        foreach ($itemsArr as $value) {
            $resArr[] = array(
                "value" => $value["code"],
                "option" => $translate->getAdapter()->translate($value["name"])
            );
        }

        return $resArr;
    }

    public function getAllChildList($pattern = "", $lang="uk") {
        global $translate;
        $dbItem = new Application_Model_DbTable_Geo();
        $originalPattern = $pattern;
        if ($pattern !== "")
            $pattern .= ".";
        $res = $dbItem->fetchAll('code LIKE "' . $pattern . '_" OR code LIKE "' . $pattern . '__"');

        $itemsArr = $res->toArray();

        $resArr = array(array(
            "name" => $originalPattern,
            "value" => $translate->getAdapter()->translate("any"),
            "is_path" => 0
        ));
        $is_path = 0;
        if (sizeof(explode(".", $pattern)) == 2)
            $is_path = 1;
        foreach ($itemsArr as $value) {
            $resArr[] = array(
                "name" => $value["code"],
                "value" => $value["name_" . $lang],
                "is_path" => $is_path
            );
        }

        return $resArr;
    }

    public function getFullGeoName ($geoCode = "") {
        global $translate;
        $indexes = explode(".", $geoCode);

        $condList = array();
        $tmp = "";
        foreach ($indexes as $val) {
            $condList[] = $tmp . $val;
            $tmp .= $val . ".";
        }

        $dbItem = new Application_Model_DbTable_Geo();
        $res = $dbItem->fetchAll('code IN ("' . implode ('","', $condList) . '")')->toArray();

        $result = array();
        foreach ($res as $value) {
            $result[] = $value["name_uk"];
        }
        return implode(", ", $result);

    }
}


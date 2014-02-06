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

    public function getAllChildList($pattern = "") {
        global $translate;
        $countsList = $this->_getCounts($pattern);
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
                "value" => $translate->getAdapter()->translate($value["name"]),
                "count" => isset($countsList[$value["code"]])?$countsList[$value["code"]]:0,
                "is_path" => $is_path
            );
        }

        //sorting
        $name = array();
        foreach ($resArr as $key => $row) {
//            if ($row["id"] == 15) {
//                $tmpItem = $resArray[1]["sub"][$key];
//                unset($resArray[1]["sub"][$key]);
//            } else
                $name[$key]  = $row['name'];
        }
        array_multisort($name, SORT_ASC, $resArr);
        return $resArr;
    }

    protected function _getCounts($temp = "") {
        $temp = $temp?$temp:"";
        $symCount = 2*strlen($temp) + 2;
        $ad = new Application_Model_DbTable_Ad();
        $select = $ad->select();
        $select->from("ads", array(
            new Zend_Db_Expr("geo"),
            new Zend_Db_Expr("COUNT(*) count"),
            new Zend_Db_Expr('SUBSTRING(REPLACE(LEFT(geo, '.$symCount .'),".",""), '.(strlen($temp)+1).') et')
        ));
        if ($temp !== "")
            $select->where('geo LIKE "'.$temp.'" OR geo LIKE "'.$temp.'.%"');
        else
            $select->where('geo LIKE "" OR geo LIKE "%"');
        $select->group("et");
        $data = $ad->fetchAll($select)->toArray();
        $resData = array();
        foreach($data as $val) {
            if ($temp !== "")
                $resData[$temp.".".$val["et"]] = $val["count"];
            else
                $resData[$val["et"]] = $val["count"];
        }
        return $resData;
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
            $result[] = $translate->getAdapter()->translate($value["name"]);
        }
        return implode(", ", $result);

    }
}


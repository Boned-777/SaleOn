<?php

class Application_Model_Geo
{
    public $id;
    public $code;
    public $name;

    public function getAll($pattern = "") {
        global $translate;
        $dbItem = new Application_Model_DbTable_Geo();
        $originalPattern = $pattern;
        if ($pattern !== "")
            $pattern .= "-";
        $res = $dbItem->fetchAll('code LIKE "' . $pattern . '_" OR code LIKE "' . $pattern . '__"');

        $itemsArr = $res->toArray();

        $resArr = array();
        if ($originalPattern !== "") {
            $resArr[$originalPattern . "-0"] = $translate->getAdapter()->translate("any");
        }
        foreach ($itemsArr as $value) {
            if ($value["code"] == $originalPattern . "-99") {
                $resArr[$value["code"]] = $translate->getAdapter()->translate($value["name"]);
            }
        }

        foreach ($itemsArr as $value) {
            if ($value["code"] != $originalPattern . "-99") {
                $resArr[$value["code"]] = $translate->getAdapter()->translate($value["name"]);
            }
        }

        return $resArr;
    }

    public function getAllChild($pattern = "") {
        global $translate;
        $dbItem = new Application_Model_DbTable_Geo();
        $originalPattern = $pattern;
        if ($pattern !== "")
            $pattern .= "-";
        $res = $dbItem->fetchAll('code LIKE "' . $pattern . '_" OR code LIKE "' . $pattern . '__"');

        $itemsArr = $res->toArray();

        $resArr = array(array(
            "value" => $originalPattern,
            "option" => $translate->getAdapter()->translate("any")
        ));

        foreach ($itemsArr as $value) {
            if ($value["code"] == $originalPattern . "-99") {
                $resArr[] = array(
                    "value" => $value["code"],
                    "option" => $translate->getAdapter()->translate($value["name"])
                );
            }
        }

        foreach ($itemsArr as $value) {
            if ($value["code"] != $originalPattern . "-99") {
                $resArr[] = array(
                    "value" => $value["code"],
                    "option" => $translate->getAdapter()->translate($value["name"])
                );
            }
        }
        return $resArr;
    }

    public function getAllChildList($pattern = "", $params = null) {
        global $translate;
        $countsList = $this->_getCounts($pattern, $params);
        $dbItem = new Application_Model_DbTable_Geo();
        $originalPattern = $pattern;
        if ($pattern !== "")
            $pattern .= "-";
        $res = $dbItem->fetchAll('code LIKE "' . $pattern . '_" OR code LIKE "' . $pattern . '__"');
        $itemsArr = $res->toArray();
        $resArr = array();
        $cityArray = null;
        $is_path = 0;
        if (sizeof(explode("-", $pattern)) == 2)
            $is_path = 1;
        foreach ($itemsArr as $value) {
            if (!preg_match("/^[0-9]{1,2}-[0-9]{1,2}-99/", $value["code"])) {
                $resArr[] = array(
                    "name" => $value["code"],
                    "value" => str_replace("І", "ИИ", $translate->getAdapter()->translate($value["name"])),
                    "count" => isset($countsList[$value["code"]])?$countsList[$value["code"]]:0,
                    "is_path" => $is_path
                );

            } else
                $cityArray = array(
                    "name" => $value["code"],
                    "value" => $translate->getAdapter()->translate($value["name"]),
                    "count" => isset($countsList[$value["code"]])?$countsList[$value["code"]]:0,
                    "is_path" => $is_path
                );
        }
        //sorting
        $name = array();
        foreach ($resArr as $key => $row) {
            $name[$key]  = $row['value'];
        }
        array_multisort($name, SORT_ASC, $resArr);
        foreach ($resArr as $key => $value) {
            $resArr[$key]["value"] = str_replace("ИИ", "І", $resArr[$key]["value"]);
        }
        $additional = array(array(
            "name" => $originalPattern,
            "value" => $translate->getAdapter()->translate("any"),
            "count" => isset($countsList[$originalPattern])?$countsList[$originalPattern]:0,
            "is_path" => 0
        ));
        if ($cityArray)
            $additional[] = $cityArray;
        $resArr = array_merge($additional, $resArr);
        return $resArr;
    }

    protected function _getCounts($temp = "", $params = null) {
        $temp = $temp?$temp:"";
        $symCount = 2*strlen($temp) + 2;
        $ad = new Application_Model_DbTable_Ad();
        $select = $ad->select();
        $select->from("ads", array(
            new Zend_Db_Expr("geo"),
            new Zend_Db_Expr("COUNT(*) count"),
            new Zend_Db_Expr('REPLACE(LEFT(REPLACE(CONCAT(">", geo), ">'.$temp.'-", ""), 2), "-","") et')
        ));
        if ($temp !== "")
            $select->where('geo LIKE "'.$temp.'" OR geo LIKE "'.$temp.'-%"');
        else
            $select->where('geo LIKE "" OR geo LIKE "%"');
        $select->where("end_dt >= NOW() AND public_dt <= NOW() AND status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE);
        $select->group("et");

        if (!is_null($params)) {
            foreach ($params as $key => $val) {
                switch ($key) {
                    case "geo" :
                        $select->where("(geo LIKE '$val' OR geo LIKE '$val-%')");
                        break;

                    default :
                        $select->where("$key = ?", $val);
                        break;
                }
            }
        }

        $data = $ad->fetchAll($select)->toArray();
        $resData = array();
        foreach($data as $val) {
            if (!empty($temp))
                if ($val["et"])
                    $resData[$temp."-".$val["et"]] = $val["count"];
                else
                    $resData[$temp] = $val["count"];
            else
                $resData[$val["et"]] = $val["count"];
        }
        return $resData;
    }

    public function getFullGeoName ($geoCode = "") {
        global $translate;
        $indexes = explode("-", $geoCode);
        $condList = array();
        $tmp = "";
        foreach ($indexes as $val) {
            $condList[] = $tmp . $val;
            $tmp .= $val . "-";
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


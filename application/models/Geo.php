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

    public function getAllTree($adId = null) {
        $db = new Application_Model_DbTable_Geo();
        $dbItems = $db->fetchAll(null, array("code", "name"));

        $currentLocations = array();
        if(!is_null($adId)) {
            $locations = new Application_Model_AdLocationCollection();
            $locations->getByAdId($adId);
            $currentLocations = $locations->getLocationsList();
        }

        $data = array();
        foreach ($dbItems as $item) {
            $map = explode("-", $item->code);
            $isChecked = false;
            if (in_array($item->code, $currentLocations)) {
                $isChecked = true;
            }
            switch (sizeof($map)) {
                case 1 :
                    $data[$map[0]] = $item->toArray($isChecked);
                    $data[$map[0]]["open"] = true;
                    break;

                case 2 :
                    $data[$map[0]]["branch"][$map[1]] = $item->toArray($isChecked ? $isChecked : $data[$map[0]]["checked"]);
                    $data[$map[0]]["inode"] = true;
                    break;

                case 3 :
                    $data[$map[0]]["branch"][$map[1]]["branch"][] = $item->toArray($isChecked ? $isChecked : $data[$map[0]]["branch"][$map[1]]["checked"]);
                    $data[$map[0]]["branch"][$map[1]]["inode"] = true;
                    break;
            }
        }

        foreach ($data as $key => $items) {
            if (sizeof($data[$key]["branch"] )) {
                $data[$key]["branch"] = array_values($data[$key]["branch"]);
            }
        }

        $data = array_values($data);

        return $data;
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
        $allCount = isset($countsList[$originalPattern]) ? $countsList[$originalPattern] : 0;
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
            $currentCount = isset($countsList[$value["code"]])?$countsList[$value["code"]]:$allCount;
            if (!preg_match("/^[0-9]{1,2}-[0-9]{1,2}-99/", $value["code"])) {
                $resArr[] = array(
                    "name" => $value["code"],
                    "value" => str_replace("І", "ИИ", $translate->getAdapter()->translate($value["name"])),
                    "count" => $currentCount,
                    "is_path" => $is_path
                );
            } else {
                $cityArray = array(
                    "name" => $value["code"],
                    "value" => $translate->getAdapter()->translate($value["name"]),
                    "count" => $currentCount,
                    "is_path" => $is_path
                );
            }
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
            "count" => $this->_getAllCounts($originalPattern, $params),
            "is_path" => 0
        ));
        if ($cityArray)
            $additional[] = $cityArray;
        $resArr = array_merge($additional, $resArr);

        return $resArr;
    }

    protected function _getCounts($temp = "", $params = null) {
        $temp = $temp?$temp:"";
        $mainId = explode("-", $temp);
        $mainId = $mainId[0];
        $ad = new Application_Model_DbTable_AdLocation();
        $select = $ad->select();
        $select->setIntegrityCheck(false);

        $columns = array(
            new Zend_Db_Expr("a.id"),
            new Zend_Db_Expr('REPLACE(LEFT(REPLACE(CONCAT(">", al.location), ">'.$temp.'-", ""), 2), "-","") et')
        );

        if ($temp == "1") {
            $columns[] = new Zend_Db_Expr("LEFT(al.location, 4) location");
        } else {
            $columns[] = new Zend_Db_Expr("al.location");
        }

        $select->from(array("al" => "AdLocation"));
        $select->distinct();


        $geoStmt = "";
        if ($temp !== "") {
            $geoStmt .= 'al.location LIKE "'.$temp.'" OR al.location LIKE "'.$temp.'-%"';
        } else {
            $geoStmt .= 'al.location LIKE "" OR al.location LIKE "%"';
        }
        if ($temp != $mainId) {
            $geoStmt .= " OR al.location LIKE '$mainId'";
        }
        $select->join(array("a" => "ads"), "a.id = al.ad_id");
        $select->where($geoStmt);
        $select->where("(a.end_dt >= NOW() - INTERVAL 1 DAY) AND a.public_dt <= NOW() AND a.status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE);

        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns($columns);
        $select2 = $ad->select()->from(
            array(
                'd' => new Zend_Db_Expr('(' . (string) $select . ')') //
            ), array(
                new Zend_Db_Expr("COUNT(d.location) count"),
                new Zend_Db_Expr("d.*")
            )
        )
        ->group("d.location");
        $select2->setIntegrityCheck(false);

        $data = $ad->fetchAll($select2)->toArray();
        $resData = array();
        $resData[$temp] = 0;

        foreach($data as $val) {
            if (!empty($temp))
                if ($val["et"]){
                    if (strstr($val["et"], ">")) {
                        $resData[$temp] += $val["count"];
                    } else {
                        $resData[$temp."-".$val["et"]] = $val["count"] + $resData[$temp];
                    }
                } else {
                    $resData[$temp] = $val["count"] + $resData[$temp];
                }
            else
                $resData[$val["et"]] = $val["count"] + $resData[$temp];
        }
        return $resData;
    }

    protected function _getAllCounts($temp = "", $params = null) {
        $temp = $temp?$temp:"";


        $ad = new Application_Model_DbTable_AdLocation();
        $select = $ad->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(array("al" => "AdLocation"), array(
            new Zend_Db_Expr("a.id")
        ));

        $geoStmt = Application_Model_DbTable_AdLocation::prepareWhereStatement($temp, "al");

        $select->join(array("a" => "ads"), "a.id = al.ad_id");
        $select->where($geoStmt);
        $select->where("(a.end_dt >= NOW() - INTERVAL 1 DAY) AND a.public_dt <= NOW() AND a.status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE);

        $res = $ad->fetchAll($select);
        return $res->count();
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

    public function addGeoItem ($parentCode, $internationalName, $nativeName) {
        $res = array(
            "success" => true,
            "msg" => null
        );

        if (!$internationalName || !$nativeName || !$parentCode) {
            $res["success"] = false;
            $res["msg"] = "incorrect data";
        } else {
            $name = strtolower(urlencode(preg_replace("/[^a-zа-я\s]/ui",'',$internationalName)));
            $db = new Application_Model_DbTable_Geo();
            $item = $db->createRow();
            $item->name = $name;
            $item->code = $this->getNextCode($parentCode);
            try {
                $id = $item->save();
                $res["msg"] = $id;
            } catch (Exception $e) {
                $res["success"] = false;
                $res["msg"] = $e->getMessage();
            }
        }
        return $res;
    }

    protected function getNextCode($parentCode) {
        $db = new Application_Model_DbTable_Geo();
        $select = $db->select()
            ->from("geo", array("lastNumber" => new Zend_Db_Expr("REPLACE(code, '$parentCode-', '') + 0")))
            ->where("code LIKE '$parentCode-_' OR code LIKE '$parentCode-__'")
            ->having("lastNumber <> 99")
            ->order("lastNumber DESC");
        $item = $db->fetchRow($select);
        if ($item) {
            $nextId = $item->lastNumber + 1;
        } else {
            $nextId = 1;
        }
        return $parentCode."-".$nextId;
    }

    public function removeGeoItem($geoCode) {
        if (is_null($geoCode)) {
            $res = array(
                "success" => false,
                "msg" => "code is null"
            );
            return $res;
        }
        $res = array(
            "success" => true,
            "msg" => null
        );

        $db = new Application_Model_DbTable_Geo();
        $select = $db->select()
            ->from("geo", array("lastNumber" => new Zend_Db_Expr("REPLACE(code, '$parentCode-', '') + 0")))
            ->where("code = ?", $geoCode);
        $item = $db->fetchRow($select);
        if ($item) {
            $item->delete();
        } else {
            $res = array(
                "success" => false,
                "msg" => "code not found"
            );
        }
        return $res;
    }
}


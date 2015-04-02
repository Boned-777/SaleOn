<?php

class Application_Model_Geo
{
    static $NATIVE_NAME = "NATIVE";
    static $INTER_NAME = "US";

    public $id;
    public $code;
    public $name;
    public $locale;

    static function getLocaleName($name) {
        global $locationsNative, $locationsInternational;
        $resultName = $locationsNative->getAdapter()->translate($name);
        if ($resultName === $name) {
            $resultName = $locationsInternational->getAdapter()->translate($name);
        }
        return $resultName ? $resultName : $name;
    }

    function getName() {
        return $this->getLocaleName($this->name);
    }

    public function getAll($pattern = "") {
        global $translate;
        $originalPattern = $pattern;
        $itemsList = $this->getAllItems($pattern);
        $itemsArr = $itemsList->toArray();

        $resArr = array();
        if ($originalPattern !== "") {
            $resArr[$originalPattern . "-0"] = $translate->getAdapter()->translate("any");
        }
        foreach ($itemsArr as $value) {
            if ($value["code"] == $originalPattern . "-99") {
                $resArr[$value["code"]] = $this->getLocaleName($value["name"]);
            }
        }

        foreach ($itemsArr as $value) {
            if ($value["code"] != $originalPattern . "-99") {
                $resArr[$value["code"]] = $this->getLocaleName($value["name"]);
            }
        }

        return $resArr;
    }

    public function getAllItems($pattern = "") {
        $dbItem = new Application_Model_DbTable_Geo();
        if ($pattern !== "")
            $pattern .= "-";

        $statement = null;
        if ($pattern) {
            $statement = 'code LIKE "' . $pattern . '_" OR code LIKE "' . $pattern . '__"';
        }

        $items = $dbItem->fetchAll($statement);
        return $items;
    }

    public function getAllChildren($pattern = "") {
        $dbItem = new Application_Model_DbTable_Geo();
        if ($pattern !== "")
            $pattern .= "-";

        $statement = null;
        if ($pattern) {
            $statement = 'code LIKE "' . $pattern . '%"';
        }

        $items = $dbItem->fetchAll($statement);
        return $items;
    }

    public function getByCode($code) {
        if (is_null($code)) {
            return false;
        }
        $db = new Application_Model_DbTable_Geo();
        $select = $db->select()
            ->where("code = ?", (string)$code);
        $item = $db->fetchRow($select);
        if ($item) {
            $this->loadData($item);
            return $item;
        } else {
            return false;
        }
    }

    protected function loadData($item) {
        $this->id = $item->id;
        $this->code = $item->code;
        $this->name = $item->name;
        $this->locale = is_null($item->locale) ? "" : unserialize($item->locale);
    }

    public function getAllTree($adId = null, $editMode = false) {
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
            $a = new Application_Model_GeoDbRow();
            $map = explode("-", $item->code);
            $isChecked = false;
            if (in_array($item->code, $currentLocations)) {
                $isChecked = true;
            }
            switch (sizeof($map)) {
                case 1 :
                    $data[$map[0]] = $item->toArray($isChecked, $editMode);
                    $data[$map[0]]["open"] = true;
                    break;

                case 2 :
                    $data[$map[0]]["branch"][$map[1]] = $item->toArray($isChecked ? $isChecked : $data[$map[0]]["checked"], $editMode);
                    $data[$map[0]]["inode"] = true;
                    break;

                case 3 :
                    $data[$map[0]]["branch"][$map[1]]["branch"][] = $item->toArray($isChecked ? $isChecked : $data[$map[0]]["branch"][$map[1]]["checked"], $editMode);
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
                    "option" => $this->getLocaleName($value["name"])
                );
            }
        }

        foreach ($itemsArr as $value) {
            if ($value["code"] != $originalPattern . "-99") {
                $resArr[] = array(
                    "value" => $value["code"],
                    "option" => $this->getLocaleName($value["name"])
                );
            }
        }
        return $resArr;
    }

    public function getAllChildList($pattern = "", $params = null) {
        global $translate;

        if ($pattern == "") {
            $pattern = "1";
        }

        $countsList = $this->_getCounts($pattern, $params);


        $originalPattern = $pattern;
        $pattern .= "-";
        $dbItem = new Application_Model_DbTable_Geo();

        $allCount = isset($countsList["origin"]) ? $countsList["origin"] : 0;

        $res = $dbItem->fetchAll('code LIKE "' . $pattern . '_" OR code LIKE "' . $pattern . '__"');
        $itemsArr = $res->toArray();

        $resArr = array();
        $cityArray = null;
        $is_path = 0;
        if (sizeof(explode("-", $pattern)) == 2)
            $is_path = 1;
        foreach ($itemsArr as $value) {
            $currentCount = (isset($countsList[$value["code"]]) ? $countsList[$value["code"]] : 0) + $allCount;
            if (!preg_match("/^[0-9]{1,2}-[0-9]{1,2}-99/", $value["code"])) {
                $resArr[] = array(
                    "name" => $value["code"],
                    "value" => str_replace("І", "ИИ", $this->getLocaleName($value["name"])),
                    "seo_name" => $value["name"],
                    "count" => $currentCount,
                    "is_path" => $is_path
                );
            } else {
                $cityArray = array(
                    "name" => $value["code"],
                    "value" => $this->getLocaleName($value["name"]),
                    "seo_name" => $value["name"],
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

        $geoOriginal = new Application_Model_Geo();
        $geoOriginal->getByCode($originalPattern);
        $additional = array(array(
            "name" => $originalPattern,
            "seo_name" => $geoOriginal->name,
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
        $temp = $temp?$temp:"1";
        $params["geo"] = $temp;
        $adSolr = new Application_Model_AdSolr();
        $data = $adSolr->getFacets("geo", $params);
        return $data;
    }

    protected function getGeoIndex($index, $value) {
        $geoCodeParts = explode("-", $value);
        for($i=$index+1; $i <= count($geoCodeParts); $i++) {
            unset($geoCodeParts[$i]);
        }
        return implode("-", $geoCodeParts);
    }

    protected function _getAllCounts($temp = "", $params = null) {
        $adSolr = new Application_Model_AdSolr();
        $res = $adSolr->getAdsCount($temp, $params);
        return $res;
    }

    public function getFullGeoName ($geoCode = "") {
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
            $result[] = $this->getLocaleName($value["name"]);
        }
        return implode(", ", $result);
    }

    public function getByAlias ($alias = "") {
        $dbItem = new Application_Model_DbTable_Geo();
        $res = $dbItem->fetchRow("name = '$alias'");
        if ($res) {
            $this->loadData($res);
            return $res->code;
        }
        return false;
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
//            $parentItem = $this->getByCode($parentCode);
            $db = new Application_Model_DbTable_Geo();
            $item = $db->createRow();
            $this->processItem($item, $nativeName, $internationalName);
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

    public function editGeoItem ($currentCode, $internationalName, $nativeName) {
        $res = array(
            "success" => true,
            "msg" => null
        );

        if (!$internationalName || !$nativeName || !$currentCode) {
            $res["success"] = false;
            $res["msg"] = "incorrect data";
        } else {
            $item = $this->getByCode($currentCode);
            $this->processItem($item, $nativeName, $internationalName);
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

    protected function processItem(&$dbItem, $nativeName, $interName) {
        $name = strtolower(urlencode(preg_replace("/[^a-zа-я\s]/ui",'',$interName)));
        $dbItem->name = $name;
        $locale = array(
            Application_Model_Geo::$INTER_NAME => $interName,
            Application_Model_Geo::$NATIVE_NAME => $nativeName
        );
        $dbItem->locale = serialize($locale);
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

        $item = $this->getByCode($geoCode);
        if ($item) {
            $item->delete();
            $this->removeChildren($geoCode);
        } else {
            $res = array(
                "success" => false,
                "msg" => "code not found"
            );
        }
        return $res;
    }

    protected function removeChildren($code) {
        $db = new Application_Model_DbTable_Geo();
        $sql = "DELETE FROM `geo` WHERE `code` LIKE '" . $code ."-%'";
        $stmt = new Zend_Db_Statement_Mysqli($db->getAdapter(), $sql);
        $stmt->execute();
    }

    public function createLocaleFile($countryCode) {
        $countryItem = new Application_Model_Geo();
        $countryItem->getByCode($countryCode);

        $fileName = "location_" . strtoupper(substr($countryItem->locale[Application_Model_Geo::$INTER_NAME], 0, 3)) . ".php";

        $subItems = $countryItem->getAllChildren($countryCode);

        $file = fopen(APPLICATION_PATH . "/locale/" . $fileName, "w");
        fwrite($file, "<?php \nreturn array(\n");

        $realName = $countryItem->locale[Application_Model_Geo::$NATIVE_NAME];
        fwrite($file, "    \"$countryItem->name\" => \"$realName\",\n");

        foreach($subItems as $item) {
            $realName = unserialize($item->locale);
            $realName = $realName[Application_Model_Geo::$NATIVE_NAME];
            fwrite($file, "    \"$item->name\" => \"$realName\",\n");
        }
        fwrite($file, ");");
    }

    public function createInterLocaleFile() {
        $countryItem = new Application_Model_Geo();
        $fileName = "location_" . Application_Model_Geo::$INTER_NAME . ".php";

        $subItems = $countryItem->getAllItems();

        $file = fopen(APPLICATION_PATH . "/locale/" . $fileName, "w");
        fwrite($file, "<?php \nreturn array(\n");
        foreach($subItems as $item) {
            $realName = "";
            if ($item->locale) {
                $realName = unserialize($item->locale);
                $realName = $realName[Application_Model_Geo::$INTER_NAME];
            }
            fwrite($file, "    \"$item->name\" => \"$realName\",\n");
        }
        fwrite($file, ");");
    }

    public function getCountries() {
        $db = new Application_Model_DbTable_Geo();
        $select = $db->select()
            ->where('code LIKE "_" OR code LIKE "__"');
        $result = $db->fetchAll($select);
        $result->toArray();
        $data = array();
        foreach ($result as $item) {
            $tmp = array(
                "code" => $item->code,
                "name" => self::getLocaleName($item->name),
                "seo_name" => $item->name
            );
            $data[] = $tmp;
        }
        return $data;
    }
}


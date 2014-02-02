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

        $db = $dbItem->getAdapter();
        $query = $db->query("SELECT category, COUNT(*) count FROM ads GROUP BY category");
        $data = $query->execute();

        $countsList = array();
        foreach ($query->fetchAll() as $countVal) {
            $countsList[$countVal["category"]] = $countVal["count"];
        }
        foreach($itemsArr as $item) {
            if ($item["parent"] == 0) {
                $resArray[$item["id"]]["name"] = $translate->getAdapter()->translate($item["name"]);
            } else {
                $tmp = array(
                    "id" => $item["id"],
                    "name" => $translate->getAdapter()->translate($item["name"]),
                    "count" => isset($countsList[$item["id"]])?$countsList[$item["id"]]:0
                );
                $resArray[$item["parent"]]["sub"][] = $tmp;
            }
        }
        //sorting
        $name1 = array();
        $name2 = array();
        foreach ($resArray[1]["sub"] as $key => $row) {
            if ($row["id"] == 15) {
                $tmpItem = $resArray[1]["sub"][$key];
                unset($resArray[1]["sub"][$key]);
            } else
                $name1[$key]  = $row['name'];
        }
        array_multisort($name1, SORT_ASC, $resArray[1]["sub"]);
        $resArray[1]["sub"][] = $tmpItem;

        foreach ($resArray[2]["sub"] as $key => $row) {
            if ($row["id"] == 28) {
                $tmpItem = $resArray[2]["sub"][$key];
                unset($resArray[2]["sub"][$key]);
            } else
                $name2[$key]  = $row['name'];
        }
        array_multisort($name2, SORT_ASC, $resArray[2]["sub"]);
        $resArray[2]["sub"][] = $tmpItem;

        return $resArray;
    }
}

<?php
class Application_Model_FilterMapper
{
    public $id;
    public $name;
    
    protected $dbItem;
    
    function __construct() {
        $this->dbItem = new Application_Model_DbTable_Category();
    }

    public function getAll() {
        global $translate;
        $res = $this->dbItem->fetchAll();
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

    public function getName() {
        return $this->name;
    }

    protected function getCounts($db, $colName, $params=null) {
        $adSolr = new Application_Model_AdSolr();
        $countsList = $adSolr->getFacets($colName, $params);

        return $countsList;
    }

    public function listAll($params=null) {
        global $translate;
        $res = $this->dbItem->fetchAll();
        $itemsArr = $res->toArray();
        $resArray = array();

        $db = $this->dbItem->getAdapter();
        $countsList = $this->getCounts($db, "category", $params);
        foreach ($countsList as $countVal) {
            $countsList[$countVal["category"]] = $countVal["count"];
        }
        foreach($itemsArr as $item) {
            if ($item["parent"] == 0) {
                $resArray[$item["id"]]["name"] = $translate->getAdapter()->translate($item["name"]);
                $resArray[$item["id"]]["seo_name"] = $item["name"];
            } else {
                $tmp = array(
                    "id" => $item["id"],
                    "name" => $translate->getAdapter()->translate($item["name"]),
                    "seo_name" => $item["name"],
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

    public function getByAlias ($alias = "") {
        $res = $this->dbItem->fetchRow("name = '$alias'");
        if ($res) {
            $this->loadData($res);
            return $res->id;
        }
        return false;
    }

    /**
     * Load DB Row data to current object
     *
     * @param $item
     * @return bool
     */
    public function loadData($item) {
        global $translate;
        $this->id = $item->id;
        $this->name = $item->name;
        $this->locale = array(
            $translate->getAdapter()->translate($item->name)
        );
        return true;
    }
}

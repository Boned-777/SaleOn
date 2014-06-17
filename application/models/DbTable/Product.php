<?php

class Application_Model_DbTable_Product extends Zend_Db_Table_Abstract
{

    protected $_name = 'products';

    public function autocompleteSearch($condition) {
        $select = $this->select(array("name", "id"))
            ->where('name LIKE ? ', $condition . '%')
            ->limit(15);

        $res = $this->fetchAll($select)->toArray();
        $result = array();

        foreach ($res as $value) {
            $result[] = array(
                "label" => $value["name"],
                "value" => $value["id"]
            );
        }
        return $result;
    }

    public function search($condition, $params=null) {
        $dbItem = new Application_Model_DbTable_Category();
        $select = $this->select(array("name", "id"))
            ->where('name LIKE ? ', $condition . '%');

        $res = $this->fetchAll($select)->toArray();

        $db = $dbItem->getAdapter();
        $countsList = $this->getCounts($db, "product", $params);
        $result = array();
        foreach ($res as $value) {
            if (isset($countsList[$value["id"]])) {
                $result[] = array(
                    "name" => $value["name"],
                    "value" => $value["id"],
                    "count" => isset($countsList[$value["id"]])?$countsList[$value["id"]]:0
                );
            }
        }
        return $result;
    }

    protected function getCounts($db, $colName, $params=null) {
        $select = $db->select();
        $select->from(array("a"=>"ads"), array(
            new Zend_Db_Expr("a.".$colName),
            new Zend_Db_Expr("COUNT(a.$colName) count")
        ));
        $select->where("a.end_dt >= NOW() AND a.public_dt <= NOW() AND a.status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE);
        if (!is_null($params)) {
            foreach ($params as $key => $val) {
                switch ($key) {
                    case "geo" :
                        $select->join(array("al" => "AdLocation"), "a.id = al.ad_id");
                        $geoWhere = "al.location LIKE '$val' OR al.location LIKE '$val-%'";
                        $mainId = explode("-", $val);
                        if (count($mainId) > 1) {
                            $geoWhere .= " OR al.location LIKE '$mainId[0]'";
                            if (isset($mainId[1])) {
                                $geoWhere .= " OR al.location LIKE '$mainId[0]-$mainId[1]-0'";
                            }
                        }
                        $select->where("(" . $geoWhere . ")");
                        break;

                    default :
                        $select->where("a.$key = ?", $val);
                        break;
                }
            }
        }
        $select->group("a.".$colName);
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        $countsList = array();
        foreach ($data as $countVal) {
            $countsList[$countVal[$colName]] = $countVal["count"];
        }

        return $countsList;
    }

    public function save($data, $id=null) {
        try {
            if (!empty($id)) {
                $res = $this->update($data, 'id = '. (int)$id);
                $res = $id;
            } else {
                if (isset($data["id"]))
                    unset($data["id"]);
                $res = $this->insert($data);
            }
            return $res;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public function getNameById($id) {
        if (empty($id))
            return false;
        $select = $this->select(array("name", "id"))
            ->where('id = ? ', $id)
            ->limit(1);
        $res = $this->fetchAll($select)->toArray();
        if (isset($res[0]))
            return $res[0]["name"];
        else
            return false;
    }
}


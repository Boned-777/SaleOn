<?php
class Application_Model_Brand {
    var $id;
    var $name;
    var $partner;
    var $status;

    var $_dbItem;

    const ACTIVE = "ACTIVE";
    const INACTIVE = "INACTIVE";
    const NEW_BRAND = "NEW_BRAND";

    public function get($id) {
        $db = new Application_Model_DbTable_Brand();
        $this->_dbItem = $db->find($id);
        if ($this->_dbItem) {
            $resArr = $this->_dbItem->toArray();
            $this->loadData($resArr);
            return $resArr;
        }
        return false;
    }

    public function getByPartnerId($id) {
        $db = new Application_Model_DbTable_Brand();
        $select = $db->select()
            ->where("partner = ?", $id)
            ->order("name");
        return $db->fetchAll($select)->toArray();
    }

    public function loadData($data) {
        $fields = array(
            "id",
            "name",
            "partner",
            "status"
        );
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
        return true;
    }

    public function create($brandName, $partnerId) {
        $this->name = $brandName;
        $this->partner = $partnerId;
        $this->status = self::NEW_BRAND;
        if ($this->id = $this->save()) {
            return $this->id;
        }
        return false;
    }

    public function toArray() {
        $fields = array(
            "id",
            "name",
            "partner",
            "status"
        );
        $resArr = array();
        foreach ($fields as $field) {
            if ($this->$field) {
                $resArr[$field] = $this->$field;
            }
        }
        return $resArr;
    }

    public function save() {
        $db = new Application_Model_DbTable_Brand();
        return $db->save($this->toArray());
    }
}

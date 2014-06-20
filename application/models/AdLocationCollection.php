<?php
class Application_Model_AdLocationCollection
{
    public $adId;
    public $locations;
    public $newLocations;
    protected $_makeUpdate = false;

    public function getByAdId($id) {
        $this->adId = $id;
        $dbItem = new Application_Model_DbTable_AdLocation();
        $select = $dbItem->select()
            ->where("ad_id = ?", array($id));
        $this->locations = $dbItem->fetchAll($select);
        return true;
    }

    /**
     * Create locations collection from array
     *
     * @param array $locations
     */
    public function setLocationsList($locations) {
        $this->_makeUpdate = true;
        $this->newLocations = $locations;
    }

    public function clearList() {
        if (!$this->adId) {
            return false;
        }
        $db = new Application_Model_DbTable_AdLocation();
        $sql = "DELETE FROM AdLocation WHERE ad_id = " . $this->adId;
        $db->getAdapter()->query($sql);
        return true;
    }

    public function getLocationsList() {
        $data = array();
        foreach ($this->locations as $row) {
            $data[] = $row->location;
        }
        return $data;
    }

    public function save() {
        if (!$this->_makeUpdate)
            return true;
        $this->clearList();
        $this->locations = array();
        if (!is_null($this->newLocations)) {
            $db = new Application_Model_DbTable_AdLocation();
            foreach ($this->newLocations as $location) {
                $item = $db->createRow();
                $item->ad_id = $this->adId;
                $item->location = $location;
                $item->save();
            }
            $this->newLocations = null;
        }
        $this->getByAdId($this->adId);
    }
}
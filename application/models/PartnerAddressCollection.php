<?php
class Application_Model_PartnerAddressCollection {
    public $list;

    protected $_items;

    /**
     * @param $id
     * @return bool
     */
    public function get() {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        if ($userId) {
            $dbItem = new Application_Model_DbTable_PartnerAddress();
            $select = $dbItem->select()
                ->where("user_id = ?", $userId)
                ->order("name");
            $this->_items = $dbItem->fetchAll($select);
            $this->list = $this->_items->toArray();
        }
        return true;
    }

    /**
     * Creates new DB entry
     *
     * @param int $adId
     * @param string $addressString
     * @return bool
     */
    public function add($adId, $addressString) {
        $item = new Application_Model_DbTable_PartnerAddress();
        $raw = $item->createRow();
        $raw->adId = $adId;
        $raw->name = $addressString;
        $result = $raw->save();
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * @param int $adAddressId
     * @return bool
     */
    public function remove($adAddressId) {
        $item = new Application_Model_DbTable_AdAddress();
        $raw = $item->fetchAll("ad_id = " . $adAddressId);
        if (!$raw) {
            return false;
        } else {
            $raw->current()->delete();
            return true;
        }
    }
}
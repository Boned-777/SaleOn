<?php
class Application_Model_AdAddressCollection {
    public $list;
    public $partnerAddresses;
    public $adId;

    protected $_items;

    /**
     * Load model data
     *
     * @param $id
     * @return bool
     */
    public function getByAdId($id, $currentUserId=null) {
        if (is_null($currentUserId)) {
            $currentUserId = Zend_Auth::getInstance()->getIdentity()->id;
        }

        $adDb = new Application_Model_DbTable_AdAddress();
        $adStmt = $adDb->select()
            ->where("ad_id = ?", $id);

        $partnerDb = new Application_Model_DbTable_PartnerAddress();
        $partnerStmt = $partnerDb->select()
            ->from(array("pa" => "PartnerAddress"))
            ->joinLeft(
                array("aa" => $adStmt),
                "aa.address_id = pa.id"
            )
            ->where("user_id = ?", $currentUserId)
            ->order("name")
            ->group("pa.id");
        $partnerStmt->setIntegrityCheck(false);
        $partnerStmt->reset(Zend_Db_Select::COLUMNS);
        $partnerStmt->columns(array(
            new Zend_Db_Expr("pa.*"),
            new Zend_Db_Expr("COUNT(aa.ad_id) checked")
        ));

        $this->adId = $id;
        $this->_items = $partnerDb->fetchAll($partnerStmt);
        $this->list = array();
        if ($this->_items->count()) {
            $this->list = $this->_items->toArray();
        }

        return true;
    }

    /**
     * Creates new DB entry
     *
     * @param int $addrId
     * @return bool
     */
    public function add($adAddressId) {
        $item = new Application_Model_DbTable_AdAddress();
        $raw = $item->createRow();
        $raw->ad_id = $this->adId;
        $raw->address_id = $adAddressId;
        $result = $raw->save();
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * Removes ad additional address
     *
     * @param int $adAddressId
     * @return bool
     */
    public function remove($adAddressId) {
        $item = new Application_Model_DbTable_AdAddress();
        $raw = $item->fetchAll(
            "ad_id = " . $this->adId . " AND address_id = " . $adAddressId
        );
        if (!$raw->count()) {
            return false;
        } else {
            $raw->current()->delete();
            return true;
        }
    }

    /**
     * Updates data at DB
     *
     * @param array $list
     * @return bool
     */
    public function update($list) {
        foreach ($this->list as $item) {
            if (in_array($item["id"], $list)) {
                if (!$item["checked"]) {
                    $item["checked"] = true;
                    $this->add($item["id"]);
                }
            } else {
                if ($item["checked"]) {
                    $item["checked"] = false;
                    $this->remove($item["id"]);
                }
            }
        }
        return true;
    }
}
<?php
class Application_Model_Brand extends Application_Model_FilterMapper{
    var $id;
    var $name;
    var $user_id;
    var $partner;
    var $status;
    var $description;

    var $owner;

    var $dbItem;

    const ACTIVE = "ACTIVE";
    const INACTIVE = "INACTIVE";
    const NEW_BRAND = "NEW_BRAND";

    function __construct() {
        $this->dbItem = new Application_Model_DbTable_Brand();
    }

    public function get($id) {
        if (!(int)$id) {
            return false;
        }

        $db = new Application_Model_DbTable_Brand();
        $this->dbItem = $db->find($id);
        if ($this->dbItem) {
            $resArr = $this->dbItem->toArray();
            $this->loadData($resArr[0]);
            return $resArr;
        }
        return false;
    }

    /**
     * @return Application_Model_Partner
     */
    public function getOwner() {
        if (empty($this->owner)) {
            $this->owner = new Application_Model_Partner();
            if ($this->user_id) {
                $this->owner->getByUserId($this->user_id);
            }
        }
        return $this->owner;
    }

    public function setOwner($username) {
        $partner = new Application_Model_Partner();
        if (!$partner->getByUsername($username)) {
            $partnerData = array();
            $partnerData["password"] = uniqid();
            $partnerData["username"] = $username;
            $partner->create($partnerData);
        }
        echo $this->user_id = $partner->getUser()->id;

        $this->saveItem();
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
            "user_id",
            "status",
            "description"
        );
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
        return true;
    }

    public function create($brandName, $partnerId=null, $description=null) {
        $this->name = $brandName;
        $this->user_id = $partnerId;
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
            "user_id",
            "status",
            "description"
        );
        $resArr = array();
        foreach ($fields as $field) {
            if ($this->$field) {
                $resArr[$field] = $this->$field;
            }
        }

        return $resArr;
    }

    protected function getDbItem() {
        if (empty($this->dbItem) || empty($this->dbItem->id)) {
            $db = new Application_Model_DbTable_Brand();
            $where = 'name="' . $this->name . '"';
            if ($this->id) {
                $where = 'id="' . $this->id . '"';
            }
            $this->dbItem = $db->fetchRow($where);
        }
        return $this->dbItem;
    }

    public function saveItem() {
        $item = $this->getDbItem();
        $data = $this->toArray();
        foreach ($data as $key => $value) {
            $item->$key = $value;
        }
        return $item->save($data, $this->id);
    }

    public function save() {
        $db = new Application_Model_DbTable_Brand();
        $item = $db->fetchRow('name="' . $this->name . '"');
        if ($item) {
            return $item->id;
        } else {
            return $db->save($this->toArray(), $this->id);
        }
    }

    public function getAll() {
        $brandDb = new Application_Model_DbTable_Brand();
        $select = $brandDb->select()
            ->from(array("b" => "brands"))
            ->joinLeft(array("u" => "users"), "u.id = b.user_id")
            ->setIntegrityCheck(false);

        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array("b.*", "u.username"));

        $data = $brandDb->fetchAll($select);

        return $data->toArray();
    }

    public function getByAlias ($alias = "")
    {
        $aliasParts = explode("_", $alias);
        $id = (int)$aliasParts[0];
        if ($id) {
            $res = $this->dbItem->find($id);
            if ($res->count()) {
                $this->loadData($res->current());
                return $this->id;
            }
        }
        return false;
    }


}

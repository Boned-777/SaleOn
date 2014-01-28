<?php

class Application_Model_Order
{
    public $id;
    public $status;
    public $ad;
    public $type;
    public $amount;
    public $created_dt;
    public $paid_dt;
    public $modified_dt;

    const STATUS_READY = "READY";
    const STATUS_PAID = "PAID";
    const STATUS_CANCELED = "CANCELED";

    const TYPE_LIQPAY = "LIQPAY";
    const TYPE_CASH = "CASH";

    public function create($adItem, $type) {
        $this->ad = $adItem;
        $this->type = $type;
        $this->amount = $adItem->getPrice();
        $this->status = self::STATUS_READY;
        $this->created_dt = date('Y-m-d H:i:s');
        if ($this->isValid()) {
            $this->save();
            return true;
        } else
            return false;
    }

    public function load($data) {
        $vars = get_class_vars(get_class());
        foreach ($vars as $key => $value) {
            switch ($key) {
                case "ad":
                    if ($data['ad']) {
                        $item = new Application_Model_Ad();
                        $item->get($data['ad']);
                        $this->ad = $item;
                    }
                    break;

                default:
                    if (isset($data[$key]))
                        $this->$key = $data[$key];
                    break;
            }
        }
    }


    public function isValid() {
        if ($this->amount < 1)
            return false;
        return true;
    }

    public function save() {
        $vars = get_class_vars(get_class());
        $data = array();
        foreach ($vars as $key => $value) {
            switch ($key) {
                case "ad":
                    $data["ad"] = $this->ad->id;
                    break;

                default:
                    $data[$key] = $this->$key;
                    break;
            }

        }
        $dbItem = new Application_Model_DbTable_Order();
        $data["modified_dt"] = date('Y-m-d H:i:s');
        $res = $dbItem->save($data, $this->id);
        if ($res !== false)
            $this->id = $res;
        return $res;
    }

    public function get($id) {
        $item = new Application_Model_DbTable_Order();
        $data = $item->get($id);
        if ($data !== false) {
            $this->load($data);
            return $data;
        } else {
            return false;
        }
    }

    public function getByAd($id) {
        $item = new Application_Model_DbTable_Order();
        $data = $item->getByAd($id);
        if ($data !== false) {
            $this->load($data);
            return true;
        } else {
            return false;
        }
    }

    public function setStatus($value) {
        $this->status = $value;
        return $this->save();
    }

    public function toArray() {
        $vars = get_class_vars(get_class());
        $data = array();
        foreach ($vars as $key => $value) {
            $data[$key] = $this->$key;
        }
        return $data;
    }

}


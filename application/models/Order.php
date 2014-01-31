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
    public $response;

    const STATUS_READY = "READY";
    const STATUS_PAID = "PAID";
    const STATUS_CANCELED = "CANCELED";
    const STATUS_FAILED = "FAILED";
    const STATUS_WAIT_SECURE = "WAIT_SECURE";

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

    public function processResponse($responseXML) {
        $xmlDoc = new SimpleXMLElement($responseXML);
        $orderId = explode("_", $xmlDoc->order_id);
        $this->get($orderId[1]);

        switch ($xmlDoc->status) {
            case "success":
                $this->status = Application_Model_Order::STATUS_PAID;
                $this->ad->status = Application_Model_DbTable_Ad::STATUS_ACTIVE;
                $this->ad->save();
                break;

            case "failure":
                $this->status = Application_Model_Order::STATUS_FAILED;
                break;

            case "wait_secure":
                $this->status = Application_Model_Order::STATUS_WAIT_SECURE;
                break;
        }
        $this->paid_dt = date('Y-m-d H:i:s');
        $this->response = $responseXML;
        $this->save();
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
        if ($this->amount < 0)
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


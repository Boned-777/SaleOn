<?php

class Application_Model_Subscription
{
    public $id;
    public $brand_id;
    public $user_id;

    public function save() {
        $data = $this->toArray();
        $dbItem = new Application_Model_DbTable_User();
        $res = $dbItem->save($data, $this->id);
        return (bool)$res;
    }

    public function toArray($unset_id=false) {
        $vars = get_class_vars(get_class());
        $data = array();
        foreach ($vars as $key => $value) {
            switch ($key) {
                case 'brand_name' :
                    break;

                default:
                    $data[$key] = $this->$key;
                    break;
            }
        }

        if($unset_id) {
            unset($data["id"]);
        }

        return $data;
    }

    public function get($id) {
        $dbItem = new Application_Model_DbTable_Subscription();
        $data = $dbItem->get($id);
        if ($data !== false)
            $this->load($data);

        return $data;
    }

    public function create($data) {

        if (empty($data["id"])) {
            $brandItem = new Application_Model_Brand();
            $brandId = $brandItem->create($data);

            if ($brandId !== false) {
                $data["brand_id"] = $brandId;
            } else {
                return false;
            }
        }

        $this->load($data);
        $dbItem = new Application_Model_DbTable_Subscription();
        $item = $dbItem->createRow($this->toArray(true));
        try {
            $res = $item->save();
        } catch (Exception $e) {
            return false;
        }

        return $res;
    }

    public function load($data) {
        $vars = get_class_vars(get_class());
        foreach ($vars as $key => $value) {
            if (isset($data[$key]))
                $this->$key = $data[$key];
        }
    }

    public function getByUserId($userId, $noNames = false) {
        $dbItem = new Application_Model_DbTable_Subscription();

        $select = $dbItem->select()
            ->setIntegrityCheck(false)
            ->from(array("s" => "subscription"))
            ->where("s.user_id = ?", $userId);

        if (!$noNames) {
            $select
                ->join(array("b" => "brands"), "b.id = s.brand_id");
        }

        return $dbItem->fetchAll($select);
    }

    public function send() {
        $email = new Application_Model_MandrillAdapter();
        $dbItem = new Application_Model_DbTable_Ad();

        $select = $dbItem->select()
            ->from(array("a"=>"ads"))
            ->setIntegrityCheck(false)


            ->join(array("s"=>"subscription"), "a.brand = s.brand_id")
            ->join(array("b"=>"brands"), "a.brand = b.id")
            ->join(array("u"=>"users"), "u.id = s.user_id")

            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                "ad_id" => "a.id",
                "ad_name" => "a.name",
                "ad_brand" => "b.name",
                "email" => "u.username"
            ))
            ->where('a.public_dt = FORMAT(NOW(), "Y-m-d")')
            ->where("a.status = ?", Application_Model_DbTable_Ad::STATUS_ACTIVE);

        $items = $dbItem->fetchAll($select);

        foreach ($items as $item) {
            $msgText =
                "<p>Сегодня на сайте <a href='http://saleon.info'>saleon.info</a> была опубликована акция:</p>" .
                "<a href='http://saleon.info/show/" . Application_Model_Ad::createAlias($item->ad_id, $item->ad_name) . "'>$item->ad_name</a>" .
                "<br/><p style='color: #CCC'>Вы получили это письмо потому что подписаны на новые акции бренда <b>$item->ad_brand</b> на сайте <a href='http://saleon.info'>saleon.info</a><br/>" .
                "Настроить оповещения вы можете перейдя по <a href='http://saleon.info/subscription/manager'>этой ссылке</a></p>" .
                "<br>С уважением, <br/> Администрация SaleOn.info";
            $email->sendHTML(
                "Подписка на сайте SaleOn.info",
                $msgText,
                array($item->email => "")
            );
        }

    }
}


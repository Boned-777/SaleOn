<?php

class Application_Model_LiqPay
{
    private $version;
    private $merchant_id;
    private $merchant_sign;
    private $result_url;
    private $server_url;
    public $order_id;
    public $amount;
    public $currency;
    public $description;
    public $default_phone;
    public $pay_way;
    public $goods_id;

    public $xml;
    public $encoded_xml;
    public $encoded_signature;

    function __construct() {
        $config = Zend_Registry::get("config");
        $this->version = $config->liqpay->version;
        $this->merchant_id = $config->liqpay->merchant_id;
        $this->merchant_sign = $config->liqpay->merchant_sign;
        $this->result_url = "http://" . $_SERVER['HTTP_HOST'] . "/payment/finish";
        $this->server_url = "http://" . $_SERVER['HTTP_HOST'] . "/payment/result";
        $this->currency = "UAH";
        $this->pay_way = "card";
        $this->goods_id = 1;
        $this->default_phone = "380509999999";
    }

    private function _createXML($order) {
        $this->order_id = $order->id;
        $this->amount = $order->amount;
        $this->description = "Оплата послуги публікації контенту відповідно до замовлення №" . $order->id;

        $this->xml =
"<request>
    <version>$this->version</version>
    <merchant_id>$this->merchant_id</merchant_id>
    <result_url>$this->result_url</result_url>
    <server_url>$this->server_url</server_url>
    <order_id>ORDER_$this->order_id</order_id>
    <amount>$this->amount</amount>
    <currency>$this->currency</currency>
    <description>$this->description</description>
    <default_phone>$this->default_phone</default_phone>
    <pay_way>$this->pay_way</pay_way>
    <goods_id>$this->goods_id</goods_id>
</request>";
        $this->encoded_xml = base64_encode($this->xml);
    }

    private function _createSignature() {
        $this->encoded_signature = base64_encode(sha1($this->merchant_sign.$this->xml.$this->merchant_sign,1));
    }

    function prepareRequest($order) {
        $this->_createXML($order);
        $this->_createSignature();
    }

}


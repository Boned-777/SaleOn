<?php

class PaymentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function prepareAction()
    {
        global $translate;
        $vars = $this->getAllParams();

        $item = new Application_Model_Ad();
        if (!empty($vars["item_id"])) {
            if (!$item->get($vars["item_id"])) {
                $this->view->error = true;
                $this->view->errorMsg = $translate->getAdapter()->translate("ad_not_found");
            }
        }

        $order = new Application_Model_Order();
        if (!$order->getByAd($vars["item_id"])) {
            //if ad not found
            if (!$order->create($item, Application_Model_Order::TYPE_LIQPAY)) {
                $this->view->error = true;
                $this->view->errorMsg = $translate->getAdapter()->translate("order_not_created");
            };
        } else {
            //if ad found
            if (!$order->isValid()) {
                $this->view->error = true;
                $this->view->errorMsg = $translate->getAdapter()->translate("order_not_created");
            }
        }

        $this->view->order = $order;

        $payment = new Application_Model_LiqPay();
        $payment->prepareRequest($order);
        $this->view->xml = $payment->encoded_xml;
        $this->view->signature = $payment->encoded_signature;
    }

    public function confirmAction()
    {
        global $translate;
        $vars = $this->getAllParams();

        $item = new Application_Model_Ad();
        if (!empty($vars["id"])) {
            if (!$item->get($vars["id"])) {
                $this->view->error = true;
                $this->view->errorMsg = $translate->getAdapter()->translate("ad_not_found");
            }
        }



        $payment = new Application_Model_LiqPay();
        $payment->prepareRequest($item);
        $this->view->xml = $payment->encoded_xml;
        $this->view->signature = $payment->encoded_signature;
    }

    public function resultAction() {

    }

}




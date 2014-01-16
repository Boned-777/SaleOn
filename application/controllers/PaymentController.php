<?php

class PaymentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function confirmAction()
    {
        $payment = new Application_Model_LiqPay();
        $payment->prepareRequest();
        $this->view->xml = $payment->encoded_xml;
        $this->view->signature = $payment->encoded_signature;
    }

    public function resultAction() {

    }

}




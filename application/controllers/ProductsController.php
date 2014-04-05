<?php

class ProductsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function autocompAction()
    {
        $item = new Application_Model_DbTable_Product();
        $results = $item->autocompleteSearch($this->_getParam('term'));
        $this->_helper->json(array_values($results));
    }

    public function listAction()
    {
        $params = null;
        $request = new Zend_Controller_Request_Http();

//        if ($request->getCookie('category'))
//            $params["category"] = $request->getCookie('category');
//        if ($request->getCookie('brands'))
//            $params["brand"] = $request->getCookie('brands');
        if ($request->getCookie('geo'))
            $params["geo"] = $request->getCookie('geo');

        $item = new Application_Model_DbTable_Product();
        $results = $item->search($this->_getParam('term'), $params);
        $this->_helper->json(array("list" => $results));
    }

}




<?php

class CategoriesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function listAction()
    {
        $params = null;
        $request = new Zend_Controller_Request_Http();

        if ($request->getCookie('category'))
            $params["category"] = $request->getCookie('category');
        if ($request->getCookie('brands'))
            $params["brand"] = $request->getCookie('brands');
        if ($request->getCookie('products'))
            $params["product"] = $request->getCookie('products');

        $item = new Application_Model_Category();
        $results = $item->listAll();
        $this->_helper->json($results);
    }
}




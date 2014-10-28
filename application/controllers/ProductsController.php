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
        $params = Application_Model_FilterParameter::prepare(array("geo"));

        $item = new Application_Model_DbTable_Product();
        $results = $item->search($this->_getParam('term'), $params);
        $this->_helper->json(array("list" => $results));
    }

}




<?php

class BrandsController extends Zend_Controller_Action
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
        $item = new Application_Model_DbTable_Brand();
        $results = $item->autocompleteSearch($this->_getParam('term'));
        $this->_helper->json(array_values($results));
    }

    public function listAction()
    {
        $params = null;
        $request = new Zend_Controller_Request_Http();

        $params = Application_Model_FilterParameter::prepare(array("geo"));

        $item = new Application_Model_DbTable_Brand();
        $results = $item->search($this->_getParam('term'), $params);
        $this->_helper->json(array("list" => $results));
    }

    public function listAllAction()
    {
        global $translate;
        $params = null;
        $params = Application_Model_FilterParameter::prepare(array("geo"));

        $item = new Application_Model_DbTable_Brand();
        $results[] = array(
            "name" => $translate->getAdapter()->translate('list_brand'),
            "sub" => $item->search("", $params)
        );

        $item = new Application_Model_DbTable_Product();
        $results[] = array(
            "name" => $translate->getAdapter()->translate('list_product'),
            "sub" => $item->search("", $params)
        );

        $this->_helper->json($results);
    }

}




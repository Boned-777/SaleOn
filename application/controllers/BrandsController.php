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
        $item = new Application_Model_DbTable_Brand();
        $results = $item->search($this->_getParam('term'));
        $this->_helper->json(array("list" => $results));
    }

    public function listAllAction()
    {
        global $translate;
        $item = new Application_Model_DbTable_Brand();
        $results[] = array(
            "name" => $translate->getAdapter()->translate('list_brand'),
            "sub" => $item->search("")
        );

        $item = new Application_Model_DbTable_Product();
        $results[] = array(
            "name" => $translate->getAdapter()->translate('list_product'),
            "sub" => $item->search("")
        );

        $this->_helper->json($results);
    }

}




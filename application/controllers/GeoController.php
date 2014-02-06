<?php

class GeoController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function getAction()
    {
        $item = new Application_Model_Geo();
        $results = $item->getAllChild($this->_getParam('term'));
        $this->_helper->json($results);
    }

    public function listAction()
    {
        $item = new Application_Model_Geo();
        $results = $item->getAllChildList($this->_getParam('term'));
        $this->_helper->json(array("list" => $results));
    }
}


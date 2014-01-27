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
        $item = new Application_Model_Category();
        $results = $item->listAll();
        $this->_helper->json($results);
    }
}




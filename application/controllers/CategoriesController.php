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

        if ($request->getCookie('geo')) {
            $geo = new Application_Model_Geo();
            $geoCode = $geo->getByAlias($request->getCookie('geo'));
            if ($geoCode) {
                $params["geo"] = $geoCode;
            }
        }

        $item = new Application_Model_Category();
        $results = $item->listAll($params);
        $this->_helper->json($results);
    }
}




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
        $params = null;
//        $request = new Zend_Controller_Request_Http();
//        if ($request->getCookie('category'))
//            $params["category"] = $request->getCookie('category');
//        if ($request->getCookie('brands'))
//            $params["brand"] = $request->getCookie('brands');
//        if ($request->getCookie('products'))
//            $params["product"] = $request->getCookie('products');

        $item = new Application_Model_Geo();
        $results = $item->getAllChildList($this->_getParam('term'), $params);
        $this->_helper->json(array("list" => $results));
    }

    public function getListAction() {
        $adId = $this->getParam("ad", null);
        $item = new Application_Model_Geo();
        $results = $item->getAllTree($adId);
        $this->_helper->json($results);
    }

    public function getEditListAction() {
        $adId = $this->getParam("ad", null);
        $item = new Application_Model_Geo();
        $results = $item->getAllTree($adId, true);
        $this->_helper->json($results);
    }

    public function addAction() {
        $nativeName = $this->_getParam("native", null);
        $internationalName = $this->_getParam("inter", null);
        $currentCode = $this->_getParam("code", null);
        $parentCode = $this->_getParam("parent", null);

        $item = new Application_Model_Geo();

        if ($currentCode) {
            //edit mode
            $res = $item->editGeoItem($currentCode, $internationalName, $nativeName);
        } else {
            //create mode
            $res = $item->addGeoItem($parentCode, $internationalName, $nativeName);
        }



        $this->_helper->json($res);
    }

    public function getEditAction() {
        $geoCode = $this->_getParam("code", null);
        $item = new Application_Model_Geo();
        $res = $item->getByCode($geoCode);
        if ($res === false) {
            $this->_helper->json(array(
                "success" => false,
                "msg" => "code not found"
            ));
            exit;
        }
        $this->_helper->json(
            array(
                "success" => true,
                "data" => $res->toItemArray()
            )
        );
    }

    public function removeAction() {
        $geoCode = $this->_getParam("code", null);

        $item = new Application_Model_Geo();
        $res = $item->removeGeoItem($geoCode);

        $this->_helper->json($res);
    }
}


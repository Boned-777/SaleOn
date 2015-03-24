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

    public function editAction() {
        $brandId = $this->getParam('id', null);
        $brand = new Application_Model_Brand();

        if ($brand->get($brandId)) {
            if ($this->getRequest()->isPost()) {
                $brand->loadData($this->getAllParams());
                $brand->save();
                $this->redirect("/admin/brands");
            }

            $brand->getOwner();

            $this->view->brandForm = new Application_Form_Brand();
            $this->view->ownerForm = new Application_Form_BrandOwner();
            $this->view->brandForm->populate($brand->toArray());
            $this->view->ownerForm->populate(array_merge(
                $brand->owner->toArray(),
                array("brand_id" => $brand->id))
            );


        } else {
            $this->redirect("/admin/brands");
        }
    }

    public function editOwnerAction() {
        $result = array();

        $brand = new Application_Model_Brand();

        if ($brand->get($this->getParam('brand_id', null))) {
            $partner = new Application_Model_Partner();
            var_dump($partner->getByUsername($this->getParam('email', null)));

            Zend_Debug::dump($partner); die();

            if (!$partner->getByUsername($this->getParam('email', null))) {
die();
                $partnerData = $this->getAllParams();
                $partnerData["password"] = "any_password";
                $partnerData["username"] = $partnerData["email"];

                $partner = new Application_Model_Partner();
                $partner->create($partnerData);
            }



            $brand->setOwner($partner);
            $brand->save();

        }

        $this->_helper->json($result);
    }

}


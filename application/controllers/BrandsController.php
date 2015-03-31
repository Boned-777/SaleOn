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

            $owner = $brand->getOwner();

            $data = $brand->owner->toArray();
            $data["owner_email"] = $owner->user->username;
            $data["brand_id"] = $brand->id;

            $this->view->brandForm = new Application_Form_Brand();
            $this->view->ownerForm = new Application_Form_BrandOwner();
            $this->view->brandForm->populate($brand->toArray());
            $this->view->ownerForm->populate($data);


        } else {
            $this->redirect("/admin/brands");
        }
    }

    public function editOwnerAction() {
        $brand = new Application_Model_Brand();
        $partnerData = $this->getAllParams();
        if ($brand->get($this->getParam('brand_id', null))) {
            if ($brand->getOwner()->user->username !== $partnerData["owner_email"]) {
                $user = new Application_Model_User();
                if (!$user->getByUsername($this->getParam('owner_email', null))) {
                    $partnerData["password"] = uniqid();
                    $partnerData["username"] = $partnerData["owner_email"];
                    $partner = new Application_Model_Partner();
                    $partner->create($partnerData);
                    $user = $partner->user;
                }

                $brand->setOwner($user);
            }


        }
        $this->redirect("/brands/edit/id/" . $partnerData["brand_id"]);
    }

}


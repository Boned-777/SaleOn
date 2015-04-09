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
                $brand->saveItem();
            }

            $owner = $brand->getOwner();

            $data = $brand->owner->toArray();
            if (isset($owner->user)) {
                $data["owner_email"] = $owner->user->username;
            }
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
            if ($brand->getOwner()->getUser()->username !== $partnerData["owner_email"]) {
                $brand->setOwner($partnerData["owner_email"]);
            }
        }
        $this->redirect("/brands/edit/id/" . $partnerData["brand_id"]);
    }

    public function combineAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            if ($auth->getIdentity()->role === Application_Model_User::ADMIN) {
                $params = $this->getAllParams();
                $currentBrand = new Application_Model_Brand();
                $targetBrand = new Application_Model_Brand();

                if (
                    isset($params["current"]) &&
                    isset($params["target"])
                ){
                    $currentBrand->get($params["current"]);
                    $targetBrand->get($params["target"]);

                    $currentBrand->status = Application_Model_Brand::INACTIVE;
                    $targetBrand->status = Application_Model_Brand::ACTIVE;
                    if (!$targetBrand->user_id) {
                        $targetBrand->user_id = $currentBrand->user_id;
                    }
                    $currentBrand->saveItem();
                    $targetBrand->saveItem();
                    $ads = new Application_Model_Ad();
                    $ads->changeAdsBrand($currentBrand->id, $targetBrand->id);
                    $subscr = new Application_Form_Subscription();
                    $subscr->changeSubscriptionsBrand($currentBrand->id, $targetBrand->id);
                    return $this->_helper->json(array("success" => true));
                }
            }
        }

        return $this->_helper->json(array("success" => false));
    }

}


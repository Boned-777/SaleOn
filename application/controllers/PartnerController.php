<?php

class PartnerController extends Zend_Controller_Action
{
    public $user;

    /**
     *
     */
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->user = $auth->getIdentity();
        } else {
            $vars = $this->getAllParams();

            if (!in_array($vars["action"], array("new", "index"))) {
                $this->_redirect("/partner/new");
            }
        }
    }

    public function addAddressAction() {
        $addressValue = $this->getParam("val");
        if (!is_null($addressValue)) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            $userId = $identity->id;
            if ($identity->role == Application_Model_User::ADMIN) {
                $userId = null;
                $adId = $this->getParam("ad", null);
                if (!is_null($adId)) {
                    $ad = new Application_Model_Ad();
                    $ad->get($adId);
                    $userId = $ad->owner;
                }
            }

            if (!$userId) {
                $this->_helper->json(array(
                    "success" => false,
                    "msg" => "Invalid user"
                ));
            }
            $partner = new Application_Model_Partner();

            if ($partner->getByUserId($userId)) {
                $id = $partner->addAddress($addressValue);
                $this->_helper->json(array("success" => true, "id"=>$id, "name"=>$addressValue));
            } else {
                $this->_helper->json(array(
                    "success" => false,
                    "msg" => "Invalid ad properties"
                ));
            }
        }
    }

    public function indexAction() {

    }

    public function brandsAction() {
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $partner = new Application_Model_Partner();
        $partner->getByUserId($userId);
        $brands = $partner->getBrands();

        $grid = Bvb_Grid::factory('Table');
        $grid->setSource(new Bvb_Grid_Source_Array($brands));
        $grid->setGridColumns(array("name", 'status', 'stats'));
        $grid->setTemplateParams(array("cssClass" => array("table" => "table table-bordered table-striped")));
        $grid->setNoFilters(true);
        $grid->setExport(array());
        $grid->setImagesUrl('/img/');

        $this->view->grid = $grid;

        $this->view->brandForm = new Application_Form_Brand();
    }

    public function removeAddressAction() {
        $res = false;
        $addressId = $this->getParam("addr");
        if (!is_null($addressId)) {
            $identity = Zend_Auth::getInstance()->getIdentity();
            $partner = new Application_Model_Partner();
            if ($partner->getByUserId($identity->id)) {
                if ($partner->removeAddress($addressId)) {
                    $res = true;
                }
            };
        }
        return $this->_helper->json(array("success" => $res));
    }

    public function profileAction()
    {
        global $translate;
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();

        $vars = $this->getAllParams();
        $item = new Application_Model_Partner();

        if (isset($vars["id"])) {
            $id = $vars["id"];
            $data = $item->get($id);
        } else {
            $id = $this->user->id;
            $data = $item->getByUserId($id);
        }

        if ($data === false) {
            $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
            die ($id."Invalid ID");
            return false;
        }

        $changePasswordForm = new Application_Form_ChangePassword();
        $changePasswordForm->populate(array("id" => $this->user->id));
        $this->view->changePasswordForm = $changePasswordForm;

        $form = new Application_Form_Partner();
        $form->setName("partner_profile");
        $request = $this->getRequest();

        $vars = $this->getAllParams();

        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                if (isset($vars["form"]) ? $vars["form"] : false == "change_password") {
                    if ($changePasswordForm->isValid($request->getPost())) {
                        $this->_changePassword($changePasswordForm->getValues());
                        $view->successMessage = $translate->getAdapter()->translate("success") . " " . $translate->getAdapter()->translate("data_save_success");
                    } else {
                        $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
                    }
                } else {
                    $item->load($vars);
                    if ($item->save()) {
                        $view->successMessage = $translate->getAdapter()->translate("success") . " " . $translate->getAdapter()->translate("data_save_success");
                    } else {
                        //$view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
                    }
                }
            } else {
                $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
            }
        } else {
            $form->populate($item->toArray());
        }

        $this->view->item = $item;
        $this->view->form = $form;
        $this->view->email = $this->user->username;
    }

    private function _changePassword ($vars) {
        $item = new Application_Model_DbTable_User();
        if (!$item->find((int)$vars["id"])) {
            return false;
        }

        $data = array();
        $password = $vars["password"];
        $data["salt"] = md5(uniqid().uniqid().uniqid());
        $data["password"] = sha1($password.$data["salt"]);

        $item->save($data, $vars["id"]);
        return true;
    }

    public function newAction()
    {
        global $translate;

        $registrationForm = new Application_Form_User();
        $registrationForm->setName("partner_registration");
        $loginForm = new Application_Form_Login();
        $loginForm->setName("login");
        $request = $this->getRequest();

        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();

        if ($request->isPost()) {
            if ($registrationForm->isValid($request->getPost())) {
                $data = $registrationForm->getValues();
                if ($this->_create($data)) {
                    $authObj = new Application_Model_Auth();
                    $authObj->process(
                        $data["username"],
                        $data["password"]
                    );
                    $this->_helper->redirector('profile', 'partner');
                    $view->successMessage = $translate->getAdapter()->translate("success") . " " . $translate->getAdapter()->translate("data_save_success");
                } else {
                    $registrationForm->getElement("username")->addError($translate->getAdapter()->translate("error_username_exists"));
                    $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
                }
            }
        }
        $this->view->registrationForm = $registrationForm;
        $this->view->loginForm = $loginForm;
    }

    private function _create($data) {
        $item = new Application_Model_User();
        $data["role"] = Application_Model_User::PARTNER;
        $newUserId =  $item->create($data);
        if ($newUserId === false)
            return false;
        $partner = new Application_Model_Partner();
        $partner->user_id = $newUserId;
        return $partner->save();
    }

    public function addAction()
    {
        // action body
    }

    public function rulesAction()
    {
        // action body
    }
}






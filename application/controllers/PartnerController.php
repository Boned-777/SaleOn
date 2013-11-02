<?php

class PartnerController extends Zend_Controller_Action
{
    public $user;

    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->user = $auth->getIdentity();
        } else {
            $vars = $this->getAllParams();
            if ($vars["action"] != "new")
                $this->_redirect("/partner/new");
        }
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

        if ($data === false)
            die("Invalid ID");

        $form = new Application_Form_Partner();
        $form->setName("partner_profile");
        $request = $this->getRequest();

        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $item->load($form->getValues());
                if ($item->save()) {
                    $view->successMessage = $translate->getAdapter()->translate("success") . " " . $translate->getAdapter()->translate("data_save_success");
                } else {
                    //$view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
                }
            } else {
                $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
            }
        } else {
            $form->populate($item->toArray());
        }

        $this->view->form = $form;
        $this->view->email = $this->user->username;
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
                    $this->_helper->redirector('index', 'auth', null, array(
                        "username" => $data["username"],
                        "password" => $data["password"],
                    ));
                    $view->successMessage = $translate->getAdapter()->translate("success") . " " . $translate->getAdapter()->translate("data_save_success");
                } else {
                    $view->errorMessage = $translate->getAdapter()->translate("error") . " " . $translate->getAdapter()->translate("data_save_error");
                }
            }
        }
        $this->view->registrationForm = $registrationForm;
        $this->view->loginForm = $loginForm;
    }

    private function _create($data) {
        $item = new Application_Model_Partner();
        return $item->create($data);
    }

    public function addAction()
    {
        // action body
    }


}






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
        $vars = $this->getAllParams();
        if (isset($vars["id"])) {
            $id = $vars["id"];
        } else {
            $id = $this->user->id;
        }

        $item = new Application_Model_Partner();
        $data = $item->get($id);

        if ($data === false)
            die("Invalid ID");

        $form = new Application_Form_Partner();
        $form->setName("partner_profile");
        $request = $this->getRequest();

        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $item->load($form->getValues());
                if ($item->save()) {
                    $this->_helper->redirector('index', 'partner');
                } else {
                    $this->view->errorStatus = TRUE;
                }
            }
        } else {
            $form->populate($data);
        }

        $this->view->form = $form;
        $this->view->email = $this->user->username;
    }

    public function newAction()
    {
        $registrationForm = new Application_Form_User();
        $registrationForm->setName("partner_registration");
        $loginForm = new Application_Form_Login();
        $loginForm->setName("login");
        $request = $this->getRequest();

        if ($request->isPost()) {
            if ($registrationForm->isValid($request->getPost())) {
                if ($this->_create($registrationForm->getValues())) {
                    $this->_helper->redirector('index', 'partner');
                } else {
                    $this->view->errorStatus = TRUE;
                }
            }
        }
        $this->view->registrationForm = $registrationForm;
        $this->view->loginForm = $loginForm;
    }

    private function _create($data) {
        $item = new Application_Model_Partner();
        $item->create($data);
    }

    public function addAction()
    {
        // action body
    }


}






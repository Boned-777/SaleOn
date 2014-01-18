<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
//    	$accessDenied = array("salesperson");
//
//		$auth = Zend_Auth::getInstance();
//		if ($auth->hasIdentity()) {
//			if (in_array($auth->getIdentity()->role, $accessDenied))
//				$this->_helper->redirector('index', 'index');
//		} else {
//			$this->_helper->redirector('index', 'auth');
//		}
    }

    public function indexAction()
    {
		if (!isset($option)) {
	        $itemsList = new Application_Model_DbTable_User();
			$this->view->itemsList = $itemsList->fetchAll();
		}
    }

    public function addAction()
    {
		$form = new Application_Form_AddUser();
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				$res = $this->_add($form->getValues());
				if ($res) {
					$this->_helper->redirector('index');
				} else {
					$form->getElement("email")->setErrorMessages(array("Such email already exists"));
					$form->getElement("email")->isValid(false);
					$form->populate($request->getPost());
				}
			}
		}
		$this->view->form = $form;
    }

    private function _add($vars)
    {
		$item = new Application_Model_DbTable_User();
		$data = $item->add($vars);

		if ($data === false) {
			return false;
		}
		
		
//		$mailhost= '127.0.0.1';
//		$mailconfig = array(
//			'port'    =>  '25',
//		);
//
//		$transport = new Zend_Mail_Transport_Smtp ($mailhost, $mailconfig);
//		Zend_Mail::setDefaultTransport($transport);
//
//		$text = "We have created an account for you in the.\nLog in using the details below:\n\n".
//		"Email: " . $data["username"] . "\n".
//		"Password: " . $data["real_password"];
//
//		//echo "<h1>This is temporary solution!</h1> Message is: <br/>" . $text; exit;
//
//		$mail = new Zend_Mail();
//		$mail->setBodyText($text);
//		$mail->setFrom('admin@test.com', 'test');
//		$mail->addTo($data["username"], '');
//		$mail->setSubject('New account for you');
//		$mail->send();
		
		return true;
    }

    public function listtraffickersAction()
    {
    	$item = new Application_Model_DbTable_User();
		$results = $item->autocompleteTraffickersSearch($this->_getParam('term'));
		$this->_helper->json(array_values($results));
    }

    public function recoveryAction()
    {
        global $translate;

        $form = new Application_Form_PasswordRecovery();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $vars = $this->getAllParams();
            $item = new Application_Model_User();
            $data = $item->getByUsername($vars["username"]);
            if ($data !== false) {
                $item->recovery = uniqid().uniqid().uniqid();
                $item->save();
                $this->view->successMsg = $translate->getAdapter()->translate("recovery_success") . $item->recovery;
            } else {
                $form->populate($request->getPost());
                $this->view->errorMsg = $translate->getAdapter()->translate("recovery_error_email_not_found");
            }
        }
        $this->view->form = $form;
    }

    public function passrecoveryAction()
    {
        global $translate;
        $vars = $this->getAllParams();
        $item = new Application_Model_User();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form = new Application_Form_ChangePassword();
            if ($form->isValid($vars)) {
                $item->getUser($vars["id"]);
                if ($item->recovery == $vars["recovery"]) {
                    $this->_changePassword($vars);
                    $this->view->successMsg = $translate->getAdapter()->translate("password_change_success");
                } else {
                    $this->view->errorMsg = $translate->getAdapter()->translate("recovery_error");
                }
            } else {
                $form->populate($request->getPost());
                $this->view->form = $form;
            }
        } else {
            $res = $item->getByRecoveryCode(isset($vars["code"])?$vars["code"]:"");
            if ($res !== false) {
                $form = new Application_Form_ChangePassword();
                $form->populate(array("id" => $item->id, "recovery" => $item->recovery));
                $this->view->form = $form;
            } else {
                $this->view->errorMsg = $translate->getAdapter()->translate("recovery_error_code_not_found");
            }
        }

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
        $data["recovery"] = "";

        $item->save($data, $vars["id"]);
        return true;
    }

    public function favoritesAction() {
        $auth = Zend_Auth::getInstance();

        $user = new Application_Model_User();
        $user->getUser($auth->getIdentity()->id);


        if ($this->getParam("act") == "add")
            $user->addFavoriteAd($this->getParam("ad_id"));
        else
            $user->removeFavoriteAd($this->getParam("ad_id"));
        //Zend_Debug::dump($user); die();

        $res = $user->save();
        $systemUser = $auth->getIdentity();
        $systemUser->favorites_ads = $user->favorites_ads;
        $this->_helper->json(array("success" => $res));
    }

    public function newAction() {
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
        $data["role"] = Application_Model_User::USER;
        $a = $item->create($data);
        return $a;
    }

}






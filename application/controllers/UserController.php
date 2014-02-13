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

		$mailhost= '127.0.0.1';
		$mailconfig = array(
			'port'    =>  '25',
		);

		$transport = new Zend_Mail_Transport_Smtp ($mailhost, $mailconfig);
		Zend_Mail::setDefaultTransport($transport);

		$text = "Рады приветствовать Вас на сайте WantLook.info\nДанные для входа:\n\n".
		"Email: " . $data["username"] . "\n".
		"Пароль: " . $data["real_password"].
        "\n\n\n\nWelcome on WantLook.info\n Credentials:\n\n".
        "Email: " . $data["username"] . "\n".
        "Password: " . $data["real_password"];

        $mail = new Zend_Mail('UTF-8');
		$mail->setBodyText($text);
		$mail->setFrom('no-reply@wantlook.info', 'WantLook.info');
		$mail->addTo($data["username"], '');
		$mail->setSubject('Регистрация на WantLook.info');
		$mail->send();
		
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
                $this->_sendRecoveryMsg($item->recovery, $item);
                $this->view->successMsg = $translate->getAdapter()->translate("recovery_success");
            } else {
                $form->populate($request->getPost());
                $this->view->errorMsg = $translate->getAdapter()->translate("recovery_error_email_not_found");
            }
        }
        $this->view->form = $form;
    }

    protected function _sendRecoveryMsg($code, $user) {
        $mailhost= '127.0.0.1';
        $mailconfig = array(
            'port'    =>  '25',
        );

        $transport = new Zend_Mail_Transport_Smtp ($mailhost, $mailconfig);
        Zend_Mail::setDefaultTransport($transport);

        $text = "На сайте WantLook.info был создан запрос на восстановление пароля. Перейдите по ссылке ниже для окончания процедуры смены пароля.\n\n".
            "http://wantlook.info/user/passrecovery?code=" . $code;

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText($text);
        $mail->setFrom('no-reply@wantlook.info', 'WantLook.info');
        $mail->addTo($user->username, '');
        $mail->setSubject('Восстановление пароля');
        $mail->send();
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
        $res = $user->save();
        $systemUser = $auth->getIdentity();
        $systemUser->favorites_ads = $user->favorites_ads;
        $this->_helper->json(array("success" => $res));
    }

    public function langAction() {
        $lang = $this->getParam("lang");
        $user = new Application_Model_User();
        if ($user->setGlobalLocale($lang)) {
            $auth = Zend_Auth::getInstance();
    		if ($auth->hasIdentity()) {
                if ($user->getByUsername($auth->getIdentity()->username)) {
                    $user->locale = $lang;
                    $this->_helper->json(array("success"=>(bool)$user->save()));
                };
    		}
            $this->_helper->json(array("success"=>true));
        } else {
            $this->_helper->json(array("success"=>false));
        }
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






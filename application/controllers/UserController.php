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
                $this->view->successMsg = $translate->getAdapter()->translate("recovery_success");
            } else {
                $form->populate($request->getPost());
                $this->view->errorMsg = $translate->getAdapter()->translate("recovery_error_email_not_found");
            }
        }
        $this->view->form = $form;
    }

    private function _createRecovery($user) {
        $item = new Application_Model_User();

    }

}






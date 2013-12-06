<?php

class AuthController extends Zend_Controller_Action
{

	public function init()
	{

	}

	public function indexAction()
	{
		$form = new Application_Form_Login();
		$request = $this->getRequest();
		
		if ($request->isPost()) {
            $vars = $request->getPost();
			if ($form->isValid($vars)) {
                $data = array(
                    "username" => $vars["username"],
                    "password" => $vars["password"]
                );
                $user = $this->_process($data);
                if ($user) {
                    if ($user->role == "PARTNER")
                        $this->_helper->redirector('profile', 'partner');
                    else
                        $this->_helper->redirector('index', 'index');
                } else {
                    $this->view->errorStatus = TRUE;
                }
                $form->populate($data);
			}
		}



		$this->view->form = $form;
	}

	private function _process($values)
	{
		$adapter = $this->_getAuthAdapter();
		$adapter->setIdentity($values['username']);
		$adapter->setCredential($values['password']);

		$auth = Zend_Auth::getInstance();
		$result = $auth->authenticate($adapter);

		if ($result->isValid()) {
			$user = $adapter->getResultRowObject();
			$auth->getStorage()->write($user);
			return $user;
		}
		return false;
	}

	protected function _getAuthAdapter()
	{
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		
		$authAdapter->setTableName('users')
			->setIdentityColumn('username')
			->setCredentialColumn('password')
            ->setCredentialTreatment('SHA1(CONCAT(?,salt)) AND deleted=0');
			//->setCredentialTreatment('UNHEX(SHA1(CONCAT(?,salt))) AND deleted=0');
		
		return $authAdapter;
	}

	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('index');
	}
}




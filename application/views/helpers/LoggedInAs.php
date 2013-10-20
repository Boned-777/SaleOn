<?php
class Zend_View_Helper_LoggedInAs extends Zend_View_Helper_Abstract
{
	public function loggedInAs ()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$username = $auth->getIdentity()->name;
			$logoutUrl = $this->view->url(array('controller'=>'auth', 'action'=>'logout'), null, true);
			return '<br/><h2>Welcome, ' . $username . "</h2>";
		}
		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		
		if ($controller == 'auth' && $action == 'index') {
			return '';
		}
		$loginUrl = $this->view->url(array('controller'=>'auth', 'action'=>'index'));
		
		return 'Login';
	}
}
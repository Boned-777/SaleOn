<?php
class Zend_View_Helper_MainMenu extends Zend_View_Helper_Abstract
{
	public function MainMenu ()
	{
		$auth = Zend_Auth::getInstance();
        $view = new Zend_View();
        $view->setScriptPath(APPLICATION_PATH . "/views/helpers/views");

        $params = array();
		if ($auth->hasIdentity()) {
            if (
                isset($auth->getIdentity()->role) &&
                (
                    $auth->getIdentity()->role === Application_Model_User::PARTNER ||
                    $auth->getIdentity()->role === Application_Model_User::ADMIN
                )
            ){
                $params["username"] = $auth->getIdentity()->username;
            }
        }
        echo $view->partial("mainMenu.phtml", $params);
	}
}
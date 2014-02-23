<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			
		} else {
			//$this->_redirect("/auth");
		}
    }

    public function indexAction()
    {
        // action body
    }

    public function favoritesAction()
    {
        // action body
    }

    public function newsAction()
    {
        // action body
    }

    public function contactsAction()
    {
        // action body
    }

}
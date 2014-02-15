<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
//        echo '
//        ';
//
//        $consumer = new Zend_Oauth_Consumer($aGoogleConfig);
//        $token = null;
//
//        die();

	}
	public function authAction()
    {
        Zend_Debug::dump($this->getAllParams()); die();
    }
}


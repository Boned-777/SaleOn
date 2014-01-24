<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        phpinfo();

        die();

	}
	public function favoritesAction()
    {
        // action body
    }
}


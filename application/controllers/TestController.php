<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $test = new Application_Model_User();
        $test->addFavoriteAd(2);
        $test->addFavoriteAd(4);
        $test->addFavoriteAd(5);

        $test->removeFavoriteAd(4);

        print_r($test->favorites_ads); die();

	}
}


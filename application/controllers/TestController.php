<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $a = new Application_Model_LiqPay();
//        $src = "/var/www/waw/public/ads/1.jpg";
//
//        $target_width = 240;
//        $target_height = 153;
//        $image = new Application_Model_Image();
//        $image->load($src);
//        $image->smartResize($target_width, $target_height);
//        $image->save("/var/www/waw/public/ads/2.jpg");

        die();

	}

    public function authAction() {
        $this->config = Zend_Registry::get('sauthConf');
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->getResponse()->setRedirect($this->view->siteDir);
        }
        $adapterName = $this->getRequest()->getParam('by') ? $this->getRequest()->getParam('by') : 'google';
        $adapterClass = 'SAuth_Adapter_' . ucfirst($adapterName);
        $adapter = new $adapterClass($this->config[$adapterName]);
        $result  = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $this->_helper->redirector('index', 'index');
        } else {
            $this->view->auth = false;
            $this->view->errors = $result->getMessages();
        }
    }
}


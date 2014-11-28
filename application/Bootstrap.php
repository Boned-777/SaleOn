<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initViewHelpers()
	{
		$view = new Zend_View();
		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
	 
		$view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
		$viewRenderer->setView($view);
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
		
		$autoloader = new Zend_Application_Module_Autoloader (array ('namespace' => '', 'basePath' => APPLICATION_PATH));
    	$autoloader->addResourceType ('Custom_Form_Element', 'forms/elements', 'Custom_Form_Element_');
		$autoloader->addResourceType ('Custom_Form_Validator', 'forms/validators', 'Custom_Form_Validator_');
	}

    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);
    }

    protected function _initSauth()
    {
        $siteDir = $this->getOption('siteDir');
        $siteUrl = $this->getOption('siteUrl');
        $url = "http://saleon.info/auth/auth/";

        $sauthConf['twitter'] = array(
            'consumerKey' => 'umLBxpl2qIik2V83EuZET2D3j',
            'consumerSecret' => 'iBlwECsETaGNobispnuHJQ2c9gmSeCqzwchv8t7diqz1ws4bv1',
            'callbackUrl' => $url . 'by/twitter',
        );

        $sauthConf['facebook'] = array(
            'consumerId' => '570943869640380',
            'consumerSecret' => 'da1e69c41727f1747bd98a44b5aefa16',
            'callbackUrl' => $url . 'by/facebook',
            'display' => SAuth_Adapter_Facebook::DISPLAY_POPUP,
            'scope' => array(
                'user_about_me', 'email',
            ),
        );

        $sauthConf['vkontakte'] = array(
            'consumerId' => '4654202',
            'consumerSecret' => 'Tm0kNZk8tCIVKjbpwFkW',
            'callbackUrl' => $url . 'by/vkontakte',
        );

//        $sauthConf['mailru'] = array(
//            'consumerId' => '',
//            'privateKey' => '',
//            'consumerSecret' => '',
//            'callbackUrl' => $siteUrl . $siteDir . '/index/auth/by/mailru',
//        );

        Zend_Registry::set('sauthConf', $sauthConf);
    }
}

define('DFP_INCLUDE_PATH', APPLICATION_PATH . '/../library/api/src/');

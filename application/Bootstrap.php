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
        $url = "http://wantlook.info/auth/auth/";

        $sauthConf['google'] = array(
            'id' => 'https://www.google.com/accounts/o8/id',
            'callbackUrl' => $url . 'by/google',
            'exchangeExtension' => array(
                'openid.ns.ax' => 'https://accounts.google.com/o/oauth2/auth',
                'openid.ax.mode' => 'fetch_request',
                'openid.ax.type.email' => '596664800521@developer.gserviceaccount.com',
                'openid.ax.required' => 'email',
            ),
        );

//        $sauthConf['google'] = array(
//            'id' => 'https://www.google.com/accounts/o8/id',
//            'callbackUrl' => $url . 'by/google',
//            'exchangeExtension' => array(
//                'openid.ns.ax' => 'http://openid.net/srv/ax/1.0',
//                'openid.ax.mode' => 'fetch_request',
//                'openid.ax.type.email' => 'http://axschema.org/contact/email',
//                'openid.ax.required' => 'email',
//            ),
//        );

        $sauthConf['twitter'] = array(
            'consumerKey' => 'zTMUlfNyrW8VuStaR6kgjw',
            'consumerSecret' => '2rspzvAhqGDxXjhPtbEdQw8GijcktlPX5hfr7EGI',
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
            'consumerId' => '4138930',
            'consumerSecret' => 'vvtR6C0BRP5KnANSloef',
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

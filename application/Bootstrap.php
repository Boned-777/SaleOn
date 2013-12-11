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

    protected function _initSauth()
    {
        $siteDir = $this->getOption('siteDir');
        $siteUrl = $this->getOption('siteUrl');

        $sauthConf['google'] = array(
            'id' => 'https://www.google.com/accounts/o8/id',
            'callbackUrl' => $siteUrl . $siteDir . '/index/auth/by/google',
            'exchangeExtension' => array(
                'openid.ns.ax' => 'http://openid.net/srv/ax/1.0',
                'openid.ax.mode' => 'fetch_request',
                'openid.ax.type.email' => 'http://axschema.org/contact/email',
                'openid.ax.required' => 'email',
            ),
        );

        $sauthConf['twitter'] = array(
            'consumerKey' => 'zTMUlfNyrW8VuStaR6kgjw',
            'consumerSecret' => '2rspzvAhqGDxXjhPtbEdQw8GijcktlPX5hfr7EGI',
            'callbackUrl' => 'http://wantlook.info/test/auth/by/twitter',
        );

        $sauthConf['facebook'] = array(
            'consumerId' => '247425758754919',
            'consumerSecret' => '64be33d726079d25f6258d0781e0b692',
            'callbackUrl' => 'http://wantlook.info/test/auth/by/facebook',
            'display' => SAuth_Adapter_Facebook::DISPLAY_POPUP,
            'scope' => array(
                'user_about_me', 'email',
            ),
        );

        $sauthConf['vkontakte'] = array(
            'consumerId' => '',
            'consumerSecret' => '',
            'callbackUrl' => $siteUrl . $siteDir . '/index/auth/by/vkontakte',
        );

        $sauthConf['skyrock'] = array(
            'consumerKey' => '',
            'consumerSecret' => '',
            'callbackUrl' => $siteUrl . $siteDir . '/index/auth/by/skyrock',
        );

        $sauthConf['mailru'] = array(
            'consumerId' => '',
            'privateKey' => '',
            'consumerSecret' => '',
            'callbackUrl' => $siteUrl . $siteDir . '/index/auth/by/mailru',
        );

        $sauthConf['foursquare'] = array(
            'consumerSecret' => '',
            'consumerId' => '',
            'callbackUrl' => $siteUrl . $siteDir . '/index/auth/by/foursquare',
        );

        $sauthConf['flickr'] = array(
            'consumerKey' => '',
            'consumerSecret' => '',
            'userAuthorizationUrl' => 'http://flickr.com/services/auth/',
            'permission' => SAuth_Adapter_Flickr::PERMS_READ,
        );

        $sauthConf['gowalla'] = array(
            'consumerSecret' => '',
            'consumerId' => '',
            'callbackUrl' => $siteUrl . $siteDir . '/index/auth/by/gowalla',
        );

        $sauthConf['github'] = array(
            'consumerSecret' => '',
            'consumerId' => '',
            'callbackUrl' => $siteUrl . $siteDir . '/index/auth/by/github',
        );

        $sauthConf['linkedin'] = array(
            'consumerKey' => '',
            'consumerSecret' => '',
            'callbackUrl' => $siteUrl . $siteDir . '/index/auth/by/linkedin',
            'scope' => array(
                'r_fullprofile', 'r_emailaddress',
            ), // list of the available permissions https://developer.linkedin.com/documents/authentication#granting
            'userFields' => array('id', 'first-name', 'last-name') // list of the available fields https://developer.linkedin.com/documents/profile-fields
        );

        Zend_Registry::set('sauthConf', $sauthConf);

    }
}

define('DFP_INCLUDE_PATH', APPLICATION_PATH . '/../library/api/src/');

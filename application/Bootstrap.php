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
}

define('DFP_INCLUDE_PATH', APPLICATION_PATH . '/../library/api/src/');

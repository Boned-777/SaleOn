<?php
// Define path to/var/www/digitalio/application/forms/elements/Universal.php application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(
    APPLICATION_PATH.'/../library'.PATH_SEPARATOR.
    APPLICATION_PATH.'/../library/Zend'
);

include "Zend/Loader/Autoloader.php";


$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('SAuth');
$autoloader->registerNamespace('Bvb');
$autoloader->registerNamespace('Zendx');


$session = new Zend_Session_Namespace();
$session->locale = isset($session->locale)?$session->locale:"ua";

$translate = new Zend_Translate(
    'array',
    APPLICATION_PATH . '/locale/lang_' . $session->locale . ".php",
    $session->locale
);
$translate->setlocale($session->locale);
Zend_Form::setDefaultTranslator($translate);

$locationLang = "US";

switch ($session->locale) {
    case "ua" :
    case "ru" :
        $locationLang = "UKR";
        break;

    case "pl":
        $locationLang = "POL";
        break;
}

try {
    $locationsNative = new Zend_Translate(
        'array',
        APPLICATION_PATH . '/locale/location_' . $locationLang . ".php",
        $session->locale
    );
} catch (Exeption $e){

}

try {
$locationsInternational = new Zend_Translate(
    'array',
    APPLICATION_PATH . '/locale/location_US.php',
    $session->locale
);
} catch (Exeption $e){

}

$ctrl = Zend_Controller_Front::getInstance();
$router = $ctrl->getRouter();
$route = new Zend_Controller_Router_Route(
    'filter/:geo/:category/:brand/:product',
    array(
        'module' => "default",
        'controller' => 'index',
        'action' => 'index'
    )
);
$router->addRoute('filter', $route);

$route = new Zend_Controller_Router_Route(
    'filter/:geo/:category/:brand/:product/:sort',
    array(
        'module' => "default",
        'controller' => 'index',
        'action' => 'index'
    )
);
$router->addRoute('filter_sort', $route);

$route = new Zend_Controller_Router_Route(
    'show/:ad_id',
    array(
        'module' => "default",
        'controller' => 'ad',
        'action' => 'index'
    )
);
$router->addRoute('show', $route);

/** Zend_Application */
require_once 'Zend/Application.php';
// Create application, bootstrap, and run
$application = new Zend_Application(
    'development',
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();

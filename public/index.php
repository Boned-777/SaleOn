<?php
// Define path to/var/www/digitalio/application/forms/elements/Universal.php application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

include "Zend/Loader/Autoloader.php";

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Bvb');
$autoloader->registerNamespace('Zendx');
$autoloader->registerNamespace('SAuth');

$locale = 'ru';
$translate = new Zend_Translate(
    'array',
    APPLICATION_PATH . '/locale/lang_' . $locale . ".php",
    "ru"
);
$translate->setlocale ($locale);

/** Zend_Application */
require_once 'Zend/Application.php';
// Create application, bootstrap, and run
$application = new Zend_Application(
    'development',
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();

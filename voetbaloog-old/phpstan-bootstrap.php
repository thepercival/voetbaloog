<?php
/**
 * Created by PhpStorm.
 * User: cdunnink
 * Date: 15-4-2019
 * Time: 09:50
 */

$szPath = realpath(dirname(__FILE__) ).'/';
$szAppName = "voetbaloog";
if (defined('APPLICATION_NAME') !== true)
    define('APPLICATION_NAME', $szAppName);
if (defined('APPLICATION_PATH') !== true)
    define('APPLICATION_PATH', $szPath."app_".$szAppName);
if (defined('GEN_APPLICATION_PATH') !== true)
    define('GEN_APPLICATION_PATH', $szPath.'apps');
// Define application environment
if (defined('APPLICATION_ENV') !== true)
    define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
if (defined('PUBLIC_HTML') !== true)
    define('PUBLIC_HTML', $szPath . 'pub_superelf');
if ( defined('WEBSITE_LOCAL_PATH') !== true )
    define('WEBSITE_LOCAL_PATH', realpath( dirname(__FILE__) ).'/' );
set_include_path(
    PATH_SEPARATOR . $szPath.'ThirdParties/ZendFramework/library'
    . PATH_SEPARATOR . APPLICATION_PATH.'/library'
    . PATH_SEPARATOR . $szPath.'library'
    . PATH_SEPARATOR . get_include_path()
);
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap(); //->run();
<?php

$sPath = realpath( dirname(__FILE__)."/../../" ).'/';
$sAppName = "voetbaloog";

if ( defined('APPLICATION_NAME') !== true )
	define('APPLICATION_NAME', $sAppName );

if ( defined('APPLICATION_PATH') !== true )
	define('APPLICATION_PATH', $sPath."app_".$sAppName );

if ( defined('GEN_APPLICATION_PATH') !== true )
	define('GEN_APPLICATION_PATH', $sPath.'apps' );

// Define application environment
if ( defined('APPLICATION_ENV') !== true )
	define('APPLICATION_ENV', ( getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

set_include_path(
	PATH_SEPARATOR . $sPath.'ThirdParties/ZendFramework/library'
	. PATH_SEPARATOR . APPLICATION_PATH.'/library'
	. PATH_SEPARATOR . $sPath.'library'
	. PATH_SEPARATOR . get_include_path()
);

require_once 'Zend/Application.php';

$application = new Zend_Application( APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini' );
$application->bootstrap(); //->run();
<?php

$sPath = realpath( dirname(__FILE__)."/../../../" ).'/';
$sAppName = "voetbaloog";

if ( defined('APPLICATION_NAME') !== true )
	define('APPLICATION_NAME', $sAppName );

if ( defined('APPLICATION_PATH') !== true )
	define('APPLICATION_PATH', $sPath . "app_" . $sAppName );

if ( defined('GEN_APPLICATION_NAME') !== true )
	define('GEN_APPLICATION_NAME', "apps" );

if ( defined('GEN_APPLICATION_PATH') !== true )
	define('GEN_APPLICATION_PATH', $sPath . GEN_APPLICATION_NAME );

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

function executeTask()
{
	echo "UPDATING " . GEN_APPLICATION_NAME . PHP_EOL;
	Source_Db_Installer::update( GEN_APPLICATION_NAME );
	if( Zend_Registry::isRegistered( "dbxmllog" ) )
	{
		$arrLogs = Zend_Registry::get("dbxmllog");
		foreach( $arrLogs as $arrLog )
			echo $arrLog[1] . PHP_EOL;
	}

	echo "UPDATING " . "Voetbal" . PHP_EOL;
	Source_Db_Installer::update( "Voetbal" );
	if( Zend_Registry::isRegistered( "dbxmllog" ) )
	{
		$arrLogs = Zend_Registry::get("dbxmllog");
		foreach( $arrLogs as $arrLog )
			echo $arrLog[1] . PHP_EOL;
	}

	echo "UPDATING " . APPLICATION_NAME . PHP_EOL;
	Source_Db_Installer::update( APPLICATION_NAME );
	if( Zend_Registry::isRegistered( "dbxmllog" ) )
	{
		$arrLogs = Zend_Registry::get("dbxmllog");
		foreach( $arrLogs as $arrLog )
			echo $arrLog[1] . PHP_EOL;
	}

	echo "AUTHORIZING " . APPLICATION_NAME . PHP_EOL;
	Source_Db_Installer::setup( APPLICATION_NAME );
	if( Zend_Registry::isRegistered( "dbxmllog" ) )
	{
		$arrLogs = Zend_Registry::get("dbxmllog");
		foreach( $arrLogs as $arrLog )
			echo $arrLog[1] . PHP_EOL;
	}
}

executeTask();
?>

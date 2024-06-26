<?php

$arrParams = array();
// $arrParams["wwwdir"] = realpath( "/home/cdunnink" ).'/';
$arrParams["wwwdir"] = realpath( dirname( __FILE__ )."/../../" ).'/';

if ( defined('APPLICATION_ENV') !== true )
	define('APPLICATION_ENV', ( getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

set_include_path(
	PATH_SEPARATOR . $arrParams["wwwdir"].'ThirdParties/ZendFramework/library'
	. PATH_SEPARATOR . $arrParams["wwwdir"].'library'
	. PATH_SEPARATOR . get_include_path()
);

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace(
array( "Agenda"
		, "Construction"
		, "Controls"
		, "Import"
		, "ICalendar"
		, "MemberShip"
		, "MetaData"
		, "Object"
		, "Patterns"
		, "RAD"
		, "SOAP"
		, "Source"
		, "XML"
		, "ZendExt"
) );

$arrParams["action"] = $argv[1];
$arrParams["dbhost"] = $argv[2];
$arrParams["dbuser"] = $argv[3];
$arrParams["dbpassword"] = $argv[4];
$arrParams["dbname"] = $argv[5];
$arrParams["dbtype"] = $argv[6];

// maak source/installer en voer hier functies over uit.
if ( $arrParams["action"] === "install" )
{
	$arrParams["dbadminuser"] = $argv[7];
	$arrParams["dbadminpassword"] = $argv[8];

	Source_Db_Installer::install( $arrParams );
}
else if ( $arrParams["action"] === "deinstall_db_tables" )
{
	$arrParams["module"] = $argv[7];

	Source_Db_Installer::deInstallTables( $arrParams );
}
else if ( $arrParams["action"] === "install_db_tables" )
{
	$arrParams["module"] = $argv[7];

	Source_Db_Installer::installTables( $arrParams );
}
else if ( $arrParams["action"] === "update" )
{
	$arrParams["module"] = $argv[7];

	Source_Db_Installer::update( $arrParams );
}
else if ( $arrParams["action"] === "setup" )
{
	$arrParams["module"] = $argv[7];

	Source_Db_Installer::setup( $arrParams );
}
set_time_limit ( 60 * 10 );
<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Installer.php 4596 2020-09-28 10:54:01Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
class Source_Db_Installer
{
	public function __construct(){}

	public static function deInstallTables( $sModuleName )
	{
		$sFileName = self::getFile( $sModuleName, "deinstall" );
		self::executeXML( $sModuleName, $sFileName, false );
	}

	public static function installTables( $sModuleName )
	{
		$sFileName = self::getFile( $sModuleName, "install" );
		self::executeXML( $sModuleName, $sFileName, false );
	}

	public static function update( $sModuleName )
	{
		$sFileName = self::getFile( $sModuleName, "update" );
		self::executeXML( $sModuleName, $sFileName );
	}

	protected static function preSetup( $sModuleName )
	{
		$arrLog = array();

		$db = Zend_Registry::get("db");

		if ( strlen( $sModuleName ) === 0 ) {
			$arrLog[] = array( Zend_Log::EMERG, "geen module opgegeven" );
			return;
		}

		try
		{
			$stmt = $db->query( "SELECT Name FROM Modules WHERE Name = '".$sModuleName."';" );
			if ( $row = $stmt->fetch() ){}
			else
			{
				$sSQL = "INSERT INTO Modules( Name ) VALUES( '".$sModuleName."' );";
				$db->query( $sSQL );
			}
		}
		catch( Exception $e )
		{
			$arrLog[] = array( Zend_Log::EMERG, "module '".$sModuleName."' kon niet worden toegevoegd: " . $e->getMessage() );
		}

		$arrSQL [] = "DELETE FROM MenuItemHierarchy WHERE ParentMenuItemModuleName = '".$sModuleName."'";
		$arrSQL [] = "DELETE FROM MenuItemHierarchy WHERE ChildMenuItemModuleName = '".$sModuleName."'";
		$arrSQL [] = "DELETE FROM MenuItems WHERE ModuleName = '".$sModuleName."'";
		$arrSQL [] = "DELETE FROM Actions WHERE ModuleName = '".$sModuleName."'";

		foreach( $arrSQL as $sSQL )
		{
			try
			{
				$db->query( $sSQL );
			}
			catch( Exception $e )
			{
				echo "Databasefout opgetreden:".$e->getMessage();
				$arrLog[] = array( Zend_Log::EMERG, "autorisatie kon niet worden verwijderd: " . $e->getMessage() );
			}
		}
		$arrLog[] = array( Zend_Log::INFO, "autorisatie verwijderd" );

		return $arrLog;
	}

	public static function setup( $sModuleName )
	{
		$arrLog = static::preSetup( $sModuleName );

		$db = Zend_Registry::get("db");

		$sFileName = self::getFile( $sModuleName, "setup" );
		$oXMLModule = Source_XML_Reader::fileToSimpleXML( $sFileName );

		$oXMLRoles = null;
		if ( $oXMLModule instanceof SimpleXMLElement )
		{
			foreach ( $oXMLModule->children() as $xmlCollection )
			{
				if ( $xmlCollection->getName() === "Actions" )
				{
					$oOptions = Construction_Factory::createOptions();
					$oOptions->addFilter( "RAD_Auth_Action::Module", "EqualTo", $sModuleName );

					$oXMLActions = RAD_Auth_Action_Factory::createObjectsFromXML( $xmlCollection, $oOptions );

					$oDbActions = RAD_Auth_Action_Factory::createObjectsFromDatabase();
					$oDbWriter = RAD_Auth_Action_Factory::createDbWriter();
					$oDbActions->addObserver( $oDbWriter );

					$oDbActions->addCollection( $oXMLActions );

					try
					{
						if ( $oDbWriter->write() === true )
							$arrLog[] = array( Zend_Log::INFO, "acties weggeschreven" );
					}
					catch( Exception $e )
					{
						$arrLog[] = array( Zend_Log::EMERG, "acties konden niet worden weggeschreven: ".$e->getMessage() );
					}
				}
				else if ( $xmlCollection->getName() === "MenuItems" )
				{
					$oOptions = Construction_Factory::createOptions();
					$oOptions->addFilter( "RAD_Auth_MenuItem::Module", "EqualTo", $sModuleName );

					$oXMLMenuItem = RAD_Auth_MenuItem_Factory::createObjectFromXML( $xmlCollection, $oOptions );

					$oDbWriter = RAD_Auth_MenuItem_Factory::createDbWriter();

					try
					{
						if ( $oDbWriter->writeExt( $sModuleName, $oXMLMenuItem ) === true )
							$arrLog[] = array( Zend_Log::INFO, "menuitems weggeschreven" );
					}
					catch( Exception $e )
					{
						$arrLog[] = array( Zend_Log::EMERG, "menuitems konden niet worden weggeschreven: ".$e->getMessage() );
					}
				}
				else if ( $xmlCollection->getName() === "Roles" )
				{
					$oOptions = Construction_Factory::createOptions();
					$oOptions->addFilter( "RAD_Auth_Role::Module", "EqualTo", $sModuleName );

					$oXMLRoles = RAD_Auth_Role_Factory::createObjectsFromXML( $xmlCollection, $oOptions );

					$oDbRoles = RAD_Auth_Role_Factory::createObjectsFromDatabase();
					$oDbWriter = RAD_Auth_Role_Factory::createDbWriter();
					$oDbRoles->addObserver( $oDbWriter );

					$oDbRoles->addCollection( $oXMLRoles );

					try
					{
						if ( $oDbWriter->write() === true )
							$arrLog[] = array( Zend_Log::INFO, "rollen weggeschreven" );
					}
					catch( Exception $e )
					{
						$arrLog[] = array( Zend_Log::EMERG, "rollen konden niet worden weggeschreven: ".$e->getMessage() );
					}
				}
			}

			foreach( $oXMLRoles as $oXMLRole )
			{
				$oDbWriter = RAD_Auth_Role_Factory::createActionDbWriter( $oXMLRole );

				$oActionsTmp = RAD_Auth_Action_Factory::createObjects();
				$oActionsTmp->addObserver( $oDbWriter );

				$oActions = $oXMLRole->getActions( $sModuleName );

				$oActionsTmp->addCollection( $oActions );

				try
				{
					$oDbWriter->write();
				}
				catch( Exception $e )
				{
					$arrLog[] = array( Zend_Log::EMERG, "acties-per-rol konden niet worden weggeschreven: ".$e->getMessage() );
				}
			}
			$arrLog[] = array( Zend_Log::INFO, "acties-per-rol weggeschreven" );

			foreach( $oXMLRoles as $oXMLRole )
			{
				$oDbWriter = RAD_Auth_Role_Factory::createMenuItemDbWriter( $oXMLRole );

				$oRootMenuItem = $oXMLRole->getRootMenuItem( $sModuleName );

				try
				{
					$oDbWriter->writeExt( $sModuleName, $oRootMenuItem );
				}
				catch( Exception $e )
				{
					$arrLog[] = array( Zend_Log::EMERG, "menuitems-per-rol konden niet worden weggeschreven: ".$e->getMessage() );
				}
			}
			$arrLog[] = array( Zend_Log::INFO, "menuitems-per-rol weggeschreven" );

		}
		Zend_Registry::set("dbxmllog", $arrLog );
	}

	protected static function executeXML( $sModuleName, $sFileName, $bUseConfig = true )
	{
		Source_Db_Object_Factory::toggleTablePool( false );

		$oDatabase = Zend_Registry::get("db");

		$oXMLDb = Source_XML_Reader::fileToSimpleXML( $sFileName );

		$nDbType = Source_Db_SqlSyntaxFactory::getDbType( $oDatabase );

		$arrLog = array();

		if ( !( $oXMLDb instanceof SimpleXMLElement ) )
			return;

		try
		{
			$oConfigIni = null;
            $sConfigFile = null;
			if ( $bUseConfig === true )
			{
				// Kijk welke versie er in het config bestand staat
				$sConfigFile = self::getFile( $sModuleName, "config" );
				$oConfigIni = new Zend_Config_Ini( $sConfigFile, null, array( "allowModifications" => true) );
			}

			if( $oConfigIni === null ) {
			    throw new \Exception("config.ini could not be read", E_ERROR);
            } else if( $oConfigIni->get("application") === null ) {
                throw new \Exception("application section of config.ini could not be read", E_ERROR);
            }
            $applicationVersion = $oConfigIni->get("application")->version;

			foreach ( $oXMLDb->children() as $xmlCollection )
			{
				if ( $xmlCollection->getName() === "Additions" )
				{
					$sVersionXML = (string) $xmlCollection->Version;
					//echo $sVersionXML.PHP_EOL;

					if ( $applicationVersion >= $sVersionXML ) {
						continue;
                    }

					try
					{
						foreach ( $xmlCollection->children() as $xmlAddition )
						{
							$vtSQL = static::getSQL( $xmlAddition, $nDbType, $oDatabase );
							if ( $vtSQL === null )
								continue;


							if ( is_array( $vtSQL ) )
							{
								foreach ( $vtSQL as $sSQL )
								{
									if ( constant("APPLICATION_ENV") === "development" and defined("APPLICATION_NODEBUG") !== true )
									{
                                        if ( PHP_SAPI !== "cli" ) {
                                            var_dump( $sSQL . PHP_EOL );
                                        }
                                        else {
                                            echo $sSQL . PHP_EOL;
                                        }
									}
									if ( strlen( $sSQL ) > 0 )
										$oDatabase->query( $sSQL );
								}
							}
							else
							{
								if ( constant("APPLICATION_ENV") === "development" and defined("APPLICATION_NODEBUG") !== true ) {
								    if ( PHP_SAPI !== "cli" ) { var_dump( $vtSQL . PHP_EOL ); }
                                    else { echo $vtSQL . PHP_EOL; }
                                }
								if ( strlen( $vtSQL ) > 0 )
									$oDatabase->query( $vtSQL );
							}
						}

						// if ( $oConfigIni !== null ) {
							$oConfigIni->application->version = $sVersionXML;
							$arrCon = array( "config" => $oConfigIni, "filename" => $sConfigFile );
							$oConfigWriter = new Zend_Config_Writer_Ini( $arrCon );
							$oConfigWriter->write();
							$arrLog[] = array( Zend_Log::INFO, "Versie update ".$sModuleName." naar:".$sVersionXML );
//						} else {
//							$arrLog[] = array( Zend_Log::INFO, "xml voor ".$sModuleName." uitgevoerd" );
//						}
					}
					catch( Exception $e )
					{
						$sMessage = "Fout opgetreden tijdens wegschrijven updates:".$e->getMessage();

						if ( $nDbType === Source_Db_SqlSyntaxFactory::MSSQL AND APPLICATION_ENV == 'development' )
						{
							if ( file_exists ( sys_get_temp_dir() . DIRECTORY_SEPARATOR . "freetds.log" ) === true ) {
								$sMessage .= " :: " . Source_Db_Log::tail( sys_get_temp_dir() . DIRECTORY_SEPARATOR . "freetds.log", "msgno" );
							}
						}
						$arrLog[] = array( Zend_Log::EMERG, $sMessage );
					}
				}
			}
		}
		catch ( Exception $e )
		{
			$arrLog[] = array( Zend_Log::EMERG, "Fout opgetreden tijdens inlezen ini-file sectie application:".$e->getMessage() );
		}
		Source_Db_Object_Factory::getTables()->flush();
		Source_Db_Object_Factory::toggleTablePool( true );

		Zend_Registry::set("dbxmllog", $arrLog );
	}

	/**
	 * converts xml to sql-string
	 *
	 * @param SimpleXMLElement $xmlSQL
	 * @param int $nDbType
	 * @return string|array|null
	 */
	public static function getSQL( $xmlSQL, $nDbType, $oDatabase = null )
	{
		$sSQLAction = (string) $xmlSQL->Name;

		if ( strlen( $sSQLAction ) === 0 )
			return null;

		$vtSQL = null;
		{
			if ( $sSQLAction === "QueryExecute" )
			{
				$vtSQL = Source_Db_Object_Factory::createQueryFromXML( $xmlSQL->queries );
			}
			else
			{
				$oTable = Source_Db_Object_Factory::createTableFromXML( $xmlSQL->table );
				if ( $sSQLAction === "TableAdd" )
				{
					$vtSQL = Source_Db_SqlSyntaxFactory::getCreateTableStatement( $nDbType, $oTable );
				}
				else if ( $sSQLAction === "TableRemove" )
				{
					if ( $oDatabase !== null and static::tableExists( $oDatabase, $oTable ) === false )
						return null;
					$vtSQL = Source_Db_SqlSyntaxFactory::getDeleteTableStatement( $nDbType, $oTable );
				}
				else if ( (string) $xmlSQL->Name === "ColumnAdd" )
				{
					$vtSQL = (string) Source_Db_SqlSyntaxFactory::getCreateColumnStatement( $nDbType, $oTable );
				}
				else if ( $sSQLAction === "ColumnUpdate" )
				{
					$vtSQL = Source_Db_SqlSyntaxFactory::getUpdateColumnStatement( $nDbType, $oTable );
				}
				else if ( $sSQLAction === "ColumnRemove" )
				{
					$vtSQL = Source_Db_SqlSyntaxFactory::getDeleteColumnStatement( $nDbType, $oTable );
				}
				else if ( $sSQLAction === "IndexAdd" )
				{
					$vtSQL = Source_Db_SqlSyntaxFactory::getCreateIndicesStatement( $nDbType, $oTable );
				}
				else if ( $sSQLAction === "IndexRemove" )
				{
					$vtSQL = Source_Db_SqlSyntaxFactory::getDeleteIndicesStatement( $nDbType, $oTable );
				}
				else if ( $sSQLAction === "KeyAdd" )
				{
					$vtSQL = Source_Db_SqlSyntaxFactory::getCreateKeysStatement( $nDbType, $oTable );
				}
				else if ( $sSQLAction === "KeyRemove" )
				{
					$vtSQL = Source_Db_SqlSyntaxFactory::getDeleteKeysStatement( $nDbType, $oTable );
				}
			}
		}
		return $vtSQL;
	}

	protected static function tableExists( $oDatabase, $oTable )
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();

		$nDisplayErrors = ini_get( "display_errors" );
		ini_set( "display_errors", 0 );
		$autoloader->suppressNotFoundWarnings( true );

		$bRetVal = true;

		$nDbType = Source_Db_SqlSyntaxFactory::getDbType( $oDatabase );

		$sSQL = Source_Db_SqlSyntaxFactory::getExistsTableStatement( $nDbType, $oTable );
		try
		{
			$stmt = $oDatabase->query( $sSQL );
			$row = $stmt->fetch();
		}
		catch( Exception $e )
		{
			$bRetVal = false;
		}

		$autoloader->suppressNotFoundWarnings( false );
		ini_set( "display_errors", $nDisplayErrors );

		return $bRetVal;
	}

	protected static function getFile( $sModule, $sAction )
	{

		$sDir = realpath( dirname( __FILE__ )."/../../../" )."/";
		if ( $sModule === "apps" )
		{
			if ( $sAction === "deinstall" )
				return $sDir.$sModule."/model/installer/db_remove.xml";
			else if ( $sAction === "install" )
				return $sDir.$sModule."/model/installer/db_install.xml";
			else if ( $sAction === "update" )
				return $sDir.$sModule."/model/installer/db_update.xml";
			else if ( $sAction === "config" )
				return $sDir.$sModule."/configs/config.ini";
		}
		else if ( $sModule === "Voetbal" )
		{
			if ( $sAction === "deinstall" )
				return $sDir."library/".$sModule."/Db/remove.xml";
			else if ( $sAction === "install" )
				return $sDir."library/".$sModule."/Db/install.xml";
			else if ( $sAction === "update" )
				return $sDir."library/".$sModule."/Db/update.xml";
			else if ( $sAction === "config" )
				return $sDir."library/".$sModule."/config.ini";
		}

		if ( $sAction === "deinstall" )
			return $sDir."app_".$sModule."/model/installer/db_remove.xml";
		else if ( $sAction === "install" )
			return $sDir."app_".$sModule."/model/installer/db_install.xml";
		else if ( $sAction === "update" )
			return $sDir."app_".$sModule."/model/installer/db_update.xml";
		else if ( $sAction === "setup" )
			return $sDir."app_".$sModule."/model/installer/setup.xml";
		else if ( $sAction === "config" )
			return $sDir."app_".$sModule."/configs/config.ini";

		throw new Exception( "combination module-action not found", E_ERROR );
	}
}

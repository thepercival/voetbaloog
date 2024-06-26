<?php

/**
 *
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Factory.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 *
 * @package    Auth
 */

/**
 *
 * @package    Auth
 */
class RAD_Auth_MenuItem_Factory extends Object_Factory_Db implements RAD_Auth_MenuItem_Factory_Interface
{
	protected static $m_objSingleton;

    /**
	 * Call parent
	 */
    protected function __construct(){ parent::__construct(); }

	public static function createObjects()
	{
		throw new Exception(__FILE__."Not implemented",E_ERROR);
	}

	public static function createObjectFromDatabase( $oOptions = null )
	{
		throw new Exception(__FILE__."Not implemented",E_ERROR);
	}

	public static function createObjectsFromDatabase( $oOptions = null )
	{
		throw new Exception(__FILE__."Not implemented",E_ERROR);
	}

	public static function createObjectFromXML( $objXML, $oOptions = null )
	{
		$objReader = self::createXMLReader( $objXML );
		return $objReader->createObject( $oOptions );
	}

	public static function createXMLReader( $objXML )
	{
		$objXMLReader = new RAD_Auth_MenuItem_XML_Reader( self::getInstance() );
		$objXMLReader->putSource( $objXML );
		return $objXMLReader;
	}

	/**
	 * Defined by RAD_Auth_MenuItem_Factory_Interface; gets the root menu item from the database
	 *
	 * @see RAD_Auth_MenuItem_Factory_Interface::getRootMenuItem()
	 *
	 */
	public static function getRootMenuItem( $objRoles, $szModuleName, $szMenuItemName = "root" )
	{
		$objDbReader = self::createDbReader();

		self::checkRoles( $objRoles );

		return $objDbReader->getMenuItems( $objRoles, $szModuleName, $szMenuItemName );
	}

	/**
	 * Defined by RAD_Auth_MenuItem_Factory_Interface; gets the root menu item from the database for the default user
	 *
	 @see RAD_Auth_MenuItem_Factory_Interface::getDefaultMenuItem()
	 */
	public static function getDefaultMenuItem( $szModuleName, $szMenuItemName = "root" )
	{
		$objRoles = RAD_Auth_Role_Factory::createObjects();
		self::checkRoles( $objRoles );
		$objDbReader = self::createDbReader();
		return $objDbReader->getMenuItems( $objRoles, $szModuleName, $szMenuItemName );
	}

	private static function checkRoles( $objRoles )
	{
		if ( $objRoles !== null and $objRoles->count() === 0 )
		{
			$objRoles->add( RAD_Auth_Role_Factory::getGuest() );
		}
	}
}
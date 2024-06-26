<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Factory.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 *
 * @package    Auth
 */

/**
 * @package Auth
 */
class RAD_Auth_Role_Factory extends Object_Factory_Db implements Object_Factory_Db_Ext_Interface
{
	protected static $m_objSingleton;
	protected static $m_oGuest;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see Object_Factory_Db_Ext_Interface::createObjectsFromDatabaseExt()
	 */
    public static function createObjectsFromDatabaseExt( $oObject, Construction_Option_Collection $oOptions = null, string $sClassName = null ): Patterns_Collection
	{
		return static::createDbReader()->createObjectsExt( $oObject, $oOptions, $sClassName );
	}

	/**
	 * @see RAD_Auth_Role_Factory_Interface::getGuest()
	 */
	public static function getGuest()
	{
		if ( static::$m_oGuest === null )
		{
			static::$m_oGuest = static::createObject();
			static::$m_oGuest->putId( "guest" );
			static::$m_oGuest->putSystem( true );
			// actions are set in RAD_Auth
		}
		return static::$m_oGuest;
	}

	/**
	 * @see RAD_Auth_Role_Factory_Interface::createMenuItemDbWriter()
	 */
	public static function createMenuItemDbWriter( $oRole )
	{
		return new RAD_Auth_Role_Db_MenuItemWriter( $oRole, self::getInstance() );
	}

	/**
	 * @see RAD_Auth_Role_Factory_Interface::createUserDbWriter()
	 */
	public static function createUserDbWriter( $oUser )
	{
		return new RAD_Auth_Role_Db_UserWriter( $oUser, self::getInstance() );
	}

	/**
	 * @see RAD_Auth_Role_Factory_Interface::createActionDbWriter()
	 */
	public static function createActionDbWriter( $oRole )
	{
		return new RAD_Auth_Role_Db_ActionWriter( $oRole, self::getInstance() );
	}

	public static function createObjectsFromXML( $objXML, $oOptions = null )
	{
		$objReader = self::createXMLReader( $objXML );
		return $objReader->createObjects( $oOptions );
	}

	public static function createXMLReader( $objXML )
	{
		$objXMLReader = new RAD_Auth_Role_XML_Reader( self::getInstance() );
		$objXMLReader->putSource( $objXML );
		return $objXMLReader;
	}
}

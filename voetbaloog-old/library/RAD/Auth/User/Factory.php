<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Factory.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
class RAD_Auth_User_Factory extends Object_Factory_Db implements RAD_Auth_User_Factory_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see Object_Factory_Db_Interface::createObjectFromDatabase()
	 */
	public static function createObjectFromDatabase( $vtOptions = null )
	{
		if ( $vtOptions !== null and is_string( $vtOptions ) )
		{
			$sName = $vtOptions;
			$vtOptions = Construction_Factory::createOptions();
			$vtOptions->addFilter( "RAD_Auth_User::Name", "EqualTo", $sName );
		}
		return parent::createObjectFromDatabase( $vtOptions );
	}

	/**
	 * @see RAD_Auth_User_Factory_Interface::createObjectsForRoleFromDatabase()
	 */
	public static function createObjectsForRoleFromDatabase( $oRole, $oOptions = null )
	{
		return static::createDbReader()->createObjectsForRole( $oRole, $oOptions );
	}
}
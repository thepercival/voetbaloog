<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package		VoetbalOog
 */
class VoetbalOog_User_Factory extends Object_Factory_Db_JSON implements Object_Factory_Db_Ext_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see Object_Factory_Interface::createObjects()
	 */
	public static function createObjects()
	{
		return RAD_Auth_User_Factory::createObjects();
	}

	/**
	 * @see Object_Factory_Db_Interface::createObjectFromDatabase()
	 */
	public static function createObjectFromDatabase( $vtOptions = null )
	{
		if ( $vtOptions !== null and is_string( $vtOptions ) )
		{
			$sName = $vtOptions;
			$vtOptions = Construction_Factory::createOptions();
			$vtOptions->addFilter( "VoetbalOog_User::Name", "EqualTo", $sName );
		}
		return parent::createObjectFromDatabase( $vtOptions );
	}

	/**
	 * @see Object_Factory_Db_Ext_Interface::createObjectsFromDatabaseExt()
	 */
    public static function createObjectsFromDatabaseExt( $oObject, Construction_Option_Collection $oOptions = null, string $sClassName = null ): Patterns_Collection
	{
		return static::createDbReader()->createObjectsExt( $oObject, $oOptions, $sClassName );
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON()
	 */
	public static function convertObjectToJSON( $oObject, $nDataFlag = null )
	{
		if ( $oObject === null )
			return "null";

		if ( static::isInPoolJSON( $oObject ) )
			return "\"" . $oObject->getId() . "\"";
		static::addToPoolJSON( $oObject );

		$sJSON =
		"{".
			"\"Id\":".$oObject->getId().",".
			"\"Name\":\"".$oObject->getName()."\""
		;

		return $sJSON."}";
	}
}
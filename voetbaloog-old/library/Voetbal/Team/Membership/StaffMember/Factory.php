<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 617 2013-12-11 10:41:56Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Team_Membership_StaffMember_Factory extends Object_Factory_Db implements Voetbal_Team_Membership_Factory_Interface
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
		return MemberShip_Factory::createObjects();
	}

	/**
	* @see Voetbal_Team_Membership_Factory_Interface::getPicture()
	*/
	public static function getPicture( $nId )
	{
		return static::createDbReader()->getPicture( $nId );
	}

	/**
	* @see JSON_Factory_Interface::convertObjectToJSON()
	*/
	/*
	public static function convertObjectToJSON( $oObject, $nDataFlag = null )
	{
		if ( $oObject === null )
		return "null";

		if ( static::isInPoolJSON( $oObject ) )
		return $oObject->getId();
		static::addToPoolJSON( $oObject );

		$nJSTimeStamp = $oObject->getDateOfBirth()->getTimeStamp() * 1000;

		return
			"{
				\"Id\": ".$oObject->getId().",
				\"FullName\": \"".$oObject->getFullName()."\",
				\"DateOfBirth\": ".$nJSTimeStamp.",
				\"Team\": ".Voetbal_Team_Factory::convertObjectToJSON( $oObject->getTeam(), $nDataFlag ).",
				\"Inactive\": ".( $oObject->getInactive() ? "true" : "false" )."
				\"FunctionX\": \"".$oObject->getFunctionX()."\",
				\"Importance\": ".$oObject->getImportance()."
			}";
	}
	*/
}
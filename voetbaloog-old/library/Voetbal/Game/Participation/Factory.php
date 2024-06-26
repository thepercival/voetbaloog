<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 991 2015-01-23 15:37:48Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Game_Participation_Factory extends Object_Factory_Db_JSON implements Voetbal_Game_Participation_Factory_Interface, Object_Factory_Db_Ext_Interface
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
		return new Patterns_ObservableObject_Collection_Idable();
	}

	/**
	 * @see Object_Factory_Db_Ext_Interface::createObjectsFromDatabaseExt()
	 */
    public static function createObjectsFromDatabaseExt( $oObject, Construction_Option_Collection $oOptions = null, string $sClassName = null ): Patterns_Collection
	{
		return static::createDbReader()->createObjectsExt( $oObject, $oOptions, $sClassName );
	}

	/**
	 * @see Voetbal_Game_Participation_Factory_Interface::getDetails()
	 */
	public static function getDetails( Construction_Option_Collection $oOptions, Voetbal_Team_Membership_Player $oPlayerMembership, bool $bTotals ): Patterns_Collection
	{
		$oDetails = null;
		/** @var Voetbal_Game_Participation_Db_Reader $oDbReader */
		$oDbReader = static::createDbReader();
		if ( $bTotals === true )
			$oDetails = $oDbReader->getDetailsTotals( $oPlayerMembership, $oOptions );
		else
			$oDetails = $oDbReader->getDetails( $oPlayerMembership, $oOptions );
		return $oDetails;
	}

	/**
	* @see JSON_Factory_Interface::convertObjectToJSON()
	*/
	public static function convertObjectToJSON( $oObject, $nDataFlag = null )
	{
		if ( $oObject === null )
			return "null";

		if ( static::isInPoolJSON( $oObject ) )
			return $oObject->getId();
		static::addToPoolJSON( $oObject );

		$sJSON =
			"{
				\"Id\": ".$oObject->getId().",
				\"Game\": ".Voetbal_Game_Factory::convertObjectToJSON( $oObject->getGame(), $nDataFlag ).",
				\"TeamMembershipPlayer\": ".Voetbal_Team_Membership_Player_Factory::convertObjectToJSON( $oObject->getTeamMembershipPlayer(), $nDataFlag )."
			";

		return $sJSON."}";
	}
}
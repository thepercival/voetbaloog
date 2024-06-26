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
class Voetbal_Goal_Factory extends Object_Factory_Db_JSON implements Voetbal_Goal_Factory_Interface
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
	 * @see Voetbal_Goal_Factory_Interface::createHomeAwayFilters()
	 */
	public static function createHomeAwayFilters( $oTeam, $oTeamVersus )
	{
		$oOptionsOr = Construction_Factory::createOptions();
		$oOptionsOr->putId( "__OR__".$oTeam->getId()."__".$oTeamVersus->getId()."__" );
		$oOptionsOrAnd = Construction_Factory::createOptions();
		$oOptionsOrAnd->putId( $oOptionsOr->getId() . "AND__" );
		$oOptionsOrAnd->addFilter("Voetbal_Game_Participation::Team", "EqualTo", $oTeam );
		$oOptionsOrAnd->addFilter("Voetbal_Goal::OwnGoal", "EqualTo", false );
		$oOptionsOr->add( $oOptionsOrAnd );
		$oOptionsOrAndOwn = Construction_Factory::createOptions();
		$oOptionsOrAndOwn->putId( $oOptionsOr->getId() . "AND__OWN__" );
		$oOptionsOrAndOwn->addFilter("Voetbal_Game_Participation::Team", "EqualTo", $oTeamVersus );
		$oOptionsOrAndOwn->addFilter("Voetbal_Goal::OwnGoal", "EqualTo", true );
		$oOptionsOr->add( $oOptionsOrAndOwn );
		return $oOptionsOr;

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
				\"GameParticipation\": ".Voetbal_Game_Participation_Factory::convertObjectToJSON( $oObject->getGameParticipation(), $nDataFlag ).",
				\"Minute\": ".$oObject->getMinute().",
				\"OwnGoal\": ".( $oObject->getOwnGoal() ? "true" : "false" ).",
				\"Penalty\": ".( $oObject->getPenalty() ? "true" : "false" )."
				\"AssistGameParticipation\": ".Voetbal_Game_Participation_Factory::convertObjectToJSON( $oObject->getAssistGameParticipation(), $nDataFlag ).",
			";

		return $sJSON."}";
	}
}
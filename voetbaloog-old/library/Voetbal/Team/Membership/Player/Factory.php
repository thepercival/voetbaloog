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
class Voetbal_Team_Membership_Player_Factory extends Object_Factory_Db_JSON implements Voetbal_Team_Membership_Factory_Interface
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
     * @see Voetbal_Team_Membership_Player_Factory_Interface::getAvailableBackNumbers( )
     */
    public static function getAvailableBackNumbers( $oTeam, $vtDateTimeSlot )
    {
        $oBackNumbers = Patterns_Factory::createNumbers(1, Voetbal_Team_Membership_Player::MAX_BACKNUMBER);

        $oOptions = Construction_Factory::createFiltersForTimeSlots("Voetbal_Team_Membership_Player", $vtDateTimeSlot, Agenda_TimeSlot::EXCLUDE_NONE, true);
        $oOptions->addFilter("Voetbal_Team_Membership_Player::Provider", "EqualTo", $oTeam );
        $oPlayerMemberships = Voetbal_Team_Membership_Player_Factory::createObjectsFromDatabase($oOptions);

        foreach ($oPlayerMemberships as $oPlayerMembership ){
			$vtPlayerbackNumber = $oPlayerMembership->getBackNumber();
			if ( $vtPlayerbackNumber instanceof Patterns_Idable_Interface )
				$vtPlayerbackNumber = $vtPlayerbackNumber->getId();
			$oBackNumber = $oBackNumbers[ $vtPlayerbackNumber ];
			if ( $oBackNumber !== null )
            	$oBackNumbers->remove( $oBackNumber );
		}

        return $oBackNumbers;
    }

	public static function getDefaultPlayerPeriodTimeSlot( $oGame )
	{
		$oCompetitionSeason = $oGame->getCompetitionSeason();
//		$oStartDateTimeTmp = null;
//		{
//			$oOptions = Construction_Factory::createOptions();
//			$oOptions->addFilter("Voetbal_Round::CompetitionSeason", "EqualTo", $oCompetitionSeason );
//			$oOptions->addFilter("Voetbal_Game::ValidatedDateTime", "EqualTo", null );
//			$oOptions->addOrder("Voetbal_Game::StartDateTime", false);
//			$oOptions->addLimit(1);
//			$oEldestInvalidGame = Voetbal_Game_Factory::createObjectFromDatabase($oOptions);
//			if ($oEldestInvalidGame !== null)
//				$oStartDateTimeTmp = $oEldestInvalidGame->getStartDateTime();

//			if ( $oStartDateTimeTmp === null or $oStartDateTimeTmp > $oGame->getStartDateTime() )
				$oStartDateTimeTmp = $oGame->getStartDateTime();
//		}
		$oStartDateTime = Agenda_Factory::createDateTime( $oStartDateTimeTmp->toString( Agenda_DateTime::STR_SQLDATETIME ) );
		return Agenda_Factory::createTimeSlotNew( $oStartDateTime, $oCompetitionSeason->getSeason()->getEndDateTime() );
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
				\"Client\": ".Voetbal_Person_Factory::convertObjectToJSON( $oObject->getClient(), $nDataFlag ).",
				\"Provider\": ".Voetbal_Team_Factory::convertObjectToJSON( $oObject->getProvider(), $nDataFlag ).",
				\"BackNumber\": ".$oObject->getBackNumber()."
			";

		return $sJSON."}";
	}
}
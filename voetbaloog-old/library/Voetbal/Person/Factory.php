<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 955 2014-09-15 16:08:29Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Person_Factory extends Object_Factory_Db_JSON implements Voetbal_Person_Factory_Interface, Object_Factory_Db_Ext_Interface
{
	protected static $m_objSingleton;
    protected static $m_arrNameInsertions = array( "van", "der", "de", "den", "te", "ten", "ter", "Van", "Der", "De", "Den", "Te", "Ten", "Ter" );

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

    /**
     * @param Voetbal_Extern_Person $oExternPerson
     * @return Voetbal_Person|null
     */
    public static function createObjectFromDatabaseByExtern( Voetbal_Extern_Person $oExternPerson  ): ?Voetbal_Person
    {
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter("Voetbal_Person::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix . $oExternPerson->getId() );
        return static::createDbReader()->createObjects( $oOptions )->first();
    }

	/**
	 * @see Object_Factory_Db_Ext_Interface::createObjectsFromDatabaseExt()
	 */
    public static function createObjectsFromDatabaseExt( $oObject, Construction_Option_Collection $oOptions = null, string $sClassName = null ): Patterns_Collection
	{
		return static::createDbReader()->createObjectsExt( $oObject, $oOptions, $sClassName );
	}

	/**
	* @see Voetbal_Person_Factory_Interface::getTopscorers()
	*/
	public static function getTopscorers( Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		$oDbReader = static::createDbReader();
		return $oDbReader->getTopscorers( $oOptions );
	}

    /**
     * @see Voetbal_Person_Factory_Interface::isNameInsertion()
     */
    public static function isNameInsertion( $sNameInsertion )
    {
        return in_array( $sNameInsertion, static::$m_arrNameInsertions );
    }

    /**
     * @see Voetbal_Person_Factory_Interface::getNameParts()
     */
    public static function getNameParts( $sName )
    {
        $sFirstName = ""; $sNameInsertions = ""; $sLastName = "";
        $arrNameParts = explode( " ", str_replace(".", "", $sName ) );

        for( $nI = 0 ; $nI < count( $arrNameParts ) ; $nI++ )
        {
            if ( $nI === 0 and count( $arrNameParts ) > 1)
                $sFirstName = $arrNameParts[$nI];
            else if ( $nI < ( count( $arrNameParts ) - 1 ) )
            {
                if ( in_array( $arrNameParts[$nI], static::$m_arrNameInsertions ) === true )
                {
                    if ( $sNameInsertions !== "" )
                        $sNameInsertions .= " ";
                    $sNameInsertions .= $arrNameParts[$nI];
                }
                else
                {
                    if ( $sLastName !== "" )
                        $sLastName .= " ";
                    $sLastName .= $arrNameParts[$nI];
                }
            }
            else if ( $nI === ( count( $arrNameParts ) - 1 ) )
            {
                if ( $sLastName !== "" )
                    $sLastName .= " ";
                $sLastName .= $arrNameParts[$nI];
            }
        }

        return array( $sFirstName, $sNameInsertions, $sLastName );
    }

    /**
     * Wanneer een person 2 playermemberships heeft voor 1 club zonder dat hij in de tussentijd bij een
     * andere club heeft gezeten, dan mogen deze 2 samengevoegd worden.
     * Er mogen dan geen wedstrijden zijn gespeeld in de tussentijd
     * en de linies mogen niet verschillen
     * @param Voetbal_Person $oPerson
     */
    public static function mergePlayerMemberships( $oPerson )
    {
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "Voetbal_Team_Membership_Player::Client", "EqualTo", $oPerson );
        $oOptions->addOrder( "Voetbal_Team_Membership_Player::StartDateTime", true );
        $oOptions->addLimit( 2 );
        $oPlayerMemberships = Voetbal_Team_Membership_Player_Factory::createObjectsFromDatabase( $oOptions );
        if ( $oPlayerMemberships->count() !== 2 ) {
            return false;
        }

        $oMostRecentPlayerMembership = $oPlayerMemberships->first();
        $oOldestPlayerMembership = $oPlayerMemberships->getIteratorReversed()->current();

        if ( $oMostRecentPlayerMembership->getProvider() !== $oOldestPlayerMembership->getProvider() ) {
            return false;
        }

        if ( $oMostRecentPlayerMembership->getLine() !== $oOldestPlayerMembership->getLine() ) {
            return false;
        }

        $oTimeslotBetweenPlayerMemberships = Agenda_Factory::createTimeSlotNew( $oOldestPlayerMembership->getEndDateTime(),$oMostRecentPlayerMembership->getStartDateTime());
        if ( $oTimeslotBetweenPlayerMemberships === null )
        	return false;


	    $oOptions = Construction_Factory::createOptions();
	    $oOptions->addFilter( "Voetbal_Game::StartDateTime", "GreaterThan", $oTimeslotBetweenPlayerMemberships->getStartDateTime() );
	    $oOptions->addFilter( "Voetbal_Game::StartDateTime", "SmallerThan", $oTimeslotBetweenPlayerMemberships->getEndDateTime() );
	    if ( Voetbal_Game_Factory::getNrOfObjectsFromDatabaseExt( $oMostRecentPlayerMembership->getProvider(), $oOptions, "Voetbal_Team" ) > 0 )
        	return false;

        $oGameParticipationDbWriter = Voetbal_Game_Participation_Factory::createDbWriter();
        $oPlayerDbWriter = Voetbal_Team_Membership_Player_Factory::createDbWriter();

        // put startdatetime
        $oMostRecentPlayerMembership->addObserver( $oPlayerDbWriter );
        $oMostRecentPlayerMembership->putStartDateTime( $oOldestPlayerMembership->getStartDateTime() );

        // find correct backnumber which is available for the new period
        $vtDateTimeSlot = Agenda_Factory::createTimeSlot($oOldestPlayerMembership->getStartDateTime(), $oMostRecentPlayerMembership->getEndDateTime() );
        $oBackNumbers = Voetbal_Team_Membership_Player_Factory::getAvailableBackNumbers( $oMostRecentPlayerMembership->getProvider(), $vtDateTimeSlot );
        $nBackNumber = null;
        if ( $oBackNumbers->count() > 0 )
            $nBackNumber = $oBackNumbers->first();
        if ( $nBackNumber === null )
            return false;

        $oMostRecentPlayerMembership->putBackNumber( $nBackNumber );
        /* commented because line should be gotten from external source again */
//	    if ( $oMostRecentPlayerMembership->getLine() === null and $oOldestPlayerMembership->getLine() !== null ) {
//		    $oMostRecentPlayerMembership->putLine( $oOldestPlayerMembership->getLine() );
//	    }

        // update all games with playerperiod $oOldestPlayerMembership->getId();
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "Voetbal_Game_Participation::TeamMembershipPlayer", "EqualTo", $oOldestPlayerMembership );
        $oGameParticipations = Voetbal_Game_Participation_Factory::createObjectsFromDatabase( $oOptions );
        foreach( $oGameParticipations as $oGameParticipation ){
            $oGameParticipation->addObserver( $oGameParticipationDbWriter );
            $oGameParticipation->putTeamMembershipPlayer( $oMostRecentPlayerMembership );
        }

        // remove old membership
        $oPlayerMemberships->addObserver( $oPlayerDbWriter );
        $oPlayerMemberships->remove( $oOldestPlayerMembership );

        try
        {
            $oGameParticipationDbWriter->write();
            $oPlayerDbWriter->write();
            return true;
        }
        catch( Exception $e )
        {
            throw new Exception( "het mergen van 2 spelersperioden is niet gelukt : " . $e->getMessage(), E_ERROR );
        }
        return false;
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
				\"FullName\": \"".$oObject->getFullName()."\",
				\"NrOfGoalsTmp\": ".$oObject->getNrOfGoalsTmp()."
			";

		return $sJSON."}";
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON2()
	 */
	public static function convertObjectToJSON2( $oObject, $nDataFlag = null )
	{
		if ( $oObject === null )
			return null;

		if ( static::isInPoolJSON( $oObject ) )
			return $oObject->getId();
		static::addToPoolJSON( $oObject );

		return array(
			"id" => $oObject->getId(),
			"fullname" => $oObject->getFullName(),
			"nrofgoalstmp" => $oObject->getNrOfGoalsTmp()
		);
	}
}
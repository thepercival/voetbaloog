<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 20-12-15
 * Time: 10:04
 */

class Voetbal_Command_Handler_ValidatePerson
{
    public function handle( Voetbal_Command_ValidatePerson $command)
    {
        $arrMessages = array();

        $oPerson = $command->getPerson();

        // check if person lastname > 1 character
        if ( strlen( $oPerson->getLastNameCalled() ) <= 1 )
            $arrMessages[] = "persoon ".$oPerson->getFullName()." heeft een achternaam met minder dan 2 karakters";

        // check if person firstname > 1 character
        if ( strlen( $oPerson->getFirstName() ) <= 1 )
            $arrMessages[] = "persoon ".$oPerson->getFullName()." heeft een voornaam met minder dan 2 karakters";

        // check if all playerperiods have correct lines
        $oPlayers = $oPerson->getPlayerMemberships();
        foreach( $oPlayers as $oPlayer )
        {
            if ( $oPlayer->getLine() === null or Voetbal_Team_Factory::getAvailableLines()[ $oPlayer->getLine()->getId() ] === null )
                $arrMessages[] = "persoon ".$oPerson->getFullName()." heeft een spelersperiode(".$oPlayer->getStartDateTime()->toString( Agenda_DateTime::STR_SQLDATE )." tot ".( $oPlayer->getEndDateTime() !== null ? $oPlayer->getEndDateTime()->toString( Agenda_DateTime::STR_SQLDATE ) : null ).") bij team ".$oPlayer->getProvider()->getName()." zonder linie";
        }

        // check if playerperiods have no other playerperiod(other persons) in same timespan for same team with same backnumber
        $oPlayers = $oPerson->getPlayerMemberships();
        foreach( $oPlayers as $oPlayer )
        {
            $oOptions = MemberShip_Factory::getMembershipFilters("Voetbal_Team_Membership_Player",$oPlayer->getProvider(), null, $oPlayer );
            $oOptions->addFilter( "Voetbal_Team_Membership_Player::BackNumber", "EqualTo", $oPlayer->getBackNumber() );
            $oOptions->addFilter( "Voetbal_Team_Membership_Player::Id", "NotEqualTo", $oPlayer );
            $oSameBackNumberPlayers = Voetbal_Team_Membership_Player_Factory::createObjectsFromDatabase( $oOptions );

            foreach( $oSameBackNumberPlayers as $oSameBackNumberPlayer )
                $arrMessages[] = "persoon ".$oPerson->getFullName()." heeft spelersperioden met gelijke rugnummers(".$oPlayer->getBackNumber()."): ".$oPlayer->getProvider()->getName().",".$oPlayer->getStartDateTime()->toString( Agenda_DateTime::STR_SQLDATE )." => ".( $oPlayer->getEndDateTime() !== null ? $oPlayer->getEndDateTime()->toString( Agenda_DateTime::STR_SQLDATE ) : null )." met persoon ".$oSameBackNumberPlayer->getClient()->getFullName()." ".$oSameBackNumberPlayer->getProvider()->getName().",".$oSameBackNumberPlayer->getStartDateTime()->toString( Agenda_DateTime::STR_SQLDATE )." => ".( $oSameBackNumberPlayer->getEndDateTime() !== null ? $oSameBackNumberPlayer->getEndDateTime()->toString( Agenda_DateTime::STR_SQLDATE ) : null );
        }

        // check if playerperiods have no startdatetime after endtime and vice versa
        $oPlayers = $oPerson->getPlayerMemberships();
        foreach( $oPlayers as $oPlayer )
        {
            if ( $oPlayer->getEndDateTime() !== null and $oPlayer->getEndDateTime() <= $oPlayer->getStartDateTime() ) {
                $arrMessages[] = "persoon ".$oPerson->getFullName()." heeft een spelersperiode(".$oPlayer->getStartDateTime()->toString( Agenda_DateTime::STR_SQLDATE )." tot ".( $oPlayer->getEndDateTime() !== null ? $oPlayer->getEndDateTime()->toString( Agenda_DateTime::STR_SQLDATE ) : null ).") bij team ".$oPlayer->getProvider()->getName()." waarbij de einddatum gelijk is aan of voor de startdatum ligt";
            }
        }

        // check if there are overlappng playerperiods
        $oPlayers = $oPerson->getPlayerMemberships();
        $oPlayersToCheck = $oPerson->getPlayerMemberships();
        foreach( $oPlayers as $oPlayer )
        {
            $oOverlappingPlayers = $oPlayersToCheck->getOverlappingTimeSlots( $oPlayer );
            if ( $oOverlappingPlayers->count() > 1 ) {
                $oOverlappingPlayers->remove( $oPlayer );
                $oOverlappingPlayer = $oOverlappingPlayers->first();
                $arrMessages[] = "persoon ".$oPerson->getFullName()." heeft overlappende spelersperioden: ".$oPlayer->getProvider()->getName().",".$oPlayer->getStartDateTime()->toString( Agenda_DateTime::STR_SQLDATE )." => ".( $oPlayer->getEndDateTime() !== null ? $oPlayer->getEndDateTime()->toString( Agenda_DateTime::STR_SQLDATE ) : null )." met ".$oOverlappingPlayer->getProvider()->getName().",".$oOverlappingPlayer->getStartDateTime()->toString( Agenda_DateTime::STR_SQLDATE )." => ".( $oOverlappingPlayer->getEndDateTime() !== null ? $oOverlappingPlayer->getEndDateTime()->toString( Agenda_DateTime::STR_SQLDATE ) : null );
                $oOverlappingPlayers->add( $oPlayer );
            }
        }

        $bIsValid = ( count( $arrMessages ) === 0 );

        $oDbWriter = Voetbal_Person_Factory::createDbWriter();
        $oPerson->addObserver( $oDbWriter );
        $oPerson->putValidatedDateTime( $bIsValid ? Agenda_Factory::createDateTime() : null );
        $oDbWriter->write();
        $oPerson->flushObservers();

        if ( $bIsValid ) { return true; }
        return $arrMessages;
    }
}

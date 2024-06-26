<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 20-12-15
 * Time: 10:04
 */

class Voetbal_Command_Handler_AddUpdatePerson
{
    protected $m_arrAvailableBacknumbers = array();
    protected $m_arrReindexedParticipations = array();

    public function handle( Voetbal_Command_AddUpdatePerson $command)
    {
        $oPerson = $command->getPerson();
        $sFirstName = $command->getFirstName();
        $sNameInsertions = $command->getNameInsertions();
        $sLastName = $command->getLastName();
        $arrPlayerPeriods = $command->getPlayerPeriods();
        $sExternId = $command->getExternId();
        $oDbWriter = Voetbal_Person_Factory::createDbWriter();

        if ( $oPerson === null ) /* person should be added */
        {
            $oPerson = Voetbal_Person_Factory::createObject();
            $oPerson->putId( "__NEW__" . hash( "md5", $sFirstName . $sNameInsertions . $sLastName . $sExternId ) );
            $oPersons = Voetbal_Person_Factory::createObjects();
            $oPersons->addObserver( $oDbWriter );
            $oPersons->add( $oPerson );
        }
        else
        {
            $oPerson->addObserver( $oDbWriter );
        }

        if ( strlen( $sFirstName ) > 0 and $oPerson->getFirstName() !== $sFirstName )
            $oPerson->putFirstName( $sFirstName );
        if ( strlen( $sNameInsertions ) > 0 and $oPerson->getNameInsertions() !== $sNameInsertions )
            $oPerson->putNameInsertions( $sNameInsertions );
        if ( strlen( $sLastName ) > 0 and $oPerson->getLastName() !== $sLastName )
            $oPerson->putLastName( $sLastName );
        if ( $command->getDateOfBirth() !== null and !( $command->getDateOfBirth() == $oPerson->getDateOfBirth() ) )
            $oPerson->putDateOfBirth( $command->getDateOfBirth() );
        if ( strlen( $sExternId ) > 0 and Import_Factory::getIdFromExternId( $oPerson->getExternId() ) !== ((string)$sExternId ) )
            $oPerson->putExternId( Import_Factory::$m_szExternPrefix . $sExternId );

        $oDbWriter->write();
        $oPerson->flushObservers();

        $this->addPlayerPeriods( $oPerson, $arrPlayerPeriods, $command );

        Voetbal_Person_Factory::mergePlayerMemberships( $oPerson );

        $validatePersonCommand = new Voetbal_Command_ValidatePerson( $oPerson );
        $command->getBus()->handle( $validatePersonCommand );

        return $oPerson;
    }

    protected function addPlayerPeriods( $oPerson, $arrPlayerPeriods, $command )
    {
        if ( $arrPlayerPeriods === null )
            return;

        foreach( $arrPlayerPeriods as $oPlayerPeriod )
        {
            if ( $oPlayerPeriod->person === null ) {
                $oPlayerPeriod->person = new stdClass();
                $oPlayerPeriod->person->id = $oPerson->getId();
            }

            $addUpdatePlayerPeriodCommand = new Voetbal_Command_AddUpdatePlayerPeriod(
                null,
                $oPlayerPeriod->person,
                $oPlayerPeriod->team,
                $oPlayerPeriod->timeslot
            );
            if ( property_exists( $oPlayerPeriod, "backnumber" ) and $oPlayerPeriod->backnumber !== null )
                $addUpdatePlayerPeriodCommand->putBackNumber( $oPlayerPeriod->backnumber );
            if ( property_exists( $oPlayerPeriod, "line" ) and $oPlayerPeriod->line !== null )
                $addUpdatePlayerPeriodCommand->putLine( $oPlayerPeriod->line );
            $addUpdatePlayerPeriodCommand->putBus( $command->getBus() );
            $command->getBus()->handle( $addUpdatePlayerPeriodCommand );
        }
    }
}
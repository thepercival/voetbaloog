<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 20-12-15
 * Time: 10:04
 */

class Voetbal_Command_Handler_AddUpdatePlayerPeriod
{

    public function handle( Voetbal_Command_AddUpdatePlayerPeriod $command)
    {
        $oPlayerPeriod = $command->getPlayerPeriod();
        $oProvider = $command->getRealProvider( $command->getProvider() );
        $oClient = $command->getRealClient( $command->getClient() );
        $oTimeSlot = $command->getRealTimeSlot( $command->getTimeSlot() );

        $nBackNumber = $command->getBackNumber();
        $nLine = $command->getLine();
        $oDbWriter = Voetbal_Team_Membership_Player_Factory::createDbWriter();

        if ( $oPlayerPeriod === null ) /* playerperiod should be added */
        {
            if ( $oProvider === null )
                throw new Exception("kon geen spelersperiode wijzigen of aanmaken, de invoerparameter team is leeg", E_ERROR );
            if ( $oClient === null )
                throw new Exception("kon geen spelersperiode wijzigen of aanmaken, de invoerparameter persoon is leeg", E_ERROR );
            if ( $oTimeSlot === null )
                throw new Exception("kon geen spelersperiode wijzigen of aanmaken, de invoerparameter tijdslot is leeg", E_ERROR );

            // check if there is an overlapping playerperiod
            $oExistingPlayerPeriod = null;
            {
                $oOptions = MemberShip_Factory::getMembershipFilters( "Voetbal_Team_Membership_Player", null, $oClient, $oTimeSlot );
                $oOptions->addOrder( "Voetbal_Team_Membership_Player::StartDateTime", false );
                $oExistingPlayerPeriods = Voetbal_Team_Membership_Player_Factory::createObjectsFromDatabase( $oOptions );
                foreach( $oExistingPlayerPeriods as $oExistingPlayerPeriod )
                {
                    if ( $oTimeSlot->getStartDateTime() > $oExistingPlayerPeriod->getStartDateTime()
                        and ( $oTimeSlot->getEndDateTime() === null or ( $oExistingPlayerPeriod->getEndDateTime() !== null and $oTimeSlot->getEndDateTime() > $oExistingPlayerPeriod->getEndDateTime() ) )
                    ) {
                        continue;
                    }

                    $sName = $oClient->getFullName( Voetbal_Person::CALLTYPE_FULLNAME_FIRSTNAMELETTER );
                    $sStart = $oExistingPlayerPeriod->getStartDateTime()->toString( Agenda_DateTime::STR_SQLDATE );
                    $sEnd = $oExistingPlayerPeriod->getEndDateTime() !== null ? $oExistingPlayerPeriod->getEndDateTime()->toString( Agenda_DateTime::STR_SQLDATE ) : null;
                    throw new Exception("voor ".$sName." is er een overlappende spelersperiode(".$sStart." -> ".$sEnd.") waarmee niet te verenigen valt", E_ERROR );
                }
            }

            // stop old and start new  playerperiod
            if ( $oExistingPlayerPeriod !== null )
            {
                $oExistingPlayerPeriod->addObserver( $oDbWriter );
                $oExistingPlayerPeriod->putEndDateTime( $oTimeSlot->getStartDateTime() );
            }

            if ( $nBackNumber === null )
            {
                $oAvailableBacknumbers = Voetbal_Team_Membership_Player_Factory::getAvailableBackNumbers( $oProvider, $oTimeSlot );
                $oAvailableBacknumber = $oAvailableBacknumbers->first();

                if ( $oAvailableBacknumber === null )
                    throw new Exception("geen rugnummers beschikbaar om uit te delen voor team ".$oProvider->getName(), E_ERROR );
                $nBackNumber = $oAvailableBacknumber->getId();
            }

            $oPlayerPeriod = Voetbal_Team_Membership_Player_Factory::createObject();
            $oPlayerPeriod->putId( "__NEW__" );
            $oPlayerPeriods = Voetbal_Team_Membership_Player_Factory::createObjects();
            $oPlayerPeriods->addObserver( $oDbWriter );
            $oPlayerPeriods->add( $oPlayerPeriod );

            $oPlayerPeriod->putProvider( $oProvider );
            $oPlayerPeriod->putClient( $oClient );
            $oPlayerPeriod->putStartDateTime( $oTimeSlot->getStartDateTime() );
            $oPlayerPeriod->putEndDateTime( $oTimeSlot->getEndDateTime() );
            $oPlayerPeriod->putBackNumber( $nBackNumber );
            $oPlayerPeriod->putLine( $nLine !== null ? $nLine : 0 ); // this is invalid
        }
        else
        {
            $oPlayerPeriod->addObserver( $oDbWriter );

            if ( $nBackNumber > 0 and $oPlayerPeriod->getBackNumber() !== $nBackNumber )
                $oPlayerPeriod->putBackNumber( $nBackNumber );
            if ( $nLine > 0 and ( $oPlayerPeriod->getLine() === null or $oPlayerPeriod->getLine()->getId() !== $nLine ) )
                $oPlayerPeriod->putLine( $nLine );
            if ( $oPlayerPeriod->getEndDateTime() < $oTimeSlot->getEndDateTime() or $oPlayerPeriod->getEndDateTime() > $oTimeSlot->getEndDateTime() ){
                $oPlayerPeriod->putEndDateTime( $oTimeSlot->getEndDateTime() );
            }
        }

        $oDbWriter->write();

        $validatePersonCommand = new Voetbal_Command_ValidatePerson( $oPlayerPeriod->getClient() );
        $command->getBus()->handle( $validatePersonCommand );

        return $oPlayerPeriod;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 20-12-15
 * Time: 10:04
 */

class Voetbal_Command_Handler_ValidateGame
{
    public function handle( Voetbal_Command_ValidateGame $command)
    {
        $oGame = $command->getGame();
        $arrMessages = array();

        // check if game played
        if ( $oGame->getState() !== Voetbal_Factory::STATE_PLAYED )
            $arrMessages[] = "wedstrijd is nog niet gespeeld";

        // check on startdatetime is not null
        if ( $oGame->getStartDateTime() === null )
            $arrMessages[] = "startdatum is niet gezet";

        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter("Voetbal_Game_Participation::Game", "EqualTo", $oGame );
        $oParticipations = Voetbal_Game_Participation_Factory::createObjectsFromDatabase( $oOptions );

        if ( $oParticipations->count() > 0 ) {
            // check nroggoals with goalevents
            $oHomeGoals = $oGame->getGoals(Voetbal_Game::HOME);
            if ($oHomeGoals->count() !== $oGame->getHomeGoals() )
                $arrMessages[] = "aantal thuisgoals (".$oGame->getHomeGoals().") komt niet overeen met het aantal thuisgoal-events (".$oHomeGoals->count().")";

            $oAwayGoals = $oGame->getGoals(Voetbal_Game::AWAY);
            if ($oAwayGoals->count() !== $oGame->getAwayGoals())
                $arrMessages[] = "aantal uitgoals (".$oGame->getAwayGoals().") komt niet overeen met het aantal uitgoal-events (".$oAwayGoals->count().")";

            // check if goalevents are with participations
            $oGoals = $oGame->getGoals();
            foreach( $oGoals as $oGoal )
            {
                if ( $oGoal->getMinute() < $oGoal->getGameParticipation()->getIn()
                    or ( $oGoal->getGameParticipation()->getOut() > 0 and $oGoal->getMinute() > $oGoal->getGameParticipation()->getOut() )
                )
                    $arrMessages[] = "goal in minuut ".$oGoal->getMinute()." valt niet binnen de deelname ( ".$oGoal->getGameParticipation()->getIn()." t/m ".$oGoal->getGameParticipation()->getOut()." )";
            }

            // check if participation have a playerperiod
            foreach( $oParticipations as $oParticipation )
            {
                $oPlayer = $oParticipation->getTeamMembershipPlayer();
                if ( $oPlayer === null )
                    $arrMessages[] = "de deelname voor team ".$oParticipation->getTeam()->getName()." van minuut ".$oParticipation->getIn()." tot ".( $oParticipation->getOut() > 0 ? "minuut ".$oParticipation->getOut() : "het einde")." heeft geen spelersperiode";
            }

            // check if nr of gameparticipations is correct
            if ( $oParticipations->count() < 22 or $oParticipations->count() > 28 )
                $arrMessages[] = "het aantal deelnames is niet correct : ".$oParticipations->count();

            // check if game startdatetime is within participation-playerperiod
            foreach( $oParticipations as $oParticipation )
            {
                $oPlayer = $oParticipation->getTeamMembershipPlayer();
                if ( $oPlayer === null ) {
                    $arrMessages[] = "deelname {in:".$oParticipation->getIn()." -> out:".$oParticipation->getOut()."} voor team (".$oParticipation->getTeam()->getName().") is niet gekoppeld aan een spelersperiode";

                    continue;
                }

                if ( $oParticipation->getTeam() !== $oPlayer->getProvider() )
                    $arrMessages[] = "team van deelname(".$oParticipation->getTeam()->getName().") is anders als het team van de spelersperiode(".$oPlayer->getProvider()->getName().")";

                if ( $oPlayer->getStartDateTime() > $oGame->getStartDateTime() )
                    $arrMessages[] = "de spelersperiode( ".$oPlayer->getClient()->getFullName( Voetbal_Person::CALLTYPE_FULLNAME_FIRSTNAMELETTER).", ".$oParticipation->getTeam()->getName()." )-startdatum(".$oPlayer->getStartDateTime().") is na de wedstrijd-startdatum(".$oGame->getStartDateTime().")";

                if ( $oPlayer->getEndDateTime() !== null and $oPlayer->getEndDateTime() < $oGame->getStartDateTime() )
                    $arrMessages[] = "de spelersperiode( ".$oPlayer->getClient()->getFullName( Voetbal_Person::CALLTYPE_FULLNAME_FIRSTNAMELETTER).", ".$oParticipation->getTeam()->getName()." )-einddatum(".$oPlayer->getEndDateTime().") is voor de wedstrijd-startdatum(".$oGame->getStartDateTime().")";
            }

            // check if participation-subs are valid
            $fncCheckSubs = function ( $oGame, $oTeam )
            {
                $oOptions = Construction_Factory::createOptions();
                $oOptions->addFilter("Voetbal_Game_Participation::Game", "EqualTo", $oGame );
                $oOptions->addFilter("Voetbal_Team_Membership_Player::Provider", "EqualTo", $oTeam );
                $oOptionsOr = Construction_Factory::createOptions();
                $oOptionsOr->putId("__OR__");
                $oOptionsOr->addFilter("Voetbal_Game_Participation::In", "GreaterThan", 0 );
                $oOptionsOr->addFilter("Voetbal_Game_Participation::Out", "GreaterThan", 0 );
                $oOptions->add( $oOptionsOr );
                $oOptions->addOrder("Voetbal_Game_Participation::In", false );
                $oOptions->addOrder("Voetbal_Game_Participation::Out", false );
                $oParticipationsSub = Voetbal_Game_Participation_Factory::createObjectsFromDatabase( $oOptions );
                $oInvalidParticipationsSub = Voetbal_Game_Participation_Factory::createObjects();
                $oInvalidParticipationsSub->addCollection( $oParticipationsSub );
                foreach( $oParticipationsSub as $oParticipationSub )
                {
                    if ( $oParticipationSub->getIn() > 0 )
                    {
                        foreach( $oInvalidParticipationsSub as $oInvalidParticipationSub ) {
                            if ( $oInvalidParticipationSub->getOut() === $oParticipationSub->getIn()) {
                                $oInvalidParticipationsSub->remove( $oInvalidParticipationSub );
                                break;
                            }
                        }
                    }
                    if ( $oParticipationSub->getOut() > 0 )
                    {
                        foreach( $oInvalidParticipationsSub as $oInvalidParticipationSub ) {
                            if ($oInvalidParticipationSub->getIn() === $oParticipationSub->getOut()) {
                                $oInvalidParticipationsSub->remove( $oInvalidParticipationSub );
                                break;
                            }
                        }
                    }
                }
                foreach( $oInvalidParticipationsSub as $oInvalidParticipationSub ){
                    if ( $oInvalidParticipationSub->getIn() > 0 )
                        $arrMessages[] = "bij deze invaller kan geen uitvaller worden gevonden";
                    if ( $oInvalidParticipationSub->getOut() > 0 )
                        $arrMessages[] = "bij deze uitvaller kan geen invaller worden gevonden";
                }
            };
            $fncCheckSubs( $oGame, $oGame->getHomePoulePlace()->getTeam() );
            $fncCheckSubs( $oGame, $oGame->getAwayPoulePlace()->getTeam() );

            // check if gameevent are within participation minutes!!!!!
            foreach( $oParticipations as $oParticipation )
            {
                if ( $oParticipation->getYellowCardOne() > 0
                    and ( $oParticipation->getYellowCardOne() < $oParticipation->getIn()
                        or ( $oParticipation->getOut() > 0 and $oParticipation->getYellowCardOne() > $oParticipation->getOut() )
                    )
                )
                    $arrMessages[] = "eerste gele kaart in minuut ".$oParticipation->getYellowCardOne()." valt niet binnen de deelname ( ".$oParticipation->getIn()." t/m ".$oParticipation->getOut()." )";

                if ( $oParticipation->getYellowCardTwo() > 0
                    and ( $oParticipation->getYellowCardTwo() < $oParticipation->getIn()
                        or ( $oParticipation->getOut() > 0 and $oParticipation->getYellowCardTwo() > $oParticipation->getOut() )
                    )
                )
                    $arrMessages[] = "tweede gele kaart in minuut ".$oParticipation->getYellowCardTwo()." valt niet binnen de deelname ( ".$oParticipation->getIn()." t/m ".$oParticipation->getOut()." )";

                if ( $oParticipation->getRedCard() > 0
                    and ( $oParticipation->getRedCard() < $oParticipation->getIn()
                        or ( $oParticipation->getOut() > 0 and $oParticipation->getRedCard() > $oParticipation->getOut() )
                    )
                )
                    $arrMessages[] = "rode kaart in minuut ".$oParticipation->getRedCard()." valt niet binnen de deelname ( ".$oParticipation->getIn()." t/m ".$oParticipation->getOut()." )";
            }
        }

        $bIsValid = ( count( $arrMessages ) === 0 );

        $oDbWriter = Voetbal_Game_Factory::createDbWriter();
        $oGame->addObserver( $oDbWriter );
        $oGame->putValidatedDateTime( $bIsValid ? Agenda_Factory::createDateTime() : null );
        $oDbWriter->write();
        $oGame->flushObservers();

        if ( $bIsValid ) { return true; }
        return $arrMessages;
    }
}
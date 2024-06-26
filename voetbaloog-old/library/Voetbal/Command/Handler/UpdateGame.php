<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 20-12-15
 * Time: 10:04
 */

class Voetbal_Command_Handler_UpdateGame
{
    protected $m_arrReindexedParticipations = array();

    public function handle(Voetbal_Command_UpdateGame $command)
    {
        $oDbWriter = Voetbal_Game_Factory::createDbWriter();
        $oGame = $command->getGame();
        $oGame->addObserver($oDbWriter);
        if ($command->getStartDateTime() !== null)
            $oGame->putStartDateTime($command->getStartDateTime());
        if ($command->getNumber() !== null)
            $oGame->putNumber($command->getNumber());
        $oGoals = $command->getGoals();

        $bScoreChanged = $this->hasScoreChanged( $oGoals, $oGame );

        $oGame->putHomeGoals( $oGoals->homegoals );
        $oGame->putAwayGoals( $oGoals->awaygoals);

        if ( property_exists( $oGoals, "homegoalsextratime" ) ) {
            $oGame->putHomeGoalsExtraTime( $oGoals->homegoalsextratime );
        }
        if( property_exists( $oGoals, "awaygoalsextratime") ) {
            $oGame->putAwayGoalsExtraTime( $oGoals->awaygoalsextratime );
        }
        if ( property_exists( $oGoals, "homegoalspenalty" ) ) {
            $oGame->putHomeGoalsPenalty( $oGoals->homegoalspenalty );
        }
        if ( property_exists( $oGoals, "awaygoalspenalty" ) ) {
            $oGame->putAwayGoalsPenalty( $oGoals->awaygoalspenalty );
        }
        $oGame->putState($command->getState());
        $oGame->putLocation( $command->getLocation() );
        if ( $oGame->getState() === Voetbal_Factory::STATE_SCHEDULED and $command->shouldSwitchHomeAway() === true ) {
            $oAwayPoulePlace = $oGame->getAwayPoulePlace();
            $oGame->putAwayPoulePlace( $oGame->getHomePoulePlace() );
            $oGame->putHomePoulePlace( $oAwayPoulePlace );
        }

        $oHomeTeam = $oGame->getHomePoulePlace()->getTeam();
        $oAwayTeam = $oGame->getAwayPoulePlace()->getTeam();

        $this->updateParticipations($oGame, $oHomeTeam, $command->getHomeParticipations());
        $this->updateParticipations($oGame, $oAwayTeam, $command->getAwayParticipations());

        $this->updateEvents($oGame, $oHomeTeam, $command->getHomeEvents());
        $this->updateEvents($oGame, $oAwayTeam, $command->getAwayEvents());

        $oDbWriter->write();
        $oGame->flushObservers();

        $validateGameCommand = new Voetbal_Command_ValidateGame( $oGame );
        $command->getBus()->handle( $validateGameCommand );

        if ( $oGame->getState() === Voetbal_Factory::STATE_PLAYED ) {
	        if ( $bScoreChanged ){
		        $applyQualifyRulesCommand = new Voetbal_Command_ApplyQualifyRules( $oGame );
		        $command->getBus()->handle( $applyQualifyRulesCommand );
	        }
	        Patterns_Event_Factory::handle( "gamechanged", $oGame );
        }

        $oCache = ZendExt_Cache::getDefaultCache();
        $oCompetitionSeason = $oGame->getCompetitionSeason();
        $oCache->clean( Zend_Cache::CLEANING_MODE_MATCHING_TAG,	array( 'competitionseason'.$oCompetitionSeason->getId() ) );

        return $oGame;
    }

    protected function hasScoreChanged( $oGoals, $oGame )
    {
        return $oGame->getHomeGoals() != $oGoals->homegoals
            or $oGame->getAwayGoals() != $oGoals->awaygoals
            or ( ( property_exists( $oGoals, "homegoalsextratime" ) and $oGame->getHomeGoalsExtraTime() != $oGoals->homegoalsextratime ) )
            or ( ( property_exists( $oGoals, "awaygoalsextratime" ) and $oGame->getAwayGoalsExtraTime() != $oGoals->awaygoalsextratime ) )
            or ( ( property_exists( $oGoals, "homegoalspenalty" ) and $oGame->getHomeGoalsPenalty() != $oGoals->homegoalspenalty ) )
            or ( ( property_exists( $oGoals, "awaygoalspenalty" ) and $oGame->getAwayGoalsPenalty() != $oGoals->awaygoalspenalty ) )
        ;
    }

    protected function updateParticipations( $oGame, $oTeam, $arrParticipations )
    {
        if ( $arrParticipations === null )
            return;

        $oGameParticipationDbWriter = Voetbal_Game_Participation_Factory::createDbWriter();
        $oGameParticipations = $oGame->getParticipations( $oTeam );
        $oGameParticipations->addObserver( $oGameParticipationDbWriter );
        $oGameParticipations->flush();

        /** @var Voetbal_Game_Participation $oParticipation */
        foreach( $arrParticipations as $oParticipation ) {

            $oPlayerMembership = $this->getPlayerPeriod( $oParticipation, $oTeam, $oGame->getStartDateTime() );

            $oGameParticipation = Voetbal_Game_Participation_Factory::createObject();
            $oGameParticipation->putId("__NEW__" . $oTeam->getId() . "_" . $oGameParticipations->count() );
            $oGameParticipation->putGame( $oGame );
            $oGameParticipation->putTeam( $oTeam );
            $oGameParticipation->putTeamMembershipPlayer( $oPlayerMembership );
            $oGameParticipation->putIn( $oParticipation->getIn() );
            $oGameParticipation->putOut( $oParticipation->getOut() );
            $oGameParticipations->add( $oGameParticipation );
        }

        $oGameParticipationDbWriter->write();
    }

    /*
    "type": 1,
    "minute": 36,
    "player": {
        "id": 210138,
        "name": "Queensy Menig"
    },
    "subplayer": null
    */
    protected function updateEvents( $oGame, $oTeam, $arrEvents )
    {
        if ( $arrEvents === null )
            return;

        $arrParticipations = $this->getReindexedParticipations( $oGame, $oTeam );

        $oParticipationDbWriter = Voetbal_Game_Participation_Factory::createDbWriter();
        $oGoalDbWriter = Voetbal_Goal_Factory::createDbWriter();
        $oGoals = Voetbal_Goal_Factory::createObjects();
        $oGoals->addObserver( $oGoalDbWriter );
        $nDetailGoal = Voetbal_Game::DETAIL_GOAL + Voetbal_Game::DETAIL_GOALOWN + Voetbal_Game::DETAIL_GOALPENALTY;

        foreach( $arrEvents as $oEvent ) {

            $oParticipation = $this->getParticipationFromExternal( $oEvent->participation, $oTeam, $oGame, $arrParticipations );
            $oAssistParticipation = null;
            if( $oEvent->type === Voetbal_Game::DETAIL_GOAL && property_exists( $oEvent, "assistparticipation" ) && $oEvent->assistparticipation !== null ) {
                $oAssistParticipation = $this->getParticipationFromExternal( $oEvent->assistparticipation, $oTeam, $oGame, $arrParticipations );
            }

            if ( ( $oEvent->type & $nDetailGoal ) === $oEvent->type ){ // create goal
                $oGoal = Voetbal_Goal_Factory::createObject();
                $oGoal->putId( "__NEW__" . $oGoals->count() );
                $oGoal->putGameParticipation( $oParticipation );
                $oGoal->putOwnGoal( $oEvent->type === Voetbal_Game::DETAIL_GOALOWN );
                $oGoal->putPenalty( $oEvent->type === Voetbal_Game::DETAIL_GOALPENALTY );
                $oGoal->putMinute( $oEvent->minute );
                $oGoal->putAssistGameParticipation( $oAssistParticipation );
                $oGoals->add( $oGoal );
            }
            else // update gameparticipation
            {
                $oParticipation->addObserver( $oParticipationDbWriter );
                if ( $oEvent->type === Voetbal_Game::DETAIL_REDCARD )
                    $oParticipation->putRedCard( $oEvent->minute );
                else if ( $oEvent->type === Voetbal_Game::DETAIL_YELLOWCARDONE )
                    $oParticipation->putYellowCardOne( $oEvent->minute );
                else if ( $oEvent->type === Voetbal_Game::DETAIL_YELLOWCARDTWO )
                    $oParticipation->putYellowCardTwo( $oEvent->minute );
            }
        }

        $oParticipationDbWriter->write();
        $oGoalDbWriter->write();
    }

    protected function getParticipationFromExternal( $p_oParticipation, $oTeam, $oGame, $arrParticipations )
    {
        $oParticipation = null;
        {
            $oPlayerMembership = $this->getPlayerPeriod( $p_oParticipation, $oTeam, $oGame->getStartDateTime() );
            $nId = -1;
            if ( $oPlayerMembership !== null ) {
                $nId = $oPlayerMembership->getId();
            }
            if ( array_key_exists( $nId, $arrParticipations ) ) {
                $oParticipation = $arrParticipations[ $nId ];
            }
        }
        if ( $oParticipation === null ){
            $sName = $p_oParticipation->player->person !== null ? $p_oParticipation->player->person->name : null;
            $sExternId = $p_oParticipation->player->person !== null ? $p_oParticipation->player->person->externid : null;
            throw new Exception("kon geen wedstrijddeelname vinden voor ( naam:".$sName.",externid:".$sExternId." )", E_ERROR );
        }
        return $oParticipation;
    }

    protected function getPlayerPeriod( Voetbal_Extern_Game_Participation $oParticipation, Voetbal_Team $oTeam, Agenda_DateTime $oDateTime )
    {
        if ( property_exists( $oParticipation, "id" ) and $oParticipation->id !== null )
            return Voetbal_Team_Membership_Player_Factory::createObjectFromDatabase( $oParticipation->id );

        $person = $oParticipation->getPlayerPeriod()->getPerson();
        $nPersonExternId = $person ? $person->getId() : null;
        if ( strlen( $nPersonExternId ) === 0 ) {
            return null;
        }

        $oOptions = Construction_Factory::createFiltersForTimeSlots("Voetbal_Team_Membership_Player", $oDateTime, Agenda_TimeSlot::EXCLUDE_NONE, true);
        $oOptions->addFilter("Voetbal_Person::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix . $nPersonExternId );
        $oOptions->addFilter("Voetbal_Team_Membership_Player::Provider", "EqualTo", $oTeam );
        return Voetbal_Team_Membership_Player_Factory::createObjectFromDatabase($oOptions);
    }

    /**
     * if participation has no playerperiod, participation can be founc be minute!!
     *
     * @param Voetbal_Game $oGame
     * @param Voetbal_Team $oTeam
     * @return mixed
     * @throws Exception
     */
    protected function getReindexedParticipations( Voetbal_Game $oGame, Voetbal_Team $oTeam )
    {
        if ( array_key_exists( $oTeam->getId(), $this->m_arrReindexedParticipations ) === false )
        {
            $oParticipations = $oGame->getParticipations( $oTeam );
            $sEmptyInOut = null;
            $arrParticipations = array();
            foreach( $oParticipations as $oParticipation )
            {
                $oTeamMembershipPlayer = $oParticipation->getTeamMembershipPlayer();
                if ( $oTeamMembershipPlayer !== null ) {
                    $arrParticipations[ $oTeamMembershipPlayer->getId() ] = $oParticipation;
                }
                else if ( strlen( $sEmptyInOut ) === 0  ){
                    $arrParticipations[ -1 ] = $oParticipation;
                    $sEmptyInOut = $oParticipation->getIn() . "->" . $oParticipation->getOut();
                }
                else {
                    $sEmptyInOut .= " , " . $oParticipation->getIn() . "->" . $oParticipation->getOut();
                    throw new Exception( "er zijn meer dan 1 onbekende spelers ( ". $sEmptyInOut ." ) voor team : " . $oTeam->getName(), E_ERROR );
                }
            }
            //die();
            $this->m_arrReindexedParticipations[ $oTeam->getId() ] = $arrParticipations;
        }
        return $this->m_arrReindexedParticipations[ $oTeam->getId() ];
    }
}
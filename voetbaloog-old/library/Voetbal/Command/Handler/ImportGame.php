<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 20-12-15
 * Time: 10:04
 */

class Voetbal_Command_Handler_ImportGame
{
    protected $m_arrReindexedParticipations = array();

    public function handle(Voetbal_Command_ImportGame $command)
    {
        $oExternGame = $command->getExternGame();

        $oDbWriter = Voetbal_Game_Factory::createDbWriter();
        $oGame = $command->getGame();
        $oGame->addObserver($oDbWriter);

        $bScoreChanged = $this->hasScoreChanged( $oGame, $oExternGame );

        $oGame->putHomeGoals( $oExternGame->getHomeGoals() );
        $oGame->putAwayGoals( $oExternGame->getAwayGoals() );

        $oGame->putState($oExternGame->getState());

        $this->importParticipations($oGame, $oExternGame);

        $this->importGoals($oGame, $oExternGame);

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

    protected function hasScoreChanged( Voetbal_Game $oGame, Voetbal_Extern_GameExt $oExternGame ): bool
    {
        return $oGame->getHomeGoals() !== $oExternGame->getHomeGoals()
            or $oGame->getAwayGoals() !== $oExternGame->getAwayGoals()
        ;
    }

    /**
     *
     * @param Voetbal_Extern_Game_Participation $oExternParticipation
     * @return Voetbal_Person
     */
    protected function importPerson( Voetbal_Extern_Game_Participation $oExternParticipation ): Voetbal_Person {

        $oExternPlayerPeriod = $oExternParticipation->getPlayerPeriod();

        $sId = $oExternPlayerPeriod->getPerson()->getId();
        $sName = $oExternPlayerPeriod->getPerson()->getName();
        list ($sFirstName, $sNameInsertions, $sLastName) = Voetbal_Person_Factory::getNameParts( $sName );

        // tmp @TODO CDK
//        $oPerson = $this->updatePersonDEP( $oExternParticipation );
//        if( $oPerson !== null ) {
//            return $oPerson;
//        }

        /** @var Voetbal_Person $oPerson */
        $oPerson = Voetbal_Person_Factory::createObject();
        $oPerson->putId("__NEW__" . $sId );
        $oPerson->putFirstName( $sFirstName );
        $oPerson->putNameInsertions( $sNameInsertions );
        $oPerson->putLastName( $sLastName );
        $oPerson->putExternId( Import_Factory::$m_szExternPrefix . $sId );

        $oDbWriter = Voetbal_Person_Factory::createDbWriter();
        $oPersons = Voetbal_Person_Factory::createObjects();
        $oPersons->addObserver( $oDbWriter );
        $oPersons->add( $oPerson );
        $oDbWriter->write();
        return $oPerson;
    }

    protected function importParticipations( Voetbal_Game $oGame, Voetbal_Extern_GameExt $oExternGame )
    {
        $oGameParticipationDbWriter = Voetbal_Game_Participation_Factory::createDbWriter();
        $oGameParticipations = $oGame->getParticipations();
        $oGameParticipations->addObserver( $oGameParticipationDbWriter );
        $oGameParticipations->flush();

        /** @var Voetbal_Extern_Game_Participation $oParticipation */
        foreach( $oExternGame->getParticipations() as $oExternParticipation ) {

            $oExternPlayerPeriod = $oExternParticipation->getPlayerPeriod();
            $oTeam = Voetbal_Team_Factory::createObjectFromDatabaseByExtern( $oExternPlayerPeriod->getTeam() );
            if( $oTeam === null ) {
                throw new \Exception("het team voor externid \"".$oExternPlayerPeriod->getTeam()->getId()."\" kan niet gevonden worden", E_ERROR );
            }

            $oPerson = Voetbal_Person_Factory::createObjectFromDatabaseByExtern( $oExternPlayerPeriod->getPerson() );
            if ( $oPerson === null ) {
                $oPerson = $this->importPerson( $oExternParticipation );
            }

            $oDateTime = clone $oGame->getStartDateTime();
            $oDateTime->modify("+1 days");

            $oPlayerMembership = $this->getPlayerPeriod( $oPerson, $oTeam, $oDateTime );
            if ( $oPlayerMembership === null ) {
                $oTimeSlot = Voetbal_Team_Membership_Player_Factory::getDefaultPlayerPeriodTimeSlot( $oGame );
                $this->createPlayerPeriod( $oPerson, $oTeam, $oTimeSlot, $oExternPlayerPeriod->getLine() );
                $oPlayerMembership = $this->getPlayerPeriod( $oPerson, $oTeam, $oDateTime );
                if ( $oPlayerMembership === null ) {
                    throw new \Exception("de spelersperiode voor  \"".$oPerson->getLastName()."\" kan nog steeds niet gevonden worden", E_ERROR );
                }
            }

            /** @var Voetbal_Game_Participation $oGameParticipation */
            $oGameParticipation = Voetbal_Game_Participation_Factory::createObject();
            $oGameParticipation->putId("__NEW__" . $oTeam->getId() . "_" . $oGameParticipations->count() );
            $oGameParticipation->putGame( $oGame );
            $oGameParticipation->putTeam( $oTeam );
            $oGameParticipation->putTeamMembershipPlayer( $oPlayerMembership );
            $oGameParticipation->putIn( $oExternParticipation->getIn() );
            $oGameParticipation->putOut( $oExternParticipation->getOut() );
            $oGameParticipation->putYellowCardOne( $oExternParticipation->getYellowCard() );
            $oGameParticipation->putYellowCardTwo( $oExternParticipation->getYellowCard2() );
            $oGameParticipation->putRedCard( $oExternParticipation->getRedCard() );
            $oGameParticipations->add( $oGameParticipation );
        }

        $oGameParticipationDbWriter->write();
    }


    protected function importGoals( Voetbal_Game $oGame, Voetbal_Extern_GameExt $oExternGame )
    {
        $oDbWriter = Voetbal_Goal_Factory::createDbWriter();
        $oGoals = $oGame->getGoals();
        $oGoals->addObserver( $oDbWriter );
        $oGoals->flush();

        /** @var Voetbal_Extern_Goal $oExternGoal */
        foreach( $oExternGame->getGoals() as $oExternGoal ) {
            /** @var Voetbal_Goal $oGoal */
            $oGoal = Voetbal_Goal_Factory::createObject();
            $oGoal->putId("__NEW__" . $oExternGoal->getId()  );
            $oGoalParticipation = $this->getGameParticipation( $oGame, $oExternGoal->getGameParticipation() );
            $oGoal->putGameParticipation( $oGoalParticipation );
            $oGoal->putMinute( $oExternGoal->getMinute() );
            $oGoal->putOwnGoal( $oExternGoal->getOwnGoal() );
            $oGoal->putPenalty( $oExternGoal->getPenalty() );
            if( $oExternGoal->getAssistGameParticipation() ) {
                $oAssistParticipation = $this->getGameParticipation( $oGame, $oExternGoal->getAssistGameParticipation() );
                $oGoal->putAssistGameParticipation( $oAssistParticipation );
            }
            $oGoals->add( $oGoal );
        }
        $oDbWriter->write();
    }

    protected function getGameParticipation( Voetbal_Game $oGame, Voetbal_Extern_Game_Participation $oExternGameParticipation ): ?Voetbal_Game_Participation {

        $oExternTeam = $oExternGameParticipation->getPlayerPeriod()->getTeam();
        $oTeam = Voetbal_Team_Factory::createObjectFromDatabaseByExtern( $oExternTeam );
        $oExternPerson = $oExternGameParticipation->getPlayerPeriod()->getPerson();
        $oPerson = Voetbal_Person_Factory::createObjectFromDatabaseByExtern( $oExternPerson );

        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter("Voetbal_Game_Participation::Game", "EqualTo", $oGame );
        $oOptions->addFilter("Voetbal_Team_Membership_Player::Provider", "EqualTo", $oTeam );
        $oOptions->addFilter("Voetbal_Team_Membership_Player::Client", "EqualTo", $oPerson );
        return Voetbal_Game_Participation_Factory::createObjectFromDatabase( $oOptions );
    }

    protected function getPlayerPeriod( Voetbal_Person $oPerson, Voetbal_Team $oTeam, Agenda_DateTime $oDateTime ): ?Voetbal_Team_Membership_Player
    {
        $oOptions = MemberShip_Factory::getMembershipFilters("Voetbal_Team_Membership_Player", $oTeam, $oPerson, $oDateTime );
        return Voetbal_Team_Membership_Player_Factory::createObjectFromDatabase($oOptions);
    }

    protected function createPlayerPeriod( Voetbal_Person $oPerson, Voetbal_Team $oTeam, Agenda_TimeSlot $oTimeSlot, int $nLine ) {

        $oDbWriter = Voetbal_Team_Membership_Player_Factory::createDbWriter();
        $oPlayerMemberships = Voetbal_Team_Membership_Player_Factory::createObjects();
        $oPlayerMemberships->addObserver( $oDbWriter );

        /** @var Voetbal_Team_Membership_Player $oPlayerMembership */
        $oPlayerMembership = Voetbal_Team_Membership_Player_Factory::createObject();
        $oPlayerMembership->putId("__NEW__" );
        $oPlayerMembership->putProvider($oTeam);
        $oPlayerMembership->putClient($oPerson);
        $oPlayerMembership->putLine($nLine);
        $oPlayerMembership->putBackNumber(0);
        $oPlayerMembership->putStartDateTime( $oTimeSlot->getStartDateTime() );
        $oPlayerMembership->putEndDateTime( $oTimeSlot->getEndDateTime() );

        $oPlayerMemberships->add( $oPlayerMembership );
        $oDbWriter->write();

        Voetbal_Person_Factory::mergePlayerMemberships( $oPerson );
    }
}
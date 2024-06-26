<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:44
 */

class VoetbalOog_Command_Handler_UpdateBets
{
    public function handle( VoetbalOog_Command_UpdateBets $command )
    {
        $oCompetitionSeason = $command->getCompetitionSeason();
        if ( $oCompetitionSeason === null )
            throw new Exception( "het competitieseizoen is leeg", E_ERROR );

        $oValidateDateTime = $command->getValidateDateTime();

        $oBetDbWriter = VoetbalOog_Bet_Factory::createDbWriter();

        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "Voetbal_Game::State", "EqualTo", Voetbal_Factory::STATE_PLAYED );
        if ( $oValidateDateTime !== null )
            $oOptions->addFilter( "Voetbal_Game::ValidatedDateTime", "GreaterThan", $oValidateDateTime );
        $oGames = $oCompetitionSeason->getGames( true, $oOptions );

        $oRoundsToCheck = Voetbal_Poule_Factory::createObjects();

        foreach( $oGames as $oGame )
        {
            $this->updateBetsCorrectnessGame( $oGame, $oBetDbWriter );
            $oPoule = $oGame->getHomePoulePlace()->getPoule();
            if ( $oPoule->getState() === Voetbal_Factory::STATE_PLAYED )
                $oRoundsToCheck->add( $oPoule->getRound()->getCompetitionSeason()->getNextRound( $oPoule->getRound() ) );
        }

        foreach( $oRoundsToCheck as $oRoundToCheck )
        {
            $this->updateBetsCorrectnessQualify( $oRoundToCheck, $oBetDbWriter );
        }

        try {
            $oBetDbWriter->write();
        }
        catch ( Exception $e ) {
            return "voorspellingen niet bijgewerkt : ".$e->getMessage();
        }
        return true;
    }

    protected function updateBetsCorrectnessGame( $oGame, $oBetDbWriter ) {
        $oFilters = Construction_Factory::createOptions();
        $oFilters->addFilter( "VoetbalOog_Bet_Score::Game", "EqualTo", $oGame );
        $oBets = VoetbalOog_Bet_Factory::createObjectsFromDatabase( $oFilters );

        if ( $oBets->count() === 0 ) {
            return;
        }

        foreach ( $oBets as $oBet ) {
            $oBet->addObserver( $oBetDbWriter );
            $oBet->putCorrect( $oBet->isCorrect( $oGame ) );
        }
    }

    protected function updateBetsCorrectnessQualify( $oRound, $oBetDbWriter )
    {
        $oFilters = Construction_Factory::createOptions();
        $oFilters->addFilter( "VoetbalOog_Round_BetConfig::Round", "EqualTo", $oRound );
        $oFilters->addFilter( "VoetbalOog_Round_BetConfig::BetType", "EqualTo", VoetbalOog_Bet_Qualify::$nId );
        $oBets = VoetbalOog_Bet_Factory::createObjectsFromDatabase( $oFilters );

        if ( $oBets->count() === 0 )
            return;

        $oQualifiedTeams = $oRound->getTeams();
        foreach ( $oBets as $oBet )
        {
            $oBet->addObserver( $oBetDbWriter );

            foreach ( $oBets as $oBet ) {
                $oBet->addObserver( $oBetDbWriter );
                $oBet->putCorrect( $oBet->isCorrect( $oQualifiedTeams ) );
            }
        }
    }
}






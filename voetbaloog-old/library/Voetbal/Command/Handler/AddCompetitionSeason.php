<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:44
 */

class Voetbal_Command_Handler_AddCompetitionSeason
{
    public function handle( Voetbal_Command_AddCompetitionSeason $command)
    {
        $oCompetition = $command->getCompetition();

        $oDbWriter = Voetbal_CompetitionSeason_Factory::createDbWriter();
        $oCompetitionSeasons = $oCompetition->getSeasons();
        $oCompetitionSeasons->addObserver($oDbWriter);

        $oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObject();
        $oCompetitionSeason->putId("__NEW__");
        $oCompetitionSeason->putCompetition( $oCompetition );
        $oCompetitionSeason->putSeason( $command->getSeason() );
        $oCompetitionSeason->putPublic( $command->getPublic() );
        $oCompetitionSeason->putExternId( $command->getExternId() );

        $oFromCompetitionSeason = $oCompetitionSeasons->first();
        if ( $oFromCompetitionSeason === null )
            $oFromCompetitionSeason = $this->getDefaultCompetitionSeason( $command->getDefaultAssociation() );

        $oCompetitionSeason->putAssociation( $oFromCompetitionSeason->getAssociation() );
        $oCompetitionSeason->putPromotionRule( $oFromCompetitionSeason->getPromotionRule() );
        $oCompetitionSeason->putNrOfMinutesGame( $oFromCompetitionSeason->getNrOfMinutesGame() );
        $oCompetitionSeason->putNrOfMinutesExtraTime( $oFromCompetitionSeason->getNrOfMinutesExtraTime() );
        $oCompetitionSeason->putWinPointsAfterGame( $oFromCompetitionSeason->getWinPointsAfterGame() );
        $oCompetitionSeason->putWinPointsAfterExtraTime( $oFromCompetitionSeason->getWinPointsAfterExtraTime() );
        $oCompetitionSeasons->add($oCompetitionSeason);
        $oDbWriter->write();

        $removeCopyCSStructureCommand = new Voetbal_Command_RemoveCopyCSStructure( $oFromCompetitionSeason, $oCompetitionSeason );
        $removeCopyCSStructureCommand->putBus( $command->getBus() );
        $command->getBus()->handle( $removeCopyCSStructureCommand );

        return $oCompetitionSeason;
    }

    protected function getDefaultCompetitionSeason( $oDefaultAssociation )
    {
        $oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObject();
        $oCompetitionSeason->putAssociation( $oDefaultAssociation );
        $oCompetitionSeason->putPromotionRule( Voetbal_Factory::DEFAULT_PROMOTION_RULE );
        $oCompetitionSeason->putNrOfMinutesGame( Voetbal_Factory::DEFAULT_NROFMINUTES_GAME );
        $oCompetitionSeason->putNrOfMinutesExtraTime( Voetbal_Factory::DEFAULT_NROFMINUTES_EXTRATIME );
        $oCompetitionSeason->putWinPointsAfterGame( Voetbal_Factory::DEFAULT_WINPOINTS_AFTERGAME );
        $oCompetitionSeason->putWinPointsAfterExtraTime( Voetbal_Factory::DEFAULT_WINPOINTS_AFTEREXTRATIME );

        $nNrOfTeams = 16;
        $arrDefaultNrOfPoules = Voetbal_Round_Factory::getDefaultNrOfPoules();
        $nNrOfPoules = $arrDefaultNrOfPoules[ $nNrOfTeams ]["nrofpoules"];
        $nNrOfTeamsPerPoule = (int) ( $nNrOfTeams / $nNrOfPoules );
        $nNrOfExtraTeams = $nNrOfTeams % $nNrOfPoules;

        $oRounds = $oCompetitionSeason->getRounds();
        $oRound = Voetbal_Round_Factory::createObject();
        {
            $oRound->putNumber( 0 );
            $oRound->putSemiCompetition( true );
            $oPoules = $oRound->getPoules();
            for ( $nI = 0 ; $nI < $nNrOfPoules ; $nI++ ) {
                $oPoule = Voetbal_Poule_Factory::createObject();
                $oPoule->putNumber($nI);
                $oPlaces = $oPoule->getPlaces();

                $nNrOfTeamsPerPouleTmp = $nNrOfTeamsPerPoule;
                if ($nNrOfExtraTeams > 0) {
                    $nNrOfTeamsPerPouleTmp++;
                    $nNrOfExtraTeams--;
                }
                for ($nJ = 0; $nJ < $nNrOfTeamsPerPouleTmp; $nJ++) {
                    $oPlace = Voetbal_PoulePlace_Factory::createObject();
                    $oPlace->putId( "__NEW__" . $nI . "_" . $nJ );
                    $oPlace->putNumber($nJ);
                    $oPlaces->add( $oPlace );
                }

                $oPoule->putId( $nI );
                $oPoules->add( $oPoule );
            }
        }
        $oRounds->add( $oRound );

        return $oCompetitionSeason;
    }
}

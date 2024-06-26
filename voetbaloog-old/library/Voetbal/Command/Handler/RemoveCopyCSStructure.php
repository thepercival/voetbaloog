<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:44
 */

class Voetbal_Command_Handler_RemoveCopyCSStructure
{
    public function handle( Voetbal_Command_RemoveCopyCSStructure $command )
    {
        $oFromCS = $command->getFromCompetitionSeason();
        $oToCS = $command->getToCompetitionSeason();
        if ( $oFromCS === null )
            throw new Exception( "van-competitieseizoen is niet ingevuld", E_ERROR );

        if ( $oToCS === null )
            throw new Exception( "naar-competitieseizoen is niet ingevuld", E_ERROR );

        if ( $oToCS->hasGames() )
            throw new Exception( "de structuur kan van ".$oToCS->getName()." kan niet overschreven worden omdat er al wedstrijden bestaan", E_ERROR );

        $this->helper( $oFromCS, $oToCS );
    }

    protected function helper( $oFromCS, $oToCS )
    {
        $oCompetitionSeasonDbWriter = Voetbal_CompetitionSeason_Factory::createDbWriter();
        $oRoundDbWriter = Voetbal_Round_Factory::createDbWriter();
        $oPouleDbWriter = Voetbal_Poule_Factory::createDbWriter();
        $oPoulePlaceDbWriter = Voetbal_PoulePlace_Factory::createDbWriter();
        $oQualifyRuleDbWriter = Voetbal_QualifyRule_Factory::createDbWriter();
        $oPPQualifyRuleDbWriter = Voetbal_QualifyRule_PoulePlace_Factory::createDbWriter();

        $arrFromToPoulePlaces = array();

        // Kopieer basisdata
        $oToCS->addObserver( $oCompetitionSeasonDbWriter );
        $oToCS->putPublic( false /* $oFromCS->getPublic() */  );
        $oToCS->putAssociation( $oFromCS->getAssociation() );
        $oToCS->putPromotionRule( $oFromCS->getPromotionRule() );
        $oToCS->putNrOfMinutesGame( $oFromCS->getNrOfMinutesGame() );
        $oToCS->putNrOfMinutesExtraTime( $oFromCS->getNrOfMinutesExtraTime() );
        $oToCS->putWinPointsAfterGame( $oFromCS->getWinPointsAfterGame() );
        $oToCS->putWinPointsAfterExtraTime( $oFromCS->getWinPointsAfterExtraTime() );

        // Kopieer ronden
        $oRoundsTo = $oToCS->getRounds();
        $oRoundsTo->addObserver( $oRoundDbWriter );
        $oRoundsTo->flush(); // cascade delete

        $oQualifyRulesTo = Voetbal_QualifyRule_Factory::createObjects();
        $oQualifyRulesTo->addObserver($oQualifyRuleDbWriter);

        $oPPQualifyRulesTo = Voetbal_QualifyRule_PoulePlace_Factory::createObjects();
        $oPPQualifyRulesTo->addObserver($oPPQualifyRuleDbWriter);

        $oPoulesTo = $oToCS->getPoules();
        $oPoulesTo->addObserver( $oPouleDbWriter );

        $oPreviousRoundTo = null;
        $oRoundsFrom = $oFromCS->getRounds();
        foreach( $oRoundsFrom as $oRoundFrom )
        {
            $oRoundTo = Voetbal_Round_Factory::createObject();
            $oRoundTo->putId( "__NEW__".$oRoundFrom->getId() );
            $oRoundTo->putCompetitionSeason( $oToCS );
            $oRoundTo->putName( $oRoundFrom->getName() );
            $oRoundTo->putNumber( $oRoundFrom->getNumber() );
            $oRoundTo->putSemiCompetition( $oRoundFrom->getSemiCompetition() );
            $oRoundsTo->add( $oRoundTo );

            // Kopieer poules
            $oPoulePlacesTo = $oRoundTo->getPoulePlaces();
            $oPoulePlacesTo->addObserver( $oPoulePlaceDbWriter );

            $oPoulesFrom = $oRoundFrom->getPoules();
            foreach( $oPoulesFrom as $oPouleFrom )
            {
                $oPouleTo = Voetbal_Poule_Factory::createObject();
                $oPouleTo->putId( "__NEW__".$oPouleFrom->getId() );
                $oPouleTo->putRound( $oRoundTo );
                $oPouleTo->putName( $oPouleFrom->getName() );
                $oPouleTo->putNumber( $oPouleFrom->getNumber() );
                $oPoulesTo->add( $oPouleTo );

                // Kopieer pouleplacess
                $oPoulePlacesFrom = $oPouleFrom->getPlaces();
                foreach( $oPoulePlacesFrom as $oPoulePlaceFrom )
                {
                    $oPoulePlaceTo = Voetbal_PoulePlace_Factory::createObject();
                    $oPoulePlaceTo->putId( "__NEW__".$oPoulePlaceFrom->getId() );
                    $oPoulePlaceTo->putPoule( $oPouleTo );
                    $oPoulePlaceTo->putNumber( $oPoulePlaceFrom->getNumber() );
                    $oPoulePlaceTo->putPenaltyPoints( 0 );
                    $oPoulePlacesTo->add( $oPoulePlaceTo );

                    $arrFromToPoulePlaces[ $oPoulePlaceFrom->getId() ] = $oPoulePlaceTo;
                }
            }

            // Kopieer qualifyrules
            $oFromQualifyRulesFrom = $oRoundFrom->getFromQualifyRules();

            foreach( $oFromQualifyRulesFrom as $oFromQualifyRuleFrom )
            {
                $oQualifyRule = Voetbal_QualifyRule_Factory::createObject();
                $oQualifyRule->putId( "__NEW__" . $oFromQualifyRuleFrom->getId() );
                $oQualifyRule->putFromRound( $oPreviousRoundTo );
                $oQualifyRule->putToRound( $oRoundTo );
                $oQualifyRule->putConfigNr( $oFromQualifyRuleFrom->getConfigNr() );
                $oQualifyRulesTo->add( $oQualifyRule );

                $oPoulePlaceRulesFrom = $oFromQualifyRuleFrom->getPoulePlaceRules();
                foreach( $oPoulePlaceRulesFrom as $oPoulePlaceRuleFrom )
                {
                    $oPPQualifyRule = Voetbal_QualifyRule_PoulePlace_Factory::createObject();
                    $oPPQualifyRule->putId( "__NEW__" . $oPoulePlaceRuleFrom->getId());
                    $oPPQualifyRule->putFromPoulePlace( $arrFromToPoulePlaces[ $oPoulePlaceRuleFrom->getFromPoulePlace()->getId() ] );
                    $oPPQualifyRule->putToPoulePlace( $oPoulePlaceRuleFrom->getToPoulePlace() !== null ? $arrFromToPoulePlaces[ $oPoulePlaceRuleFrom->getToPoulePlace()->getId() ] : null );
                    $oPPQualifyRule->putQualifyRule($oQualifyRule);
                    $oPPQualifyRulesTo->add( $oPPQualifyRule );
                }
            }
            $oPreviousRoundTo = $oRoundTo;
        }

        $oCompetitionSeasonDbWriter->write();
        $oRoundDbWriter->write();
        $oPouleDbWriter->write();
        $oPoulePlaceDbWriter->write();
        $oQualifyRuleDbWriter->write();
        $oPPQualifyRuleDbWriter->write();

        // check config-item if teams should be created here, (only for fctoernooi )
        // if ( false /* and check config-item */ and $arrTeamsOldFirstRound !== null ) {
           //  $supplementTeamsCommand = new Voetbal_Command_SupplementTeams( $oRounds->first(), $arrTeamsOldFirstRound );
            //  $command->getBus()->handle($supplementTeamsCommand);
        // }
    }
}
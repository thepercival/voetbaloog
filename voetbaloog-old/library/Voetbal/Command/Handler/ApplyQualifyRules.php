<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 4-12-15
 * Time: 16:11
 */

class Voetbal_Command_Handler_ApplyQualifyRules
{
    public function handle( Voetbal_Command_ApplyQualifyRules $command) {

        $oPoule = $command->getGame()->getPoule();
        $oRound = $oPoule->getRound();

        if ( $oPoule->getState() != Voetbal_Factory::STATE_PLAYED )
            return;

        $oPoulePlaceDbWriter = Voetbal_PoulePlace_Factory::createDbWriter();

        $oRankedPoulePlaces = $oPoule->getPlacesByRank();

        $oPoulePlaces = $oPoule->getPlaces();
        /** @var Voetbal_PoulePlace $oPoulePlace */
        foreach ( $oPoulePlaces as $oPoulePlace )
        {
            $oQualifyRulePP = $oPoulePlace->getToQualifyRule();
            if ( $oQualifyRulePP === null )
                continue;

            $oQualifyRule = $oQualifyRulePP->getQualifyRule();
            if ( $oQualifyRule->isSingle() )
            {
                $oToPoulePlace = $oQualifyRulePP->getToPoulePlace();
                $oToPoulePlace->addObserver( $oPoulePlaceDbWriter );

                $oQualifiedTeam = $oRankedPoulePlaces[ $oPoulePlace->getNumber() + 1 ]->getTeam();
                $oToPoulePlace->putTeam( $oQualifiedTeam );
            }
            else
            {
                if ( $oRound->getState() != Voetbal_Factory::STATE_PLAYED )
                    continue;

                $oRankedFromPlaces = Voetbal_PoulePlace_Factory::createObjects();
                {
                    $oFromPoulePlaces = $oQualifyRule->getFromPoulePlaces();
                    foreach( $oFromPoulePlaces as $oFromPoulePlace )
                    {
                        $oRankedPlacesTmp = $oFromPoulePlace->getPoule()->getPlacesByRank()[ $oFromPoulePlace->getNumber() + 1 ];
                        $oRankedFromPlaces->add( $oRankedPlacesTmp );
                    }
                }

                Voetbal_Ranking::putPromotionRule( $oRound->getCompetitionSeason()->getPromotionRule() );
                Voetbal_Ranking::putGameStates( Voetbal_Factory::STATE_PLAYED );
                Voetbal_Ranking::updatePoulePlaceRankings( null, $oRankedFromPlaces );
                $oRankedPlaces = Voetbal_Ranking::getPoulePlacesByRanking( null, $oRankedFromPlaces );

                $oToPlaces = $oQualifyRule->getToPoulePlaces();
                $nNrOfToPlaces = $oToPlaces->count();
                $arrConfigs = $oQualifyRule->getConfig();

                $nTotalRank = 0; $nCount = 1;
                foreach( $oRankedPlaces as $oRankedPlace ) {

                    if ( $nCount++ > $nNrOfToPlaces ) { break; }
                    $nTotalRank += pow( 2, $oRankedPlace->getPoule()->getNumber() );
                }

                $arrConfig = $arrConfigs[ $nTotalRank ];

                $nCount = 1;
                foreach( $oRankedPlaces as $oRankedPlace )
                {
                    if ( $nCount++ > $nNrOfToPlaces ) { break; }
                    $nIndex = array_search( pow( 2, $oRankedPlace->getPoule()->getNumber() ), $arrConfig );
                    $nI = 0;
                    foreach ( $oToPlaces as $oToPlace )
                    {
                        if ( $nI++ === $nIndex ) {
                            $oToPlace->addObserver( $oPoulePlaceDbWriter );
                            $oToPlace->putTeam( $oRankedPlace->getTeam() );
                        }
                    }
                }
            }
        }
        $oPoulePlaceDbWriter->write();
    }
}


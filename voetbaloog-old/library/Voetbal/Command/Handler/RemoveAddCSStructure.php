<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:44
 */

class Voetbal_Command_Handler_RemoveAddCSStructure
{
    public function handle( Voetbal_Command_RemoveAddCSStructure $command )
    {
        $oRoundDbWriter = Voetbal_Round_Factory::createDbWriter();
        $oPouleDbWriter = Voetbal_Poule_Factory::createDbWriter();
        $oPoulePlaceDbWriter = Voetbal_PoulePlace_Factory::createDbWriter();
        $oQualifyRuleDbWriter = Voetbal_QualifyRule_Factory::createDbWriter();
        $oPPQualifyRuleDbWriter = Voetbal_QualifyRule_PoulePlace_Factory::createDbWriter();

        $oRounds = $command->getCompetitionSeason()->getRounds();
        $arrTeamsOldFirstRound = null;
        if ( $command->getCompetitionSeason()->getAssociation() === null and $oRounds->first() !== null ) {
            $arrTeamsOldFirstRound = $oRounds->first()->getTeamsByPlace();
        }

        $oRounds->addObserver($oRoundDbWriter);
        $oRounds->flush(); // cascade delete

        $oPoules = Voetbal_Poule_Factory::createObjects();
        $oPoules->addObserver($oPouleDbWriter);

        $oPoulePlaces = Voetbal_PoulePlace_Factory::createObjects();
        $oPoulePlaces->addObserver($oPoulePlaceDbWriter);

        $oQualifyRules = Voetbal_QualifyRule_Factory::createObjects();
        $oQualifyRules->addObserver($oQualifyRuleDbWriter);

        $oPPQualifyRules = Voetbal_QualifyRule_PoulePlace_Factory::createObjects();
        $oPPQualifyRules->addObserver($oPPQualifyRuleDbWriter);

        $nIdIt = 0;
        // var_dump( $arrCompetitionSeason );
        $oPreviousRound = null;
        $arrStructure = $command->getCSStructure();
        $arrRounds = $arrStructure["rounds"];
        foreach ($arrRounds as $arrRound) {
            $oRound = Voetbal_Round_Factory::createObject();
            $sId = array_key_exists( '$$hashKey', $arrRound ) ? $arrRound['$$hashKey'] : "__NEW__" . $nIdIt++;
            $oRound->putId( $sId );
            $oRound->putCompetitionSeason( $command->getCompetitionSeason() );
            // $oRound->putName( "tmp".$arrRound['$$hashKey'] );
            $oRound->putNumber($arrRound["number"]);
            $oRound->putSemiCompetition( $arrRound["semicompetition"] );
            $oRounds->add($oRound);

            $arrPoules = $arrRound["poules"];
            foreach ($arrPoules as $arrPoule) {
                // $sHashKey = "WINNER"  // $arrRound["type"]
                $sId = array_key_exists( '$$hashKey', $arrPoule ) ? $arrPoule['$$hashKey'] : "__NEW__" . $nIdIt++;
                $oPoule = Voetbal_Poule_Factory::createObject();
                $oPoule->putId( $sId );
                $oPoule->putNumber($arrPoule["number"]);
                $oPoule->putRound($oRound);
                // $oPoule->putName();
                $oPoules->add($oPoule);

                // Kopieer pouleplaces
                $arrPoulePlaces = $arrPoule["places"];
                foreach ($arrPoulePlaces as $arrPoulePlace) {
                    $sId = array_key_exists( '$$hashKey', $arrPoulePlace ) ? $arrPoulePlace['$$hashKey'] : "__NEW__" . ( array_key_exists( 'id', $arrPoulePlace ) ? $arrPoulePlace['id'] : $nIdIt++ );
                    $oPoulePlace = Voetbal_PoulePlace_Factory::createObject();
                    $oPoulePlace->putId($sId);
                    $oPoulePlace->putPoule($oPoule);
                    $oPoulePlace->putNumber($arrPoulePlace["number"]);
                    $oPoulePlace->putPenaltyPoints(0);
                    $oPoulePlaces->add($oPoulePlace);
                }
            }

            // Kopieer qualifyrules
            if ( array_key_exists( "fromqualifyrules", $arrRound ) )
            {
                $arrQualifyRules = $arrRound["fromqualifyrules"];
                foreach ($arrQualifyRules as $arrQualifyRule) {
                    $oQualifyRule = Voetbal_QualifyRule_Factory::createObject();
                    $oQualifyRule->putId($oPreviousRound->getId() . $oRound->getId() . $oQualifyRules->count());
                    $oQualifyRule->putFromRound($oPreviousRound);
                    $oQualifyRule->putToRound($oRound);
                    $oQualifyRule->putConfigNr($arrQualifyRule["confignr"]);
                    $oQualifyRules->add($oQualifyRule);

                    for ($nI = 0; $nI < count($arrQualifyRule["frompouleplaces"]); $nI++) {
                        $arrFromPoulePlace = $arrQualifyRule["frompouleplaces"][$nI];
                        $sFromPoulePlaceHashKey = $arrFromPoulePlace['$$hashKey'];
                        $oFromPoulePlace = $oPoulePlaces[ $sFromPoulePlaceHashKey ];
                        // var_dump( 'sFromPoulePlaceHashKey:' . $sFromPoulePlaceHashKey );
                        if ($oFromPoulePlace === null) {
                            $oFromPoulePlace = $oPoulePlaces["__NEW__" . $sFromPoulePlaceHashKey];
                        }
                        if ($oFromPoulePlace === null) {
                            throw new Exception("kan from-pouleplace(".$sFromPoulePlaceHashKey.") niet vinden", E_ERROR);
                        }

                        $oToPoulePlace = null;
                        if (array_key_exists($nI, $arrQualifyRule["topouleplaces"])) {
                            $arrToPoulePlace = $arrQualifyRule["topouleplaces"][$nI];
                            $sToPoulePlaceHashKey = $arrToPoulePlace['$$hashKey'];
                            $oToPoulePlace = $oPoulePlaces[$sToPoulePlaceHashKey];
                            // var_dump( 'sToPoulePlaceHashKey:' . $sToPoulePlaceHashKey );
                        }
                        // if ($oToPoulePlace === null) {
                           // $oToPoulePlace = $oPoulePlaces["__NEW__" . $sToPoulePlaceHashKey];
                        //}
                        // $oToPoulePlace can be null

                        $oPPQualifyRule = Voetbal_QualifyRule_PoulePlace_Factory::createObject();
                        $oPPQualifyRule->putId($oFromPoulePlace->getId() . "-" . $oFromPoulePlace->getId());
                        $oPPQualifyRule->putFromPoulePlace($oFromPoulePlace);
                        $oPPQualifyRule->putToPoulePlace($oToPoulePlace);
                        $oPPQualifyRule->putQualifyRule($oQualifyRule);
                        $oPPQualifyRules->add($oPPQualifyRule);
                    }
                }
            }

            $oPreviousRound = $oRound;
        }

        $oRoundDbWriter->write();
        $oPouleDbWriter->write();
        $oPoulePlaceDbWriter->write();
        $oQualifyRuleDbWriter->write();
        $oPPQualifyRuleDbWriter->write();

        // check config-item if teams should be created here, (only for fctoernooi )
//        if ( false /* and check config-item */ and $arrTeamsOldFirstRound !== null ) {
//            $supplementTeamsCommand = new Voetbal_Command_SupplementTeams( $oRounds->first(), $arrTeamsOldFirstRound );
//           //  $command->getBus()->handle($supplementTeamsCommand);
//        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:44
 */

class Voetbal_Command_Handler_UpdateFirstRoundTeams
{
    public function handle( Voetbal_Command_UpdateFirstRoundTeams $command )
    {
        $oPoulePlaceDbWriter = Voetbal_PoulePlace_Factory::createDbWriter();
        $oTeamDbWriter = Voetbal_Team_Factory::createDbWriter();

        // $command->getRound()

        // $nIdIt = 0;
        // var_dump( $arrStructure );
        // $oPreviousRound = null;
        $arrStructure = $command->getStructure();
        $arrPoules = $arrStructure["poules"];
        foreach ($arrPoules as $arrPoule) {
            $arrPoulePlaces = $arrPoule["places"];
            foreach ($arrPoulePlaces as $arrPoulePlace) {
                $oPoulePlace = Voetbal_PoulePlace_Factory::createObjectFromDatabase( (int) $arrPoulePlace["id"] );

                $oTeam = null;
                $arrTeam = $arrPoulePlace["team"];
                if ( $arrTeam !== null )
                    $oTeam = Voetbal_Team_Factory::createObjectFromDatabase( (int) ( is_array( $arrTeam ) ? $arrTeam["id"] : $arrTeam ) );

                if ( $command->getEditMode() === "update" ) {
                    $oTeam->addObserver( $oTeamDbWriter );
                    $oTeam->putName( substr( $arrTeam["name"], 0, Voetbal_Team::MAX_NAME_LENGTH ) );
                }
                else /* if ( $command->getEditMode() === "assign" ) */ {
                    $oPoulePlace->addObserver( $oPoulePlaceDbWriter );
                    $oPoulePlace->putTeam( $oTeam );
                }
            }
        }
        $oPoulePlaceDbWriter->write();
        $oTeamDbWriter->write();
    }
}
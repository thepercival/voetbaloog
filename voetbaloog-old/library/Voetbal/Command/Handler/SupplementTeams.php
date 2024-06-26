<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 4-12-15
 * Time: 16:11
 */

class Voetbal_Command_Handler_SupplementTeams
{
    // bij het toevoegen van een config
    // dan simpel voor elke pouleplace zonder team een team genereren en koppelen aan pouleplace

    // bij het updaten van de structuur, eerst alle teams ophalen die voor het opslaan aan de cs hangen, incl. poule en placenr
    // vervolgens strucuur updaten
    // daarna teams weer koppelen aan bestaande structuur en eventueel aanvullen met teams
    // of oude teams die niet meer gebruikt worden verwijderen.
    public function handle( Voetbal_Command_SupplementTeams $command) {

        $oRound = $command->getRound();
        $arrTeams = $command->getTeams();

        $oPoulePlaceDbWriter = Voetbal_PoulePlace_Factory::createDbWriter();
        $oTeamDbWriter = Voetbal_Team_Factory::createDbWriter();
        $oTeamsToRemove = $oRound->getTeams();
        $oTeamsToAdd = Voetbal_Team_Factory::createObjects();
        $oTeamsToAdd->addObserver( $oTeamDbWriter );
        $nHighestNumberDefaultTeamName = $this->getHighestNumberDefaultTeamName();

        foreach( $oRound->getPoules() as $oPoule )
        {
            foreach( $oPoule->getPlaces() as $oPoulePlace )
            {
                $oTeam = $oPoulePlace->getTeam();
                if ( $oTeam !== null ) {
                    $oTeamsToRemove->remove($oTeam);
                    continue;
                }

                if ( array_key_exists( $oPoule->getNumber(), $arrTeams )
                    and array_key_exists( $oPoulePlace->getNumber(), $arrTeams[ $oPoule->getNumber() ] )
                ) // get team from cs
                {
                    $oTeam = $arrTeams[ $oPoule->getNumber() ][ $oPoulePlace->getNumber() ];
                    $oTeamsToRemove->remove($oTeam);
                }
                else // create team
                {
                    $oTeam = Voetbal_Team_Factory::createObject();
                    $oTeam->putId( "__NEW__" . ++$nHighestNumberDefaultTeamName );
                    $oTeam->putName( "team " . $nHighestNumberDefaultTeamName );
                    $oTeam->putAbbreviation( "tm" . $nHighestNumberDefaultTeamName );
                    $oTeam->putAssociation( null );
                    $oTeamsToAdd->add( $oTeam );
                }

                $oPoulePlace->addObserver( $oPoulePlaceDbWriter );
                $oPoulePlace->putTeam( $oTeam );
            }
        }

        $oTeamsToRemove->addObserver( $oTeamDbWriter );
        $oTeamsToRemove->flush();

        $oTeamDbWriter->write();
        $oPoulePlaceDbWriter->write();
    }

    private function getHighestNumberDefaultTeamName()
    {
        return 0;
    }
}


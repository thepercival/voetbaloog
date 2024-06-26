<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:44
 */

class Voetbal_Command_Handler_RemoveAddCSGames
{
    public function handle( Voetbal_Command_RemoveAddCSGames $command )
    {
        $oCompetitionSeason = $command->getCompetitionSeason();
        $this->createGamesCheck( $oCompetitionSeason );

        $this->removeGames( $oCompetitionSeason );

        $oStartDateTime = $command->getStartDateTime();
        $this->createGames( $oCompetitionSeason, $oStartDateTime );
    }

    /*
     * 1 Alle pouleplekken van ronde 1 moeten een team hebben
    * 2 Alle pouleplekken van de volgende rondes moeten een kwalificerende pouleplek hebben
    */
    protected function createGamesCheck( $oCompetitionSeason )
    {
        $oRounds = $oCompetitionSeason->getRounds();
        if ( $oRounds->count() === 0 )
            throw new Exception( "er zijn geen rondes om wedstrijden voor aan te maken", E_ERROR );

        foreach ( $oRounds as $oRound )
        {
            $oPoules = $oRound->getPoules();
            if ( $oPoules->count() === 0 )
                throw new Exception( "voor ronde ".$oRound->getName()." zijn er geen poules aangemaakt", E_ERROR );

            foreach ( $oPoules as $oPoule )
            {
                $oPoulePlaces = $oPoule->getPlaces();

                if ( /*( !$oRound->isLastRound() and $oPoulePlaces->count() < 2 )
                        or*/ ( $oRound->isLastRound() and $oPoulePlaces->count() < 1 )
                )
                    throw new Exception( "voor ronde ".$oRound->getName().", poule ".$oPoule->getName()." zijn er niet genoeg pouleplekken aangemaakt", E_ERROR );

                foreach ( $oPoulePlaces as $sPoulePlaceId => $oPoulePlace )
                {
                    if ( $oRound->isFirstRound() and $oPoulePlace->getTeam() === null )
                        throw new Exception( "er zijn plekken in de eerste ronde ( poule ".$oPoule->getName()." ) waar nog geen team aan gekoppeld is", E_ERROR );

                    if ( $oRound->isFirstRound() === false and $oPoulePlace->getFromQualifyRule() === null )
                        throw new Exception( "voor ronde ".$oRound->getName().", poule ".$oPoule->getName()." zijn er pouleplekken waarvoor niet gekwalificeerd kan worden", E_ERROR );
                }
            }
        }
    }

    protected function createGames( $oCompetitionSeason, $oStartDateTime )
    {
        // Create games for competiotionseason here
        $oDbWriter = Voetbal_Game_Factory::createDbWriter();

        $oGames = Voetbal_Game_Factory::createObjects();
        $oGames->addObserver( $oDbWriter );

        $oRounds = $oCompetitionSeason->getRounds();
        foreach ( $oRounds as $oRound )
        {
            $bSemiCompetition = $oRound->getSemiCompetition();
            $oPoules = $oRound->getPoules();
            foreach ( $oPoules as $oPoule )
            {
                $oPoulePlaces = $oPoule->getPlaces();
                $nStartGameNrReturnGames = $oPoulePlaces->count() - 1;
                $arrPoulePlaces = array(); foreach( $oPoulePlaces as $oPoulePlace ) { $arrPoulePlaces[] = $oPoulePlace; }

                $arrSchedule = $this->generateRRSchedule( $arrPoulePlaces );

                foreach ( $arrSchedule as $nGameNumber => $arrGames )
                {
                    foreach ( $arrGames as $nViewOrder => $arrGame )
                    {
                        if ( $arrGame[0] === null or $arrGame[1] === null )
                            continue;
                        $oGame = Voetbal_Game_Factory::createObjectExt( $oStartDateTime, $arrGame[0], $arrGame[1], null, $nGameNumber + 1, $nViewOrder );
                        $oGames->add( $oGame );
                        if ( $bSemiCompetition !== true )
                        {
                            $oReturnGame = Voetbal_Game_Factory::createObjectExt( $oStartDateTime, $arrGame[1], $arrGame[0], null, $nStartGameNrReturnGames + $nGameNumber + 1, $nViewOrder );
                            $oGames->add( $oReturnGame );
                        }
                    }
                }
            }
        }

        try
        {
            $oDbWriter->write();
        }
        catch ( Exception $e )
        {
            throw new Exception ( "wedstrijden konden niet worden aangemaakt : " . $e->getMessage(), E_ERROR );
        }
    }

    protected function removeGames( $oCompetitionSeason )
    {
        // remove games for competitionseason here
        $oGameDbWriter = Voetbal_Game_Factory::createDbWriter();
        // when games are deleted, the teams from the pouleplaces which have a previous pouleplace should be removed also.
        $oPoulePlaceDbWriter = Voetbal_PoulePlace_Factory::createDbWriter();

        $oRounds = $oCompetitionSeason->getRounds();
        foreach ( $oRounds as $oRound )
        {
            $oPoules = $oRound->getPoules();
            foreach ( $oPoules as $oPoule )
            {
                $oPoulePlaces = $oPoule->getPlaces();
                foreach ( $oPoulePlaces as $oPoulePlace )
                {
                    $oGames = $oPoulePlace->getGames();
                    $oGames->addObserver( $oGameDbWriter );
                    $oGames->flush();

                    if ( $oPoulePlace->getFromQualifyRule() !== null and $oPoulePlace->getTeam() !== null )
                    {
                        $oPoulePlace->addObserver( $oPoulePlaceDbWriter );
                        $oPoulePlace->putTeam( null );
                    }
                }
            }
        }

        try
        {
            $oGameDbWriter->write();
            $oPoulePlaceDbWriter->write();
        }
        catch ( Exception $e )
        {
            throw new Exception( "wedstrijden konden niet verwijderd worden : " . $e->getMessage(), E_ERROR );
        }
    }

    /**
     * Generate a round robin schedule from a list of players
     *
     * @param array     $players	A list of players
     * @param bool      $rand		Set TRUE to randomize the results
     * @return array	Array of matchups separated by sets
     */
    function generateRRSchedule(array $players, $rand = false): array {
        $numPlayers = count($players);

        // add a placeholder if the count is odd
        if($numPlayers%2) {
            $players[] = null;
            $numPlayers++;
        }

        // calculate the number of sets and matches per set
        $numSets = $numPlayers-1;
        $numMatches = $numPlayers/2;

        $matchups = array();

        // generate each set
        for($j = 0; $j < $numSets; $j++) {
            // break the list in half
            $halves = array_chunk($players, $numMatches);
            // reverse the order of one half
            $halves[1] = array_reverse($halves[1]);
            // generate each match in the set
            for($i = 0; $i < $numMatches; $i++) {
                // match each pair of elements
                $matchups[$j][$i][0] = $halves[0][$i];
                $matchups[$j][$i][1] = $halves[1][$i];
            }
            // remove the first player and store
            $first = array_shift($players);
            // move the second player to the end of the list
            $players[] = array_shift($players);
            // place the first item back in the first position
            array_unshift($players, $first);
        }

        // shuffle the results if desired
        if($rand) {
            foreach($matchups as &$match) {
                shuffle($match);
            }
            shuffle($matchups);
        }

        return $matchups;
    }

}
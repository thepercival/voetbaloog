<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: FCUpdate.php 220 2014-12-16 19:27:30Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Extern_System_SofaScore extends Voetbal_Extern_System_Abstract implements Voetbal_Extern_System_Interface
{
    CONST NAME = "sofascore";
    CONST URL = "https://www.sofascore.com/";
    CONST APIURL = "https://api.sofascore.com/api/v1/";

    protected $m_nCacheTimeForGame;

    /**
     * @inheritDoc
     */
	public function getTeams( string $sCompetitionId, string $sSeasonId ): array
	{
		$oCache = ZendExt_Cache::getCache( $this->getCacheTimeForTeams(), APPLICATION_PATH . "/cache/", 25 );

		$sCacheId = get_called_class() . "_getTeams_".$sCompetitionId."_".$sSeasonId;
		$sCacheId = str_replace("-","_", $sCacheId );
		$sJson = $oCache->load( $sCacheId );
		if( APPLICATION_ENV === "production" or $sJson === false )
		{
			$sJson = $this->getContentForUrl( $this->getUrlForTeams( $sCompetitionId, $sSeasonId ) );
			$oCache->save( $sJson, $sCacheId );
		}

        $arrCompetitionSeason = json_decode( $sJson );

		$arrTeams = array();
        foreach( $arrCompetitionSeason->teams as $team ) {
            $arrTeams[] = $this->createTeam( (string)$team->id, $team->name );
        }

		return $arrTeams;
	}

    /**
     * @inheritDoc
     */
	public function getUrlForTeams( string $sCompetitionId, string $sSeasonId ): string
	{
        return self::URL . "u-tournament/".$sCompetitionId."/season/".$sSeasonId."/json".$this->getUrlPostfix();
	}

    /**
     * @inheritDoc
     */
	public function getCacheTimeForTeams(): int
	{
		return 60 * 60 * 24;
	}

    /**
     * @inheritDoc
     */
	public function getGames( string $sCompetitionId, string $sSeasonId, int $nGameRoundNumber = null ): array
	{
		$oCache = ZendExt_Cache::getCache( $this->getCacheTimeForGames(), APPLICATION_PATH . "/cache/", 25 );

		$sCacheId = get_called_class() . "_getGames_".$sCompetitionId."_".$sSeasonId."_".$nGameRoundNumber;
		$sCacheId = str_replace("-","_", $sCacheId );
		$sJson = $oCache->load( $sCacheId );
        $oJson = null;
		if( $sJson === false )
		{
            $sJson = $this->getContentForUrl( static::getUrlForGames( $sCompetitionId, $sSeasonId, $nGameRoundNumber ) );
            $oJson = json_decode( $sJson );
            if( !($oJson instanceof stdClass) ) {
                throw new \Exception("got wrong result from sofascore: " . $sJson, E_ERROR );
            }
			$oCache->save( $sJson, $sCacheId );
		} else {
            $oJson = json_decode( $sJson );
        }

		if( property_exists( $oJson, "error" ) ) {
            throw new \Exception("sofascore fout: \"" . $oJson->error->message . "\"", E_ERROR);
        }

        $arrGames = [];
        foreach( $oJson->events as $oJsonEvent ) {
            $arrGames[] = $this->getGameFromJson( $oJsonEvent );
        }

        return $this->removePostponedIfNotUnique( $arrGames );
	}

	protected function removePostponedIfNotUnique( array $arrGames ): array {
        $arrPostponedGames = array_filter( $arrGames, function( $oGame ) {
            return $oGame->getState() === Voetbal_Factory::STATE_POSTPONED;
        });
        $arrNonPostponedGames = array_filter( $arrGames, function( $oGame ) {
            return $oGame->getState() !== Voetbal_Factory::STATE_POSTPONED;
        });
        // remove postponed games which are not unique
        $arrPostponedGames = array_filter( $arrPostponedGames, function( $oPostponedGame ) use ( $arrNonPostponedGames ) {
            $arrNonUniqueGames = array_filter( $arrNonPostponedGames, function( $oNonPostponedGame ) use ( $oPostponedGame ) {
                return $oPostponedGame->getHomeTeam()->getId() === $oNonPostponedGame->getHomeTeam()->getId()
                    && $oPostponedGame->getAwayTeam()->getId() === $oNonPostponedGame->getAwayTeam()->getId();
            });
            return count( $arrNonUniqueGames ) === 0;

        });
        return array_merge( $arrNonPostponedGames, $arrPostponedGames );
    }

	protected function getGameFromJson( stdClass $oJson ): Voetbal_Extern_GameExt {
        $oGame = new Voetbal_Extern_GameExt( (string)$oJson->id );
        $oGame->putRoundNumber( $oJson->roundInfo->round );
        $date = Agenda_Factory::createDateTime();
        $date->setTimestamp($oJson->startTimestamp);
        $oGame->putStartDateTime( $date );
        $oGame->putHomeTeam( $this->createTeam(  (string)$oJson->homeTeam->id, $oJson->homeTeam->name ) );
        $oGame->putAwayTeam( $this->createTeam(  (string)$oJson->awayTeam->id, $oJson->awayTeam->name ) );
        $nState = Voetbal_Factory::STATE_CREATED;
        if( $oJson->status->code === 100 ) {
            $nState = Voetbal_Factory::STATE_PLAYED;
        } else if( $oJson->status->code === 60 || $oJson->status->code === 70/*canceled*/  ) {
            $nState = Voetbal_Factory::STATE_POSTPONED;
        }
        $oGame->putState( $nState );
        if( $nState === Voetbal_Factory::STATE_PLAYED ) {
            $oGame->putHomeGoals( $oJson->homeScore->normaltime );
            $oGame->putAwayGoals( $oJson->awayScore->normaltime );
        }
        return $oGame;
    }

    /**
     * @inheritDoc
     */
	public function getUrlForGames( string $sCompetitionId, string $sSeasonId, int $nGameRoundId = null ): string
	{
        // https://api.sofascore.com/api/v1/unique-tournament/35/season/28210/events/round/2
        return self::APIURL . "unique-tournament/".$sCompetitionId."/season/".$sSeasonId."/events/round/".$nGameRoundId; // .$this->getUrlPostfix();
        // return self::URL . "u-tournament/".$sCompetitionId."/season/".$sSeasonId."/matches/round/".$nGameRoundId.$this->getUrlPostfix();
	}

    /**
     * @inheritDoc
     */
	public function getCacheTimeForGames(): int
	{
		return 60 * 60 * 24;
	}

//
//	// for gameid
    public function getGame( string $sCompetitionId, string $sSeasonId, int $nGameId
        //, Agenda_TimeSlot $oDefaultPlayerPeriodTimeSlot
    ): Voetbal_Extern_GameExt
	{
        $oCache = ZendExt_Cache::getCache( $this->getCacheTimeForGame(), APPLICATION_PATH . "/cache/", 25 );

        $sCacheId = get_called_class() . "_getGame_".$sCompetitionId."_".$sSeasonId."_".$nGameId;
        $sCacheId = str_replace("-","_", $sCacheId );
        $sJson = $oCache->load( $sCacheId );
        $sUrlForGame = static::getUrlForGame( $sCompetitionId, $sSeasonId, $nGameId );
        if( /*APPLICATION_ENV === "production" or*/ $sJson === false )
        {
            $sJson = $this->getContentForUrl( $sUrlForGame );
            $oCache->save( $sJson, $sCacheId );
        }
        $oJsonGame = json_decode( $sJson );
        if( property_exists( $oJsonGame, "error" ) ) {
            throw new \Exception("sofascore fout: \"" . $oJsonGame->error->message . "\"", E_ERROR);
        }
        if( !property_exists( $oJsonGame, "event" ) || $oJsonGame->event === null ) {
            throw new \Exception("sofascore fout: error fetching \"" . $sUrlForGame . "\"", E_ERROR);
        }
        $oGame = $this->getGameFromJson( $oJsonGame->event );

        if( !$this->getGameParticipations( $oGame, $sCompetitionId, $sSeasonId, $nGameId ) ) {
            return $oGame;
        }
        if( !$this->getGameEvents( $oGame, $sCompetitionId, $sSeasonId, $nGameId ) ) {
            return $oGame;
        }

        return $oGame;
	}

//	// https://api.sofascore.com/api/v1/event/8805140/incidents
//    // is new url
//    protected function getGameParticipationsOld( Voetbal_Extern_GameExt $oGame, string $sCompetitionId, string $sSeasonId, int $nGameId ): bool {
//        $oCache = ZendExt_Cache::getCache( $this->getCacheTimeForGame(), APPLICATION_PATH . "/cache/", 25 );
//
//        $sCacheId = get_called_class() . "_getGame_".$sCompetitionId."_".$sSeasonId."_".$nGameId."_stats";
//        $sCacheId = str_replace("-","_", $sCacheId );
//        $sJsonStats = $oCache->load( $sCacheId );
//        if( $sJsonStats === false )
//        {
//            $sJsonStats = $this->getContentForUrl( static::getUrlForGameStats( $nGameId ) );
//            $oCache->save( $sJsonStats, $sCacheId );
//        }
//        $oJsonStats = json_decode( $sJsonStats );
//        if( property_exists( $oJsonStats, "error" ) ) {
//            return false;
//        }
//        if( count($oJsonStats->players) === 0 ) {
//            return false;
//        }
//
//        foreach( $oJsonStats->players as $oJsonPlayer ) {
//
//            $oTeam = $this->createTeam( $oJsonPlayer->team->id, $oJsonPlayer->team->name );
//            $oPerson = $this->createPerson( $oJsonPlayer->player );
//
//            $oPlayerPeriod = new Voetbal_Extern_PlayerPeriod( $oTeam, $oPerson );
//            $nLine = $this->getLine( $oJsonPlayer->groups->summary->items->position->raw );
//            $oPlayerPeriod->putLine( $nLine );
//            $oParticipation = new Voetbal_Extern_Game_Participation( $oGame, $oPlayerPeriod );
//        }
//
//        return true;
//    }

	protected function getGameParticipations( Voetbal_Extern_GameExt $oGame, string $sCompetitionId, string $sSeasonId, int $nGameId ) {
        $oCache = ZendExt_Cache::getCache( $this->getCacheTimeForGame(), APPLICATION_PATH . "/cache/", 25 );

        $sCacheId = get_called_class() . "_getGame_".$sCompetitionId."_".$sSeasonId."_".$nGameId."_lineups";
        $sCacheId = str_replace("-","_", $sCacheId );
        $sJsonLineups = $oCache->load( $sCacheId );
        if( $sJsonLineups === false )
        {
            $sJsonLineups = $this->getContentForUrl( static::getUrlForGameParticipations( $nGameId ) );
            $oCache->save( $sJsonLineups, $sCacheId );
        }
        $oJsonLineups = json_decode( $sJsonLineups );
        if( property_exists( $oJsonLineups, "error" ) ) {
            return false;
        }

        $addParticipations = function( Voetbal_Extern_Team $oTeam, array $arrJsonPlayers ) use ( $oGame ) {
            foreach( $arrJsonPlayers as $oJsonPlayer ) {
                if( count((array)$oJsonPlayer->statistics) === 0 ) {
                    continue;
                }
                $oPerson = $this->createPerson( $oJsonPlayer->player );

                $oPlayerPeriod = new Voetbal_Extern_PlayerPeriod( $oTeam, $oPerson );

                $lineAsString = null;
                if( property_exists($oJsonPlayer, 'player') &&
                    property_exists($oJsonPlayer->player, 'position') ) {
                    $lineAsString = $oJsonPlayer->player->position;
                }
                if( $lineAsString === null && property_exists($oJsonPlayer, 'position') ) {
                    $lineAsString = $oJsonPlayer->position;
                }
                $oPlayerPeriod->putLine( $this->getLineFromPosition( $lineAsString ) );
                new Voetbal_Extern_Game_Participation( $oGame, $oPlayerPeriod );
            };
        };
        $addParticipations( $oGame->getHomeTeam(), $oJsonLineups->home->players );
        $addParticipations( $oGame->getAwayTeam(), $oJsonLineups->away->players );
        return true;
    }

    protected function getGameEvents( Voetbal_Extern_GameExt $oGame, string $sCompetitionId, string $sSeasonId, int $nGameId ) {
        $oCache = ZendExt_Cache::getCache( $this->getCacheTimeForGame(), APPLICATION_PATH . "/cache/", 25 );

        $sCacheId = get_called_class() . "_getGame_".$sCompetitionId."_".$sSeasonId."_".$nGameId."_events";
        $sCacheId = str_replace("-","_", $sCacheId );
        $sJsonEvents = $oCache->load( $sCacheId );
        if( $sJsonEvents === false )
        {
            $sJsonEvents = $this->getContentForUrl( static::getUrlForGameEvents( $nGameId ) );
            $oCache->save( $sJsonEvents, $sCacheId );
        }
        $oJsonEvents = json_decode( $sJsonEvents );
        if( $oJsonEvents === null || property_exists( $oJsonEvents, "error" ) ) {
            return false;
        }

        // do incidents
        $arrJsonGameDetails = $oJsonEvents->incidents;
        uasort( $arrJsonGameDetails, function ( $oJsonDetailA, $oJsonDetailB ) {
            return $oJsonDetailA->time < $oJsonDetailB->time ? -1 : 1;
        });

        foreach( $arrJsonGameDetails as $oJsonGameDetail ) {
            $oEvent = null;
            if( $oJsonGameDetail->incidentType === "card" ) {
                $oEvent = $this->createGameCardEvent( $oGame, $oJsonGameDetail );
            } else if( $oJsonGameDetail->incidentType === "goal" or $oJsonGameDetail->incidentType === "penalty" ) {
                $oEvent = $this->createGameGoalEvent( $oGame, $oJsonGameDetail );
            } else if( $oJsonGameDetail->incidentType === "substitution" ) {
                $oEvent = $this->createGameSubstitutionEvent( $oGame, $oJsonGameDetail );
            }
            if( $oEvent !== null ) {
                $oGame->convertEvent( $oEvent );
            }
        }

        return true;
    }


    public function getUrlForGame( string $sCompetitionId, string $sSeasonId, int $nGameId ): string
	{
        return self::APIURL . "event/" . $nGameId;
	}



    protected function getUrlForGameParticipations( int $nGameId ): string
    {
        return self::APIURL . "event/" . $nGameId ."/lineups";
    }

    protected function getUrlForGameEvents( int $nGameId ): string
    {
        return self::APIURL . "event/" . $nGameId ."/incidents";
    }

	public function getCacheTimeForGame(): int
	{
		return 60 * 55;
	}

    protected function createGameSubstitutionEvent( Voetbal_Extern_GameExt $oGame, stdClass $oJsonGameDetail ): Voetbal_Extern_Game_Event_Substitution {

        $oPersonOut = $this->createPerson( $oJsonGameDetail->playerOut );
	    $oParticipationOut = $oGame->getParticipation( $oPersonOut );
        if( $oParticipationOut === null ) {
            throw new \Exception( $oPersonOut->getName() . "(".$oPersonOut->getId().") kon niet worden gevonden als spelers", E_ERROR );
        }

        $oPersonIn = $this->createPerson( $oJsonGameDetail->playerIn );
        $oParticipationIn = $oGame->getParticipation( $oPersonIn );
        if( $oParticipationIn === null ) {
            throw new \Exception( $oPersonIn->getName() . "(".$oPersonIn->getId().") kon niet worden gevonden als spelers", E_ERROR );
        }
        return new Voetbal_Extern_Game_Event_Substitution( $oParticipationOut, $oJsonGameDetail->time, $oParticipationIn );
    }

    protected function createGameGoalEvent( Voetbal_Extern_GameExt $oGame, stdClass $oJsonGameDetail ): ?Voetbal_Extern_Game_Event_Goal {
	    if ( $oJsonGameDetail->incidentType === "penalty" && $oJsonGameDetail->incidentClass === "missedpenalty" ) {
            return null;
        }
	    $oPerson = $this->createPerson( $oJsonGameDetail->player );
        $oParticipation = $oGame->getParticipation( $oPerson );
        if( $oParticipation === null ) {
            throw new \Exception( $oPerson->getName() . "(".$oPerson->getId().") kon niet worden gevonden als spelers", E_ERROR );
        }
        $nHome = $oJsonGameDetail->homeScore;
        $nAway = $oJsonGameDetail->awayScore;

        $oGoalEvent = new Voetbal_Extern_Game_Event_Goal( $oParticipation, $oJsonGameDetail->time, $nHome, $nAway );

        $incidentType = strtolower( $oJsonGameDetail->incidentType );
        $incidentClass = strtolower( $oJsonGameDetail->incidentClass );
        if( $incidentType === "goal" ) {
            if( $incidentClass === "regulargoal" ) {

            } else if( $incidentClass === "owngoal" ) {
                $oGoalEvent->putOwn( true );
            } else if( $incidentClass === "penalty" ) {
                $oGoalEvent->putPenalty( true );
            }
            if( property_exists( $oJsonGameDetail, "assist1") ) {
                $oPersonAssist = $this->createPerson( $oJsonGameDetail->assist1 );
                $oAssist = $oGame->getParticipation( $oPersonAssist );
                if( $oAssist === null ) {
                    throw new \Exception( $oPersonAssist->getName() . "(".$oPersonAssist->getId().") kon niet worden gevonden als spelers", E_ERROR );
                }
                $oGoalEvent->putAssist($oAssist);
            }
        } else if ( $incidentType === "penalty" ) {
            if( $incidentClass === "penalty" ) {
                $oGoalEvent->putPenalty( true );
            }
        }
        return $oGoalEvent;
    }

    protected function createGameCardEvent( Voetbal_Extern_GameExt $oGame, stdClass $oJsonGameDetail ): ?Voetbal_Extern_Game_Event_Card {
        if( !property_exists( $oJsonGameDetail, "player" ) &&
            property_exists( $oJsonGameDetail, "manager" ) ) {
            return null;
        }
	    $oPerson = $this->createPerson( $oJsonGameDetail->player );
        $oParticipation = $oGame->getParticipation( $oPerson );
        if( $oParticipation === null ) {
            if( $oPerson->getId() === 'aaron-meijers/32386') { // kaart vanaf de bank
                return null;
            }
            throw new \Exception( $oPerson->getName() . "(".$oPerson->getId().") kon niet worden gevonden als spelers", E_ERROR );
        }
        $nCard = null;
        if( $oJsonGameDetail->incidentClass === "yellow" ) {
            $nCard = Voetbal_Game::DETAIL_YELLOWCARDONE;
        } else if( $oJsonGameDetail->incidentClass === "yellowRed" ) {
            $nCard = Voetbal_Game::DETAIL_YELLOWCARDTWO;
        } else if( $oJsonGameDetail->incidentClass === "red" ) {
            $nCard = Voetbal_Game::DETAIL_REDCARD;
        } else {
            throw new \Exception( "kon het kaarttype \"".$oJsonGameDetail->incidentClass."\" niet vaststellen", E_ERROR );
        }
        return new Voetbal_Extern_Game_Event_Card( $oParticipation, $oJsonGameDetail->time, $nCard );
    }

    protected function getLine( $nLineRaw ): ?int {
	    if( $nLineRaw === 1 ) {
            return Voetbal_Team_Line::KEEPER;
        } elseif( $nLineRaw === 2 ) {
            return Voetbal_Team_Line::DEFENSE;
        } elseif( $nLineRaw === 3 ) {
            return Voetbal_Team_Line::MIDFIELD;
        } elseif( $nLineRaw === 4 ) {
            return Voetbal_Team_Line::ATTACK;
        }
	    return null;
    }

    protected function getLineFromPosition( string $sPosition ): ?int {
        if( $sPosition === "G" ) {
            return Voetbal_Team_Line::KEEPER;
        } elseif( $sPosition === "D" ) {
            return Voetbal_Team_Line::DEFENSE;
        } elseif( $sPosition === "M" ) {
            return Voetbal_Team_Line::MIDFIELD;
        } elseif( $sPosition === "F" ) {
            return Voetbal_Team_Line::ATTACK;
        }
        return null;
    }

    protected function createTeam( string $sId, string $sName ): Voetbal_Extern_Team {
        $oTeam = new Voetbal_Extern_Team( $sId );
        $oTeam->putName( trim( $sName ) );
        return $oTeam;
    }

    protected function createPerson( stdClass $oPlayer ): Voetbal_Extern_Person {
        $oPerson = new Voetbal_Extern_Person( $oPlayer->slug . "/" . $oPlayer->id  );
        $oPerson->putName( trim( $oPlayer->name ) );
        return $oPerson;
    }

    protected function getUrlPostfix()
    {
        // current 37 23873
        return "?_=" . Agenda_Factory::createDateTime()->getTimestamp();
    }
}

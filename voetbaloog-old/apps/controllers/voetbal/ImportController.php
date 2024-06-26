<?php
/**
 * Created by PhpStorm.
 * User: cdunnink
 * Date: 7/4/15
 * Time: 9:57 PM
 */

class Voetbal_ImportController extends Zend_Controller_Action
{
    public function init()
    {
        $cfgApp = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini');
        $this->view->bHasImport = ( $cfgApp->get( "import" ) !== null );
        if ( $this->view->bHasImport === true )
            $this->view->importModuleName = $cfgApp->get( "import" )->name;
    }

    private function getExternalLib(): Voetbal_Extern_System_Interface
    {
        $cfgApp = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini');
        $bHasImport = ( $cfgApp->get( "import" ) !== null );
        if ( $bHasImport === false )
            throw new Exception("impport module is niet geconfigureerd", E_ERROR);

        return Voetbal_Factory::getExternalLib( $cfgApp->get( "import" )->name );
    }

    public function indexAction()
    {

    }

    public function teamsAction()
    {
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "Voetbal_CompetitionSeason::Public", "EqualTo", false );
        $oOptions->addFilter( "Voetbal_CompetitionSeason::ExternId", "NotEqualTo", null );
        $oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
        $this->view->oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjectsFromDatabase( $oOptions );

        $nCSId = (int) $this->getParam("csid");
        if ( $nCSId > 0 )
            $this->view->oCompetitionSeason = $this->view->oCompetitionSeasons[ $nCSId ];

        if ( $this->view->oCompetitionSeason === null ){ return; }

        list ( $this->view->externalurl, $this->view->externalurlcache, $vtRetVal ) = $this->getJsonTeams( $this->view->oCompetitionSeason );
        if ( is_string( $vtRetVal ) ) {
            $this->view->errormessage = $vtRetVal;
            return;
        }

        $this->view->arrTeamsToImport = $vtRetVal;
        $arrPropertiesToConvert = array(
            "externid" => function ($oObject, $vtPropValue) {
                $oObject->id = $vtPropValue;
            }
        );
        Import_Factory::convertProperties( $this->view->arrTeamsToImport, $arrPropertiesToConvert);

        if ( strlen( $this->getParam("btnimport") ) > 0 )
            $this->handleImportTeams( $this->view->oCompetitionSeason, $this->view->arrTeamsToImport );
    }

    protected function getJsonTeams( $oCompetitionSeason )
    {
        $sCSId = Import_Factory::getIdFromExternId( $oCompetitionSeason->getExternId()  );
        $sCompetitionId = Import_Factory::getIdFromExternId( $oCompetitionSeason->getCompetition()->getExternId()  );
        $sSeasonId = Import_Factory::getIdFromExternId( $oCompetitionSeason->getSeason()->getExternId()  );

        $arrJSON = null; $sUrl = null;
        try {
            $oExternalLib = $this->getExternalLib();

            $sUrl = $oExternalLib->getUrlForTeams( $sCSId, $sCompetitionId, $sSeasonId );
            $sUrlCache = $oExternalLib->getCacheTimeForTeams();
            $arrJSON = $oExternalLib->getTeams( $sCSId, $sCompetitionId, $sSeasonId );
            $arrPropertiesToConvert = array( "id" => function( $oObject, $vtPropValue) { $oObject->externid = $vtPropValue; } );
            Import_Factory::convertProperties( $arrJSON, $arrPropertiesToConvert );
        }
        catch( Exception $e ){ return $e->getMessage(); }

        return array( $sUrl, $sUrlCache, $arrJSON );
    }

    protected function handleImportTeams( $oCompetitionSeason, $arrTeamsToImport )
    {
        $oAssociation = $oCompetitionSeason->getAssociation();

        $oTeams = Voetbal_Team_Factory::createObjects();
        $oDbWriter = Voetbal_Team_Factory::createDbWriter();
        $oTeams->addObserver( $oDbWriter );

        $arrTeamsToAssignToPlaces = array();

        foreach ( $arrTeamsToImport as $oTeamToImport )
        {
            $oOptions = Construction_Factory::createOptions();
            $oOptions->addFilter("Voetbal_Team::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix . $oTeamToImport->id );
            $oTeam = Voetbal_Team_Factory::createObjectFromDatabase( $oOptions );
            if ( $oTeam === null )
            {
                $oTeam = Voetbal_Team_Factory::createObject();
                $oTeam->putId( "__NEW__" . $oTeamToImport->id );
                $oTeam->putName( $oTeamToImport->name );
                $oTeam->putImageName( substr( str_replace( " ", "", strtolower( $oTeamToImport->name ) ), 0, 4 ) );
                $oTeam->putAssociation( $oAssociation );
                $oTeam->putExternId( Import_Factory::$m_szExternPrefix . $oTeamToImport->id );
                $oTeams->add( $oTeam );
            }
            else
            {
                $oTeam->addObserver( $oDbWriter );
                $oTeam->putName( $oTeamToImport->name );
                $oTeam->putImageName( substr( str_replace( " ", "", strtolower( $oTeamToImport->name ) ), 0, 4 ) );
            }
            $arrTeamsToAssignToPlaces[] = $oTeam;
        }

        try {
            $oDbWriter->write();

            $this->view->successmessage = "de teams zijn geimporteerd";

            if ( $this->getParam("assignteamstopouleplaces") === "on")
            {
                if ( $oCompetitionSeason->getRounds()->count() === 0 ) {
                    throw new Exception( "de eerste ronde kon niet gevonden worden", E_ERROR );
                }

                $oPlaces = $oCompetitionSeason->getDefaultPoule()->getPlaces();
                if ( $oPlaces->count() != count( $arrTeamsToAssignToPlaces ) ) {
                    throw new Exception( "het aantal pouleplekken van de eerste ronde komt niet overeen met het aantal teams", E_ERROR );
                }

                $oPoulePlaceDbWriter = Voetbal_PoulePlace_Factory::createDbWriter();
                foreach( $oPlaces as $oPlace ) {
                    $oPlace->addObserver( $oPoulePlaceDbWriter );
                    $oPlace->putTeam( array_shift( $arrTeamsToAssignToPlaces ) );
                }
                $oPoulePlaceDbWriter->write();
                $this->view->successmessage .= "<br>de teams zijn toegekend aan de pouleplekken van de eerste ronde";
            }

        }
        catch( Exception $e )
        {
            $this->view->errormessage = $e->getMessage();
        }
    }

    public function wedstrijdenAction()
    {
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter("Voetbal_CompetitionSeason::ExternId", "NotEqualTo", null);
        $oOptions->addOrder("Voetbal_Season::StartDateTime", true);
        $this->view->oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjectsFromDatabaseWithTeams($oOptions);

        $nCSId = (int)$this->getParam("csid");
        if ($nCSId > 0)
            $this->view->oCompetitionSeason = $this->view->oCompetitionSeasons[$nCSId];

        if ($this->view->oCompetitionSeason === null) {
            return;
        }

        $oExternalLib = $this->getExternalLib();
        $sCompetitionId = Import_Factory::getIdFromExternId( $this->view->oCompetitionSeason->getCompetition()->getExternId() );
        $sSeasonId = Import_Factory::getIdFromExternId( $this->view->oCompetitionSeason->getSeason()->getExternId() );
        $this->view->externalurl = $oExternalLib->getUrlForGames( $sCompetitionId, $sSeasonId );
        $this->view->externalurlcache = $oExternalLib->getCacheTimeForGames();
        $this->view->arrGamesToImport = $this->getJsonGames( $this->view->oCompetitionSeason);

        if ( strlen( $this->getParam("btnimport") ) > 0 )
            $this->handleImportGames( $this->view->oCompetitionSeason, $this->view->arrGamesToImport );
    }

    protected function getJsonGames( $oCompetitionSeason )
    {
        $sCompetitionId = Import_Factory::getIdFromExternId( $this->view->oCompetitionSeason->getCompetition()->getExternId() );
        $sSeasonId = Import_Factory::getIdFromExternId( $this->view->oCompetitionSeason->getSeason()->getExternId() );

        $arrGames = null;
        try {
            $oExternalLib = $this->getExternalLib();
            throw new \Exception("onbekend gameroundnumber", E_ERROR);
            $arrGames = $oExternalLib->getGames( $sCompetitionId, $sSeasonId );
        }
        catch( Exception $e ){ $vtJSON = $e->getMessage(); }

        return $arrGames;
    }

    protected function handleImportGames( $oCompetitionSeason, $arrGamesToImport )
    {
        $oGames = Voetbal_Game_Factory::createObjects();
        $oDbWriter = Voetbal_Game_Factory::createDbWriter();
        $oGames->addObserver( $oDbWriter );

        foreach ( $arrGamesToImport as $oGameToImport )
        {
            $oOptions = Construction_Factory::createOptions();
            $oOptions->addFilter("Voetbal_Game::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix . $oGameToImport->externid );
            $oGame = Voetbal_Game_Factory::createObjectFromDatabase( $oOptions );

            $oDateTime = Agenda_Factory::createDateTime( $oGameToImport->startdatetime );
            $oHomePP = Voetbal_PoulePlace_Factory::createObjectByExternTeamId( $oCompetitionSeason, $oGameToImport->hometeamexternid );
            $oAwayPP = Voetbal_PoulePlace_Factory::createObjectByExternTeamId( $oCompetitionSeason, $oGameToImport->awayteamexternid );
            if ( $oGame === null )
            {
                if ( $oGameToImport->externid == "273643" or $oGameToImport->externid == "273709" ){
                    continue;
                }
                $sExternId = Import_Factory::$m_szExternPrefix . $oGameToImport->externid;
                $oGame = Voetbal_Game_Factory::createObjectExt( $oDateTime, $oHomePP, $oAwayPP, $sExternId, $oGameToImport->number );

                $oGames->add( $oGame );
            }
            else
            {
                $oGame->addObserver( $oDbWriter );
                if ( $oDateTime != $oGame->getStartDateTime() ) {
                    $oGame->putStartDateTime( $oDateTime );
                }
                if ( $oHomePP != $oGame->getHomePoulePlace() ) {
                    $oGame->putHomePoulePlace( $oHomePP );
                }
                if ( $oAwayPP != $oGame->getAwayPoulePlace() ) {
                    $oGame->putAwayPoulePlace( $oAwayPP );
                }
            }
        }

        try {
            // create game
            $oDbWriter->write();
            $oDbWriter->getObjectChanges()->flush();
            $this->view->successmessage = "de wedstrijden zijn geimporteerd";
            if ( $this->handleGameNumbers( $oCompetitionSeason, $oDbWriter ) === true )
                $this->view->successmessage .= " ( nummers zijn bijgewerkt )";
        }
        catch( Exception $e )
        {
            $this->view->errormessage = $e->getMessage();
        }
    }

    protected function handleGameNumbers( $oCompetitionSeason, $oGameDbWriter )
    {
        $oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter("Voetbal_Game::Number", "GreaterThan", 0 );
		$oOptions->addFilter("Voetbal_Round::Number", "EqualTo", 0 );
		$oOptions->addFilter("Voetbal_Round::CompetitionSeason", "EqualTo", $oCompetitionSeason );
		$nNrOfNumberedGames = Voetbal_Game_Factory::getNrOfObjectsFromDatabase( $oOptions );
        if ( $nNrOfNumberedGames > 0 )
            return false;

        $oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter("Voetbal_Round::Number", "EqualTo", 0 );
		$oOptions->addFilter("Voetbal_Round::CompetitionSeason", "EqualTo", $oCompetitionSeason );
		$oGames = Voetbal_Game_Factory::createObjectsFromDatabase( $oOptions );
        foreach ( $oGames as $oGame )
        {
            $oGame->addObserver( $oGameDbWriter );

            $nNumberHomePP = $this->getNrOfPreviousGames( $oCompetitionSeason, $oGame->getStartDateTime(), $oGame->getHomePoulePlace() );
            $nNumberAwayPP = $this->getNrOfPreviousGames( $oCompetitionSeason, $oGame->getStartDateTime(), $oGame->getAwayPoulePlace() );

            if ( $nNumberHomePP != $nNumberAwayPP ) {
                throw new Exception( "het aantal voorgaande wedstrijden van thuis- en uitteam is ongelijk", E_ERROR );
            }
            $oGame->putNumber( $nNumberHomePP + 1 );
        }

        $oGameDbWriter->write();
        return true;
    }

    protected function getNrOfPreviousGames( $oCompetitionSeason, $oDateTime, $oPoulePlace )
    {
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter("Voetbal_Game::StartDateTime", "SmallerThan", $oDateTime );
        $oOptionsOr = Construction_Factory::createOptions();
        {
            $oOptionsHome = Construction_Factory::createOptions();
            {
                $oOptionsHome->putId("__HOME__");
                $oOptionsHome->addFilter( "Voetbal_Game::HomePoulePlace", "EqualTo", $oPoulePlace );
                $oOptionsOr->add( $oOptionsHome );
            }
            $oOptionsAway = Construction_Factory::createOptions();
            {
                $oOptionsAway->putId("__AWAY__");
                $oOptionsAway->addFilter( "Voetbal_Game::AwayPoulePlace", "EqualTo", $oPoulePlace );
                $oOptionsOr->add( $oOptionsAway );
            }
        }
        $oOptions->add( $oOptionsOr );
        $oOptions->addFilter("Voetbal_Round::Number", "EqualTo", 0 );
        $oOptions->addFilter("Voetbal_Round::CompetitionSeason", "EqualTo", $oCompetitionSeason );
        return Voetbal_Game_Factory::getNrOfObjectsFromDatabase( $oOptions );
    }

    public function wedstrijdAction()
    {
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter("Voetbal_CompetitionSeason::ExternId", "NotEqualTo", null);
        $oOptions->addOrder("Voetbal_Season::StartDateTime", true);
        $this->view->oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjectsFromDatabaseCustom( null, null, $oOptions);

        $nCSId = (int)$this->getParam("csid");
        if ($nCSId > 0)
            $this->view->oCompetitionSeason = $this->view->oCompetitionSeasons[$nCSId];

        if ($this->view->oCompetitionSeason === null) {
            return;
        }

        $nGameId = (int) $this->getParam('gameid');
        if ( $nGameId > 0 )
        {
            $oOptions = Construction_Factory::createOptions();
            $oOptions->addFilter( "Voetbal_Game::Id", "EqualTo", $nGameId );
            $oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this->view->oCompetitionSeason );
            $this->view->oGame = Voetbal_Game_Factory::createObjectFromDatabase( $oOptions );
        }

        if ( $this->view->oGame === null ) // different competitionseason is chosen
        {
            $oOptions = Construction_Factory::createOptions();
            $oOptions->addFilter( "Voetbal_Game::State", "EqualTo", Voetbal_Factory::STATE_SCHEDULED );
            $oOptions->addLimit( 1 );
            $oGames = $this->view->oCompetitionSeason->getGames( true, $oOptions );
            if ( $oGames->count() > 0 )
                $this->view->oGame = $oGames->first();
        }

        // toon lijst van wedstrijd die geimporteerd kunnen worden, selecteer standaard eerst volgende wedstrijd na nu!!!

        if ($this->view->oGame === null) {
            return;
        }

        $oExternalLib = $this->getExternalLib();
        $sCompetitionId = Import_Factory::getIdFromExternId( $this->view->oCompetitionSeason->getCompetition()->getExternId() );
        $sSeasonId = Import_Factory::getIdFromExternId( $this->view->oCompetitionSeason->getSeason()->getExternId() );
        $sGameId = Import_Factory::getIdFromExternId( $this->view->oGame->getExternId() );
        $this->view->externalurl = $oExternalLib->getUrlForGame( $sCompetitionId, $sSeasonId, $sGameId );
        $this->view->externalurlcache = $oExternalLib->getCacheTimeForGame();
        $this->view->oGameToImport = $this->getJsonGame( $this->view->oGame, $this->view->oCompetitionSeason,  );

        if ( strlen( $this->getParam("btnimport") ) > 0 )
            $this->handleImportGame( $this->view->oGame, $this->view->oGameToImport );
    }

    protected function getJsonGame( $oGame, $oCompetitionSeason ): Voetbal_Extern_GameExt
    {
        $sCompetitionId = Import_Factory::getIdFromExternId( $oCompetitionSeason->getCompetition()->getExternId() );
        $sSeasonId = Import_Factory::getIdFromExternId( $oCompetitionSeason->getSeason()->getExternId() );
        $sGameId = Import_Factory::getIdFromExternId( $oGame->getExternId() );

        $oExternGame = null;
        try {
            $oExternalLib = $this->getExternalLib();
            $oExternGame = $oExternalLib->getGame( $sCompetitionId, $sSeasonId, $sGameId );
        }
        catch( Exception $e ){ $vtJSON = $e->getMessage(); }

        return $oExternGame;
    }

    protected function handleImportGame( Voetbal_Game $oGame, Voetbal_Extern_GameExt $oGameToImport )
    {
        $handlerMiddleware = Voetbal_Command_Main_Factory::getMiddleWare();
        $transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );
        $commandBus = new \League\Tactician\CommandBus([$transactionMiddleware,$handlerMiddleware]);

        try
        {
            $oGoals = new stdClass();
            $oGoals->homegoals = $oGameToImport->getHomeGoals();
            $oGoals->awaygoals = $oGameToImport->getAwayGoals();
            // command update Game
            $updateGameCommand = new Voetbal_Command_UpdateGame( $oGame, $oGoals, Voetbal_Factory::STATE_PLAYED );
            $homeParticipants = $oGameToImport->getParticipations(Voetbal_Game::HOME);
            $updateGameCommand->putHomeDetails( $homeParticipants, $oGameToImport->getEvents( Voetbal_Game::HOME ) );
            $awayParticipants = $oGameToImport->getParticipations(Voetbal_Game::AWAY);
            $updateGameCommand->putAwayDetails( $awayParticipants, $oGameToImport->getEvents( Voetbal_Game::AWAY ) );
            $updateGameCommand->putBus( $commandBus );
            $commandBus->handle( $updateGameCommand );

            $this->view->successmessage = "de wedstrijd is geimporteerd";
        }
        catch( Exception $e ){ $this->view->errormessage = $e->getMessage(); }
    }
}

?>
<?php


class Voetbal_CompetitieseizoenController extends Zend_Controller_Action
{
	public function init()
	{
		$cfgApp = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini');
		$this->view->bHasImport = ($cfgApp->get("import") !== null);
	}

	public function indexAction()
	{
		$this->view->angular = true;

		$this->view->oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase((int)$this->getParam("id"));
		if ( $this->view->oCompetitionSeason === null)
			return;

		$this->view->subject = $this->getParam("subject");
		if ($this->view->subject === null)
			$this->view->subject = "structure";
		else if ($this->view->subject === "teams"){
			$this->view->oRound = $this->view->oCompetitionSeason->getRounds()->first();
		}

		$this->view->extraurlparams = "id/" . $this->view->oCompetitionSeason->getId() . "/";
	}

	/*
	public function indexAction()
	{
		$nId = (int) $this->getParam('competitionseasonid');
		if ( $nId === 0 )
			return;

        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "Voetbal_CompetitionSeason::Id", "EqualTo", $nId );
        $this->view->oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( $oOptions );

        $this->initWizardActions();
		if ( $this->view->arrWizardSteps["games"]["accessible"] === true )
			$this->initGameWizardActions();

		// $sJsIncludes = "js.push( \"//code.jquery.com/ui/1.10.4/jquery-ui.min.js\" );";
		$sJsIncludes = $this->_helper->GetDateIncludes("js");
		$this->getResponse()->insert("extrajsincludes", $sJsIncludes );
		// $sCssIncludes = "css.push( \"//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.min.css\" );";
		$sCssIncludes = $this->_helper->GetDateIncludes("css");
		$this->getResponse()->insert("extracssincludes", $sCssIncludes );

		$this->setBetPoints();

		// $this->view->betpointsreadonly = ( $this->view->nNrOfPools > 0 );
		// $this->view->bAllowRemoveGames = ( $this->view->nNrOfPools === 0 );

		if ( strlen( $this->getParam("btnupdatesettings") ) > 0 )
			$this->handleSettings( $this->view->oCompetitionSeason );
		elseif ( strlen( $this->getParam("btnupdatestructure") ) > 0 )
			$this->handleStructureUpdate();
		elseif ( strlen( $this->getParam("btnattachteamstostructure") ) > 0 )
			$this->handleAttachTeamsToStructure();
		e
		elseif ( strlen( $this->getParam("btnremovegames") ) > 0 )
			$this->handleGamesRemove();
		elseif ( strlen( $this->getParam("btnpublish") ) > 0 )
			$this->handlePublish( $this->view->oCompetitionSeason );


//		elseif ( strlen( $this->getParam("btnimportteams") ) > 0 )
//			$this->handleImportTeams();
//		elseif ( strlen( $this->getParam("btnexportplayers") ) > 0 )
//			$this->handleExportPlayers();
//		elseif ( strlen( $this->getParam("btnimportplayers") ) > 0 )
//			$this->handleImportPlayers();
//		elseif ( strlen( $this->getParam("btnexportgames") ) > 0 )
//			$this->handleExportScheduledGames();
//		elseif ( strlen( $this->getParam("btnimportgames") ) > 0 )
//			$this->handleImportScheduledGames();
//
//
//		elseif ( strlen( $this->getParam("btnsavebetpoints") ) > 0 )
//			$this->handleBetPoints();
	}

    protected function initWizardActions()
    {
        $this->view->arrWizardSteps = array(
            "settings" => array( "name" => "basis", "accessible" => true ),
            "structure" => array( "name" => "structuur", "accessible" => true ),
            "teams" => array( "name" => "teams", "accessible" => false ),
            "games" => array( "name" => "wedstrijden", "accessible" => false ),
            "publish" => array( "name" => "publiceren", "accessible" => false )
        );

        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this->view->oCompetitionSeason );
        $oOptions->addFilter( "Voetbal_Round::Number", "EqualTo", 0 );
        $oPoulePlaces = Voetbal_PoulePlace_Factory::createObjectsFromDatabase( $oOptions );

        $oTeams = Voetbal_Team_Factory::createObjects();
        foreach( $oPoulePlaces as $oPoulePlace )
            $oTeams->add( $oPoulePlace->getTeam() );

        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this->view->oCompetitionSeason );
        $nNrOfGames = Voetbal_Game_Factory::getNrOfObjectsFromDatabase( $oOptions );

        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this->view->oCompetitionSeason );
        $oOptions->addFilter( "Voetbal_Game::StartDateTime", "EqualTo", null );
        $nNrOfUnscheduledGames = Voetbal_Game_Factory::getNrOfObjectsFromDatabase( $oOptions );

        $this->view->wizardaction = $this->getParam('wizardaction');
        if ( $this->view->wizardaction === null ){
            $this->view->wizardaction =  $this->getActiveAction( $oPoulePlaces, $oTeams, $nNrOfGames, $nNrOfUnscheduledGames );
        }

        if ( $oPoulePlaces->count() < 2 )
            return;
        $this->view->arrWizardSteps["teams"]["accessible"] = true;

        if ( $oTeams->count() < $oPoulePlaces->count() )
            return;
        $this->view->arrWizardSteps["games"]["accessible"] = true;

        if ( $nNrOfGames === 0 or $nNrOfUnscheduledGames > 0 )
            return;

        $this->view->arrWizardSteps["publish"]["accessible"] = true;
    }

    protected function getActiveAction( $oPoulePlaces, $oTeams, $nNrOfGames, $nNrOfUnscheduledGames )
    {
       if ( $oPoulePlaces->count() < 2 ) {
            return "settings";
        }

        $oTeams = Voetbal_Team_Factory::createObjects();
        foreach( $oPoulePlaces as $oPoulePlace )
            $oTeams->add( $oPoulePlace->getTeam() );

        if ( $oTeams->count() === 0 ) {
            return "structure";
        }

        if ( $oTeams->count() < $oPoulePlaces->count() )
            return "teams";

       if ( $nNrOfGames === 0 or $nNrOfUnscheduledGames > 0 )
            return "games";

        return "publish";
    }

	protected function initGameWizardActions()
	{
		$this->view->arrGameWizardSteps = array(
			"generate" => array( "name" => "genereren", "accessible" => false ),
			"plan" => array( "name" => "inplannen", "accessible" => false ),
			"process" => array( "name" => "verwerken", "accessible" => false )
		);

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this->view->oCompetitionSeason );
		$nNrOfGames = Voetbal_Game_Factory::getNrOfObjectsFromDatabase( $oOptions );

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this->view->oCompetitionSeason );
		$oOptions->addFilter( "Voetbal_Game::StartDateTime", "EqualTo", null );
		$nNrOfUnscheduledGames = Voetbal_Game_Factory::getNrOfObjectsFromDatabase( $oOptions );

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $this->view->oCompetitionSeason );
		$oOptions->addFilter( "Voetbal_Game::State", "EqualTo", Voetbal_Factory::STATE_PLAYED );
		$nNrOfProcessedGames = Voetbal_Game_Factory::getNrOfObjectsFromDatabase( $oOptions );

		$this->view->gamewizardaction = $this->getParam('gamewizardaction');
		var_dump($this->view->gamewizardaction);
		if ( $this->view->gamewizardaction === null ){
			$this->view->gamewizardaction =  $this->getActiveGameAction( $nNrOfGames, $nNrOfUnscheduledGames );
		}

		if ( $nNrOfProcessedGames === 0 ) {
			$this->view->arrGameWizardSteps["generate"]["accessible"] = true;
		}
		if ( $nNrOfGames > 0 ) {
			$this->view->arrGameWizardSteps["plan"]["accessible"] = true;
		}
		if ( $nNrOfGames > 0 and $nNrOfUnscheduledGames === 0 ) {
			$this->view->arrGameWizardSteps["process"]["accessible"] = true;
		}
	}

	protected function getActiveGameAction( $nNrOfGames, $nNrOfUnscheduledGames )
	{
		if ( $nNrOfGames === 0 ) {
			return "generate";
		}
		if ( $nNrOfUnscheduledGames < $nNrOfGames ) {
			return "plan";
		}
		return "process";
	}

	protected function handleExportTeams()
	{
		$this->view->btnteamexport = true;
		try {
			$this->view->m_arrExportedTeams = Voetbal_Extern_FCUpdate::getExportedTeams();
		}
		catch( Exception $e )
		{
			$this->view->teamsexporterrormessage = $e->getMessage();
		}
	}

	protected function handleImportTeams()
	{
		$this->handleExportTeams();

		$oPoulePlaceDbWriter = Voetbal_PoulePlace_Factory::createDbWriter();

		$oTeams = Voetbal_Team_Factory::createObjects();
		$oDbWriter = Voetbal_Team_Factory::createDbWriter();
		$oTeams->addObserver( $oDbWriter );

		$oFirstPoule = $this->view->oCompetitionSeason->getDefaultPoule();

		$arrParams = $this->getAllParams();
		$nPoulePlaceNr = 0;
		foreach ( $arrParams as $sId => $sValue )
		{
			if ( strpos( $sId, "teamname-" ) === false )
				continue;
			$nTeamNr = (int) substr( $sId, strlen( "teamname-" ) );
			$sTeamName = $this->getParam( "teamname-" . $nTeamNr );
			$nExternId = (int) $this->getParam( "teamexternid-" . $nTeamNr );

			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter("Voetbal_Team::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix . $nExternId );
			$oTeam = Voetbal_Team_Factory::createObjectFromDatabase( $oOptions );
			if ( $oTeam === null )
			{
				$oTeam = Voetbal_Team_Factory::createObject();
				$oTeam->putId( "__NEW__" . $nExternId );
				$oTeam->putName( $sTeamName );
				$oTeam->putImageName( substr( str_replace( " ", "", strtolower( $sTeamName ) ), 0, 4 ) );
				$oTeam->putAssociation( $this->view->oCompetitionSeason->getAssociation() );
				$oTeam->putExternId( Import_Factory::$m_szExternPrefix . $nExternId );
				$oTeams->add( $oTeam );
			}
			else
			{
				$oTeam->addObserver( $oDbWriter );
				$oTeam->putName( $sTeamName );
				$oTeam->putImageName( substr( str_replace( " ", "", strtolower( $sTeamName ) ), 0, 4 ) );
			}

			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter("Voetbal_PoulePlace::Poule", "EqualTo", $oFirstPoule );
			$oOptions->addFilter("Voetbal_PoulePlace::Number", "EqualTo", $nPoulePlaceNr++ );
			$oPoulePlace = Voetbal_PoulePlace_Factory::createObjectFromDatabase( $oOptions );

			$oPoulePlace->addObserver( $oPoulePlaceDbWriter );
			$oPoulePlace->putTeam( $oTeam );
		}

		try {
			$oDbWriter->write();
			$oPoulePlaceDbWriter->write();
			$this->view->teamsimportsavemessage = "de teams zijn geimporteerd en aan het competitieseizoen toegevoegd";

			$oCache = ZendExt_Cache::getDefaultCache();
			$oCache->clean( Zend_Cache::CLEANING_MODE_MATCHING_TAG,	array( 'competitionseason'.$this->view->oCompetitionSeason->getId() ) );
		}
		catch( Exception $e )
		{
			$this->view->teamsimporterrormessage = $e->getMessage();
		}
	}

	protected function handleExportPlayers()
	{
		$this->view->btnplayerexport = true;
		try {
			$this->view->m_arrExportedPlayers = Voetbal_Extern_FCUpdate::getExportedPlayers( $this->view->oCompetitionSeason->getTeams() );
		}
		catch( Exception $e )
		{
			$this->view->playersexporterrormessage = $e->getMessage();
		}
	}

	protected function handleImportPlayers()
	{
		$this->handleExportPlayers();
		$oNow = Agenda_Factory::createDateTime();

		$oStaffMembershipDbWriter = Voetbal_Team_Membership_StaffMember_Factory::createDbWriter();
		$oPlayerMembershipDbWriter = Voetbal_Team_Membership_Player_Factory::createDbWriter();

		$oPersons = Voetbal_Person_Factory::createObjects();
		$oPersonDbWriter = Voetbal_Person_Factory::createDbWriter();
		$oPersons->addObserver( $oPersonDbWriter );

		$oPlayerMemberships = Voetbal_Team_Membership_Player_Factory::createObjects();
		$oPlayerMembershipDbWriter = Voetbal_Team_Membership_Player_Factory::createDbWriter();
		$oPlayerMemberships->addObserver( $oPlayerMembershipDbWriter );

		$oStaffMemberships = Voetbal_Team_Membership_StaffMember_Factory::createObjects();
		$oStaffMembershipDbWriter = Voetbal_Team_Membership_StaffMember_Factory::createDbWriter();
		$oStaffMemberships->addObserver( $oStaffMembershipDbWriter );

		$nPlayerNr = 0;
		$bFound = true;
		while ( $bFound === true )
		{
			$sJSONPlayer = $this->getParam( "player-" . $nPlayerNr++ );
			if ( strlen( $sJSONPlayer ) === 0 ){
				$bFound = false;
				continue;
			}

			$jsonPlayer = json_decode( $sJSONPlayer );

			if ( strlen( $jsonPlayer->stopdate ) > 0 )
			{
				$oMembership = null;
				if ( $jsonPlayer->type === "player" )
				{
					$oMembership = Voetbal_Team_Membership_Player_Factory::createObjectFromDatabase( (int) $jsonPlayer->id );
					$oMembership->addObserver( $oPlayerMembershipDbWriter );
				}
				else
				{
					$oMembership = Voetbal_Team_Membership_StaffMember_Factory::createObjectFromDatabase( (int) $jsonPlayer->id );
					$oMembership->addObserver( $oStaffMembershipDbWriter );
				}
				$oMembership->putEndDateTime( $jsonPlayer->stopdate );

				continue;
			}

			$oPlayerDate = Agenda_Factory::createDate( $jsonPlayer->startdate );

			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter("Voetbal_Person::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix . $jsonPlayer->externid );
			$oPerson = Voetbal_Person_Factory::createObjectFromDatabase( $oOptions );
			$bNewPerson = true;
			if ( $oPerson === null )
			{
				$oPerson = Voetbal_Person_Factory::createObject();
				$oPerson->putId( "__NEW__" . $jsonPlayer->externid );
				$oPerson->putFirstName( $jsonPlayer->firstname );
				$oPerson->putNameInsertions( $jsonPlayer->nameinsertions );
				$oPerson->putLastName( $jsonPlayer->lastname );
				$oPerson->putDateOfBirth( $jsonPlayer->dateofbirth );
				//$oTeam->putImageName( substr( str_replace( " ", "", strtolower( $sTeamName ) ), 0, 4 ) );
				//$oTeam->putAssociation( $this->view->oCompetitionSeason->getAssociation() );
				$oPerson->putExternId( Import_Factory::$m_szExternPrefix . $jsonPlayer->externid );
				$oPersons->add( $oPerson );
			}
			else
			{
				$bNewPerson = false;
				$oPerson->addObserver( $oPersonDbWriter );
				$oPerson->putFirstName( $jsonPlayer->firstname );
				$oPerson->putNameInsertions( $jsonPlayer->nameinsertions );
				$oPerson->putLastName( $jsonPlayer->lastname );
				$oPerson->putDateOfBirth( $jsonPlayer->dateofbirth );
				//$oTeam->putImageName( substr( str_replace( " ", "", strtolower( $sTeamName ) ), 0, 4 ) );
			}

			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter("Voetbal_Team::Name", "EqualTo", $jsonPlayer->team );
			$oTeam = Voetbal_Team_Factory::createObjectFromDatabase( $oOptions );

			if ( $jsonPlayer->function === Voetbal_Team_Membership_StaffMember::FUNCTION_TRAINER )
			{
				$oMembership = null;
				if ( $bNewPerson === false )
				{
					$oOptions = Construction_Factory::createFiltersForTimeSlots( "Voetbal_Team_Membership_StaffMember", $oNow, Agenda_TimeSlot::EXCLUDE_NONE, true );
					$oOptions->addFilter( "Voetbal_Team_Membership_StaffMember::Client", "EqualTo", $oPerson );
					$oOptions->addFilter( "Voetbal_Team_Membership_StaffMember::Provider", "EqualTo", $oTeam );
					$oMembership = Voetbal_Team_Membership_StaffMember_Factory::createObjectFromDatabase( $oOptions );
				}

				if ( $oMembership === null )
				{
					$oMembership = Voetbal_Team_Membership_StaffMember_Factory::createObject();
					$oMembership->putId( "__NEW__" . $oPerson->getId() );
					$oMembership->putClient( $oPerson );
					$oMembership->putProvider( $oTeam );
					$oMembership->putStartDateTime( $oPlayerDate );

					$oMembership->putFunctionX( Voetbal_Team_Membership_StaffMember::FUNCTION_TRAINER );
					$oMembership->putImportance( 1 );

					$oStaffMemberships->add( $oMembership );
				}
			}
			else
			{
				$oMembership = null;
				if ( $bNewPerson === false )
				{
					$oOptions = Construction_Factory::createFiltersForTimeSlots( "Voetbal_Team_Membership_Player", $oNow, Agenda_TimeSlot::EXCLUDE_NONE, true );
					$oOptions->addFilter( "Voetbal_Team_Membership_Player::Client", "EqualTo", $oPerson );
					$oOptions->addFilter( "Voetbal_Team_Membership_Player::Provider", "EqualTo", $oTeam );
					$oMembership = Voetbal_Team_Membership_Player_Factory::createObjectFromDatabase( $oOptions );
				}

				$nLine = 0;
				if ( $jsonPlayer->line === Voetbal_Team_Factory::getLineDescription( Voetbal_Team_Line::KEEPER ) )
					$nLine = Voetbal_Team_Line::KEEPER;
				else if ( $jsonPlayer->line === Voetbal_Team_Factory::getLineDescription( Voetbal_Team_Line::DEFENSE ) )
					$nLine = Voetbal_Team_Line::DEFENSE;
				else if ( $jsonPlayer->line === Voetbal_Team_Factory::getLineDescription( Voetbal_Team_Line::MIDFIELD ) )
					$nLine = Voetbal_Team_Line::MIDFIELD;
				else if ( $jsonPlayer->line === Voetbal_Team_Factory::getLineDescription( Voetbal_Team_Line::ATTACK ) )
					$nLine = Voetbal_Team_Line::ATTACK;

				if ( $oMembership !== null )
				{
					$oMembership->addObserver( $oPlayerMembershipDbWriter );
					$oMembership->putLine( $nLine );
					$oMembership->putBackNumber( (int)$jsonPlayer->backnumber );
				}
				else
				{
					$oMembership = Voetbal_Team_Membership_Player_Factory::createObject();
					$oMembership->putId( "__NEW__" . $oPerson->getId() );
					$oMembership->putClient( $oPerson );
					$oMembership->putProvider( $oTeam );
					$oMembership->putStartDateTime( $oPlayerDate );
					$oMembership->putLine( $nLine );
					$oMembership->putBackNumber( (int)$jsonPlayer->backnumber );

					$oPlayerMemberships->add( $oMembership );
				}
			}
		}

		try {
			$oPersonDbWriter->write();
			$oStaffMembershipDbWriter->write();
			$oPlayerMembershipDbWriter->write();
			$this->view->playersimportsavemessage = "de spelers zijn geimporteerd";

			$oCache = ZendExt_Cache::getDefaultCache();
			$oCache->clean( Zend_Cache::CLEANING_MODE_MATCHING_TAG,	array( 'competitionseason'.$this->view->oCompetitionSeason->getId() ) );
		}
		catch( Exception $e )
		{
			$this->view->playersimporterrormessage = $e->getMessage();
		}
	}

    protected function handleAttachTeamsToStructure()
    {
        $oDefaultPoule = $this->view->oCompetitionSeason->getDefaultPoule();
        if( $oDefaultPoule === null or $oDefaultPoule->getPlaces()->count() === 0
        ) {
            $this->view->errormessage = "de structuur kan niet gevonden worden";
            return;
        }

		// init pouleplaces
		$oDbWriter = Voetbal_PoulePlace_Factory::createDbWriter();
		$oPlaces = $oFirstRound->getPoulePlaces();
		foreach( $oPlaces as $oPlace )
		{
			$oPlace->addObserver( $oDbWriter );
			$oPlace->putTeam( null );
		}
		$oDbWriter->write();

		$oDbWriter->getObjectChanges()->flush();

        $oAddedTeams = Voetbal_Team_Factory::createObjects();
        foreach( $oPlaces as $oPlace )
        {
            $nTeamId = (int) $this->getParam("pouleplace-".$oPlace->getId());
            if ( $nTeamId > 0 and $oAddedTeams[ $nTeamId ] === null ) {
				$oTeam = Voetbal_Team_Factory::createObjectFromDatabase($nTeamId);
				$oPlace->putTeam( $oTeam );
				$oAddedTeams->add( $oTeam );
			}
        }

        try
        {
            $oDbWriter->write();
            $this->view->savemessage = "teams opgeslagen";
        }
        catch ( Exception $e )
        {
            $this->view->errormessage = "teams konden niet worden opgeslagen: " . $e->getMessage();
        }
    }

	public function ajaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		if ( $this->getParam('method') === "settings" )
		{
			$this->view->oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( (int) $this->getParam('id') );
			echo $this->render("settings");
		}
		else if ( $this->getParam('method') === "structure" )
		{
			$this->view->oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( (int) $this->getParam('id') );
			echo $this->render("structure");
		}
        else if ( $this->getParam('method') === "teams" )
        {
            $this->view->oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( (int) $this->getParam('id') );
            echo $this->render("teams");
        }
        else if ( $this->getParam('method') === "games" )
        {
            $this->view->oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( (int) $this->getParam('id') );
			$this->initGameWizardActions();
            echo $this->render("games");
        }
		else if ( $this->getParam('method') === "publish" )
		{
			$this->view->oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( (int) $this->getParam('id') );
			echo $this->render("publish");
		}
	*/
}

?>

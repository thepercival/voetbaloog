<?php

class Voetbal_CompetitieController extends Zend_Controller_Action
{
	public function init()
	{
		$cfgApp = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini');
		$this->view->bHasImport = ( $cfgApp->get( "import" ) !== null );
	}

	public function indexAction()
	{
		$this->view->oCompetitions = Voetbal_Competition_Factory::createObjectsFromDatabase();

		$sBtnSave = $this->getParam('btnsave');
		if ( strlen ( $sBtnSave ) > 0 ) {
			if ( $sBtnSave === "add" )
				$this->add();
			else if ( $sBtnSave === "remove" )
				$this->remove();
		}

		$this->view->oCompetitions->uasort(
			function( $oCompetitionA, $oCompetitionB ){
				return ( $oCompetitionA->getName() < $oCompetitionB->getName() ? -1 : 1 );
			}
		);
	}

	protected function add()
	{
		$sName = $this->getParam('name');
		if ( strlen ( $sName ) == 0 )
			$this->view->errormessage = "naam is niet ingevuld";
		else if ( strlen ( $sName ) > Voetbal_Competition::MAX_NAME_LENGTH )
			$this->view->errormessage = "naam mag niet langer dan ".Voetbal_Competition::MAX_NAME_LENGTH." karakters zijn";

		if ( $this->view->errormessage === null )
		{
			$oDbWriter = Voetbal_Competition_Factory::createDbWriter();
			$this->view->oCompetitions->addObserver( $oDbWriter );

			$oCompetition = Voetbal_Competition_Factory::createObject();
			$oCompetition->putId( "__new__" );
			$oCompetition->putName( $sName );
			$oCompetition->putAbbreviation( $this->getParam('abbreviation') );
			if ( $this->view->bHasImport === true ) {
				$sExternId = $this->getParam('externid');
				$sExternId = ( strlen ( $sExternId ) > 0 ) ? Import_Factory::$m_szExternPrefix . $sExternId : null;
				$oCompetition->putExternId( $sExternId );
			}

			$this->view->oCompetitions->add( $oCompetition );

			try
			{
				$oDbWriter->write();
				$this->view->savemessage = "competitie toegevoegd";
			}
			catch ( Exception $e)
			{
				$this->view->oCompetitions->remove( $oCompetition );
				$this->view->errormessage = "competitie ".$sName." bestaat al";
			}
		}
	}

	protected function remove()
	{
		$oDbWriter = Voetbal_Competition_Factory::createDbWriter();
		$this->view->oCompetitions->addObserver( $oDbWriter );

		$oCompetition = $this->view->oCompetitions[ $this->getParam("competitionid") ];

		$bRemoved = $this->view->oCompetitions->remove( $oCompetition );

		try
		{
			if ( $bRemoved )
				$oDbWriter->write();
			$this->view->savemessage = "competitie ".$oCompetition->getName()." verwijderd.";
		}
		catch ( Exception $e)
		{
			if ( $bRemoved )
				$this->view->oCompetitions->add( $oCompetition );
			$this->view->errormessage = "competitie kan niet verwijderd worden";
		}
	}

	public function updateAction()
	{
		/*$oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( 28 );

		$handlerMiddleware = Voetbal_Command_Main_Factory::getMiddleWare();
		$transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );
		$commandBus = new \League\Tactician\CommandBus([$transactionMiddleware,$handlerMiddleware]);

		$removeAddCSGames = new Voetbal_Command_RemoveAddCSGames( $oCompetitionSeason );

		$oDateTime = Agenda_Factory::createDateTime( "2016-03-30 12:00:00" );
		$removeAddCSGames->putStartDateTime( $oDateTime );
		$commandBus->handle( $removeAddCSGames );

		die();*/
		$this->view->oCompetition = Voetbal_Competition_Factory::createObjectFromDatabase( (int) $this->getParam("competitionid") );

		if ( $this->view->oCompetition === null )
			$this->render( 'index');

		if ( strlen ( $this->getParam('btnupdate') ) > 0 )
		{
			$sName = $this->getParam('name');
			if ( strlen ( $sName ) == 0 )
				$this->view->errormessage = "naam is niet ingevuld";
			else if ( strlen ( $sName ) > Voetbal_Competition::MAX_NAME_LENGTH )
				$this->view->errormessage = "naam mag niet langer dan ".Voetbal_Competition::MAX_NAME_LENGTH." karakters zijn";

			if ( $this->view->errormessage !== null )
				return;

			$oDbWriter = Voetbal_Competition_Factory::createDbWriter();
			$this->view->oCompetition->addObserver( $oDbWriter );

			$this->view->oCompetition->putName( $sName );
			$this->view->oCompetition->putAbbreviation( $this->getParam('abbreviation') );
			if ( $this->view->bHasImport === true ) {
				$sExternId = $this->getParam('externid');
				$sExternId = ( strlen ( $sExternId ) > 0 ) ? Import_Factory::$m_szExternPrefix . $sExternId : null;
				$this->view->oCompetition->putExternId( $sExternId );
			}

			if ( $oDbWriter->write() === true )
			{
				$this->view->savemessage = "competitie aangepast";
			}
		}
		else if ( strlen ( $this->getParam('btnaddseason') ) > 0 )
			$this->addSeason();
		elseif ( strlen ( $this->getParam('btndeleteseason') ) > 0 )
			$this->deleteSeason();
	}

	protected function addSeason()
	{
		$nSeasonId = (int) $this->getParam('seasonid');
		if ( $nSeasonId === 0 )
			$this->view->seasonerrormessage = "seizoen is niet ingevuld";
		$oSeason = Voetbal_Season_Factory::createObjectFromDatabase( $nSeasonId );
		if ( $oSeason === null )
			$this->view->seasonerrormessage = "het seizoen met id ".$nSeasonId." kon niet gevonden worden";

		$oDefaultAssociation = null;
		$nDefaultAssociationId = (int) $this->getParam('defaultassociationid');
		if ( $nDefaultAssociationId > 0 )
			$oDefaultAssociation = Voetbal_Association_Factory::createObjectFromDatabase( $nDefaultAssociationId );
		if ( $this->view->oCompetition->getSeasons()->count() === 0 and $oDefaultAssociation === null )
			$this->view->seasonerrormessage = "voor het eerste competitieseizoen moet er een bond worden gekozen";

		if ( $this->view->seasonerrormessage !== null )
			return;

		$addCompetitionSeasonCommand = new Voetbal_Command_AddCompetitionSeason( $this->view->oCompetition, $oSeason );
		$sExternId = $this->getParam('externid');
		$sExternId = ( strlen ( $sExternId ) > 0 ) ? Import_Factory::$m_szExternPrefix . $sExternId : null;
		$addCompetitionSeasonCommand->putExternId( $sExternId );
		$addCompetitionSeasonCommand->putDefaultAssociation( $oDefaultAssociation );
		$handlerMiddleware = Voetbal_Command_Main_Factory::getMiddleWare();
		$transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );

		$commandBus = new \League\Tactician\CommandBus([$transactionMiddleware,$handlerMiddleware]);
		$addCompetitionSeasonCommand->putBus( $commandBus );
		// die();

		try
		{
			$commandBus->handle( $addCompetitionSeasonCommand );
			$this->view->seasonsuccessmessage = "competitieseizoen is toegevoegd";
		}
		catch ( Exception $e )
		{
			$this->view->seasonerrormessage = "competitieseizoen kan niet worden toegevoegd : ".$e->getMessage();
		}
	}

	protected function deleteSeason()
	{
		$oCompetitionSeasons = $this->view->oCompetition->getSeasons();

		$oDbWriter = Voetbal_CompetitionSeason_Factory::createDbWriter();
		$oCompetitionSeasons->addObserver( $oDbWriter );

		$nCompetitionSeasonId = (int) $this->getParam("competitionseasonid");
		$oCompetitionSeason = $oCompetitionSeasons[ $nCompetitionSeasonId ];

		$bRealDelete = $this->getParam("realdelete") === "true";
		if ( $bRealDelete === true )
		{
			$oCompetitionSeasons->remove( $oCompetitionSeason );

			try
			{
				if ( $oDbWriter->write() === true )
					$this->view->seasonsavemessage = "competitieseizoen ".$oCompetitionSeason->getName()." verwijderd";
			}
			catch ( Exception $e)
			{
				$oCompetitionSeasons->add( $oCompetitionSeason );
				$this->view->seasonerrormessage = "competitieseizoen kan niet verwijderd worden : " . $e->getMessage();
			}
		}
		else
		{
			$sLink = $this->view->url."/btndeleteseason/true/realdelete/true/competitionid/".$this->view->oCompetition->getId()."/competitionseasonid/".$nCompetitionSeasonId."/";
			$this->view->seasonerrormessage = "klik <a href=\"".$sLink."\">hier</a> om definitief te verwijderen";
		}
	}

	public function ajaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		if ( $this->getParam('method') === "editform" )
		{
			$nId = (int) $this->getParam('id');
			if ( $nId > 0 )
				$this->view->oCompetition = Voetbal_Competition_Factory::createObjectFromDatabase( $nId );
			echo $this->render( "edit" );
		}
		else{
			echo "no input-param 'method'";
		}
	}
}

?>

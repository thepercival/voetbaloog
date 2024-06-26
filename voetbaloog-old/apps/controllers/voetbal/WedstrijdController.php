<?php

class Voetbal_WedstrijdController extends Zend_Controller_Action
{
	public function init()
	{
		$cfgApp = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini');
		$this->view->bHasImport = ($cfgApp->get("import") !== null);
	}

	public function indexAction()
	{
		$this->getResponse()->insert("extrajsincludes", $this->_helper->GetDateIncludes("js", $this->view->lazyload === true ) );
		$this->getResponse()->insert("extracssincludes", $this->_helper->GetDateIncludes("css", $this->view->lazyload === true ) );

		$this->view->oGame = Voetbal_Game_Factory::createObjectFromDatabase( (int) $this->getParam('id') );

		if ( $this->view->oGame === null ) {
			$this->view->errormessage = "wedstrijd met id ".$this->getParam('id')." kon niet gevonden worden";
			return;
		}

		$this->view->oPoule = $this->view->oGame->getPoule();
		$this->view->oRound = $this->view->oPoule->getRound();
		$this->view->oCompetitionSeason = $this->view->oRound->getCompetitionSeason();

		$this->view->activetabid = $this->getParam('activetabid');
		$this->view->tabidmain = "basisgegevens";
		$this->view->tabidplayermemberships = "spelers";

		/////////////////// handle save actions ////////////////////////////
		if ( strlen ( $this->getParam('btnupdate') ) > 0 )
			$this->update();
		else if ( strlen ( $this->getParam('btnvalidate') ) > 0 )
			$this->validate();

		// if ( $this->view->oGame->getState() === Voetbal_Factory::STATE_PLAYED )
		{
			$this->view->oGameParticipations = $this->view->oGame->getParticipations();
			$this->view->participationprefix = "cb_participation_membershipid_";
			if ( strlen ( $this->getParam('btnsaveparticipation') ) > 0 )
				$this->saveParticipation();
		}
	}

	public function ajaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		if ( $this->getParam('method') === "deelname" )
		{
			$nPlayerId = (int) $this->getParam('playerid');
			$this->view->oPlayer = Voetbal_Team_Membership_Player_Factory::createObjectFromDatabase( $nPlayerId );
			$this->view->oGame = Voetbal_Game_Factory::createObjectFromDatabase( (int) $this->getParam('gameid') );
			echo $this->view->render( "wedstrijd/deelname.phtml" );
		}
		else {
			echo "no input-param 'method'";
		}
	}

	protected function update()
	{
		$this->view->mainsavemessage = array();

		$handlerMiddleware = Voetbal_Command_Main_Factory::getMiddleWare();
		$transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );
		$commandBus = new \League\Tactician\CommandBus([$transactionMiddleware,$handlerMiddleware]);

		try
		{
			$nState = ( $this->getParam('played') === "on" ) ? Voetbal_Factory::STATE_PLAYED : Voetbal_Factory::STATE_SCHEDULED;
			$oGoals = new stdClass();
			$oGoals->homegoals = (int)$this->getParam('homegoals');
			$oGoals->awaygoals = (int)$this->getParam('awaygoals');

            $oLocation = null;
            $nLocationId = (int)$this->getParam('locationid');
            if ( $nLocationId > 0 )
                $oLocation = Voetbal_Location_Factory::createObjectFromDatabase( $nLocationId );

			if ( $this->view->oRound->getType() == Voetbal_Round::TYPE_KNOCKOUT )
			{
				$oGoals->homegoalsextratime = (int)$this->getParam('homegoalsextratime');
				$oGoals->awaygoalsextratime = (int)$this->getParam('awaygoalsextratime');
				$oGoals->homegoalspenalty = (int)$this->getParam('homegoalspenalty');
				$oGoals->awaygoalspenalty = (int)$this->getParam('awaygoalspenalty');
			}
			// command update Game
			$updateGameCommand = new Voetbal_Command_UpdateGame( $this->view->oGame, $oGoals, $nState );
			$oDateTime = $this->_helper->getDateTime( 'gamedatetime', true );
			$updateGameCommand->putStartDateTime( $oDateTime );
			$updateGameCommand->putNumber( (int)$this->getParam('number') );
            $updateGameCommand->putLocation( $oLocation );
            if ( $nState === Voetbal_Factory::STATE_SCHEDULED and $this->getParam('switchhomeaway') === "on" ){
                $updateGameCommand->switchHomeAway();
            }
			$updateGameCommand->putBus( $commandBus );
			$commandBus->handle( $updateGameCommand );

			$this->view->mainsavemessage[] = "de wedstrijd is bijgewerkt";

			$vtRetVal = $this->updateCache( $this->view->oCompetitionSeason );
			if ( strlen( $vtRetVal ) > 0 )
				$this->view->mainsavemessage[] = $vtRetVal;
		}
		catch( Exception $e )
		{
			$this->view->mainerrormessage = "onbekende fout: ".$e->getMessage();
		}
	}

	protected function validate()
	{
		$this->view->mainsavemessage = array();

		$handlerMiddleware = Voetbal_Command_Main_Factory::getMiddleWare();
		$transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );
		$commandBus = new \League\Tactician\CommandBus([$transactionMiddleware,$handlerMiddleware]);

		try
		{
			$validateGameCommand = new Voetbal_Command_ValidateGame( $this->view->oGame );
			$vtRetVal = $commandBus->handle( $validateGameCommand );

			if ( $vtRetVal !== true )
				$this->view->mainerrormessage = $vtRetVal;
			else {
				$this->view->mainsavemessage = "de wedstrijs is gevalideerd";
			}
		}
		catch( Exception $e )
		{
			$this->view->mainerrormessage = "onbekende fout: ".$e->getMessage();
		}
	}

	protected function updateCache( $oCompetitionSeason )
	{
		$oCache = ZendExt_Cache::getDefaultCache();
		$oCache->clean( Zend_Cache::CLEANING_MODE_MATCHING_TAG,	array( 'competitionseason'.$oCompetitionSeason->getId() ) );
		return "cache geleegd";
	}

	protected function saveParticipation()
	{
		$oDbWriter = Voetbal_Game_Participation_Factory::createDbWriter();

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter("Voetbal_Game_Participation::TeamMembershipPlayer", "EqualTo", (int) $this->getParam("playerid") );
		$oOptions->addFilter("Voetbal_Game_Participation::Game", "EqualTo", $this->view->oGame );
		$oParticipations = Voetbal_Game_Participation_Factory::createObjectsFromDatabase( $oOptions );

		$oParticipations->addObserver( $oDbWriter );

		$bStarts = $this->getParam("starts") === "on";
		$nIn = (int) $this->getParam("in");

		$oParticipation = null;
		if ( $bStarts !== true and $nIn === 0 )
			$oParticipations->flush();
		else if ( $oParticipations->count() === 0 )
		{
			$oParticipation = Voetbal_Game_Participation_Factory::createObject();
			$oParticipation->putId( "__NEW__" );
			$oParticipation->putGame( $this->view->oGame );
			$oParticipation->putTeamMembershipPlayer( (int) $this->getParam("playerid") );
			$oParticipation->putYellowCardOne( 0 );
			$oParticipation->putYellowCardTwo( 0 );
			$oParticipation->putRedCard( 0 );
			$oParticipation->putOut( 0 );
			$oParticipation->putIn( 0 );
			$oParticipations->add( $oParticipation );
		}
		else {
			$oParticipation = $oParticipations->first();
			$oParticipation->addObserver( $oDbWriter );
		}

		if ( $oParticipation !== null ) // update
		{
			$oParticipation->putYellowCardOne( (int) $this->getParam("yellowcardone") );
			$oParticipation->putYellowCardTwo( (int) $this->getParam("yellowcardtwo") );
			$oParticipation->putRedCard( (int) $this->getParam("redcard") );
			$oParticipation->putOut( (int) $this->getParam("out") );
			$oParticipation->putIn( $nIn );
		}

		try
		{
			if ( $oDbWriter->write() == true )
			{
				$this->view->participationsavemessage = "de deelname is bijgewerkt";
				if ( $oParticipation !== null )
					$this->updateGoals( $oParticipation );

				$vtRetVal = $this->updateCache( $this->view->oCompetitionSeason );
				if ( strlen( $vtRetVal ) > 0 )
					$this->view->participationsavemessage .= "<br>" . $vtRetVal;
			}
			Patterns_Event_Factory::handle( "gameparticipationchanged", $this->view->oGame );
		}
		catch ( Exception $oException )
		{
			$this->view->participationerrormessage = "de deelname kon niet worden bijgewerkt : ".$oException->getMessage();
		}
	}

	protected function updateGoals( $oParticipation )
	{
		$oGoals = $oParticipation->getGoals();
		$oDbWriter = Voetbal_Goal_Factory::createDbWriter();
		$oGoals->addObserver( $oDbWriter );

		$oGoals->flush();

		for( $nI = 1 ; $nI <= 5 ; $nI++ )
		{
			$nMinute = (int) $this->getParam( "goal".$nI."-m" );
			if ( $nMinute === 0 )
				break;

			$oGoal = Voetbal_Goal_Factory::createObject();
			$oGoal->putId( "__NEW__" . $nI );
			$oGoal->putGameParticipation( $oParticipation );
			$oGoal->putMinute( $nMinute );
			$oGoal->putOwnGoal( $this->getParam( "goal".$nI."-o" ) === "on" );
			$oGoal->putPenalty( $this->getParam( "goal".$nI."-p" ) === "on" );

			$oGoals->add( $oGoal );
		}

		try
		{
			if ( $oDbWriter->write() == true )
			{
				$this->view->participationsavemessage .= "<br>goals bijgewerkt en cache geleegd";
			}
		}
		catch ( Exception $oException )
		{
			$this->view->participationerrormessage = "de goals kon niet worden bijgewerkt: ".$oException->getMessage();
		}
	}
}

?>

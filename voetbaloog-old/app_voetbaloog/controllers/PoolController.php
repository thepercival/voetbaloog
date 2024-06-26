<?php

/**
  * @TODO
  * 1 admin aanpassen naar nieuwe layout. Kijken als dit op sfg komt?
  * wk 80% van punten vanaf achtste finale

/*
 * @author coen
 */
class PoolController extends Zend_Controller_Action
{
	protected $emergencyUserName = null;
	protected $emergencyPoolName = null;

	public function preDispatch()
	{
		if ( $this->getRequest()->getActionName() === "aanmaken" or $this->getRequest()->getActionName() === "ajax" )
			return;

		$this->view->oPool = $this->getPoolFromParams();

		if ( $this->view->oPool === null ) {
			$sErrorMessage = urlencode( "de pool met id ".$this->getParam('poolid')." kon niet gevonden worden" );
			$this->redirect( Zend_Registry::get("baseurl") . "index/index/mainerrormessage/".$sErrorMessage."/" );
		}

		// Get pooluser
		if ( $this->view->oUser !== null )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "VoetbalOog_Pool_User::User", "EqualTo", $this->view->oUser );
			$oOptions->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $this->view->oPool );
			$this->view->oPoolUser = VoetbalOog_Pool_User_Factory::createObjectFromDatabase( $oOptions );

			$this->view->bWelcome = ( strlen( $this->getParam('welcome') ) > 0 );

			if( $this->emergencyPoolName && $this->emergencyPoolName === $this->view->oPool->getName()
				&& $this->emergencyUserName !== null && $this->emergencyUserName === $this->view->oUser->getName() ) {
				$this->view->emergency = true;
			}
		}

		$this->view->oNow = Agenda_Factory::createDateTime();
		if( $this->view->emergency === true ) {
			$this->view->oNow = $this->view->oPool->getStartDateTime()->modify('-1 day');
		}
		$this->view->tsJSNow = $this->view->oNow->getTimeStamp() * 1000;
		$this->view->oCompetitionSeason = $this->view->oPool->getCompetitionSeason();

		$this->view->bCompetitionSeasonHasStarted = ( $this->view->oNow > $this->view->oCompetitionSeason->getStartDateTime() );

		$this->view->bPoolHasStarted = ( $this->view->oNow > $this->view->oPool->getStartDateTime() );
		$this->view->bPoolHasEnded = ( $this->view->oNow > $this->view->oPool->getEndDateTime() );
		$this->view->bPoolIsActive = ( $this->view->bPoolHasStarted and !$this->view->bPoolHasEnded );

		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
		$this->view->bCordova = $oSession->cordova;
	}

	protected function getPoolFromParams()
	{
		$nPoolId = (int) $this->getParam('poolid');
		if ( $nPoolId > 0 )
			return  VoetbalOog_Pool_Factory::createObjectFromDatabase( $nPoolId );

		$sName = $this->getParam('name');
		if ( strlen( $sName ) > 0 )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "VoetbalOog_Pool::Name", "EqualTo", str_replace( "_", " ", $sName ) );
			$sSeasonName = $this->getParam('seizoen');
			if ( strlen( $sSeasonName ) > 0 )
			{
				$oOptions->addFilter( "Voetbal_Season::Name", "EqualTo", $sSeasonName );
			}
			else {
				$oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
				$oOptions->addLimit( 1 );
			}
			return VoetbalOog_Pool_Factory::createObjectFromDatabase( $oOptions );
		}
		return null;
	}

	public function aanmakenAction()
	{
		$this->view->oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjects();
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "Voetbal_CompetitionSeason::Public", "EqualTo", true );
			$oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
			$bStarted = false; $bEnded = null;
			$oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjectsFromDatabaseCustom( $bStarted, $bEnded, $oOptions );
			foreach( $oCompetitionSeasons as $oCompetitionSeasonIt ) {
				if ( VoetbalOog_Round_BetConfig_Factory::createObjectsFromDatabaseExt( $oCompetitionSeasonIt )->count() > 0 )
					$this->view->oCompetitionSeasons->add( $oCompetitionSeasonIt );
			}
		}
		$nCompetitionSeasonId = (int) $this->getParam('competitionseasonid');
		$sName = trim( $this->getParam('name') );

		$nPoolStake = (int) $this->getParam( "stake" );

		$oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( $nCompetitionSeasonId );

		if ( strlen( $this->getParam('btn_step3_finish') ) === 0 )
			return;

		if ( strlen( $sName ) < 3 )
			$this->view->adderrormessage = "de naam moet uit minimaal 3 karakters bestaan.";
		elseif ( strlen( $sName ) > 20 )
			$this->view->adderrormessage = "de naam mag uit maximaal 20 karakters bestaan.";
		if ( $oCompetitionSeason === null )
			$this->view->adderrormessage = "de competitie is niet ingevuld.";

		if ( $this->view->adderrormessage === null )
		{
			if ( $this->isNameAvailableHelper( $oCompetitionSeason, $sName ) === true )
			{
				$oOptions = Construction_Factory::createOptions();
				$oOptions->addFilter( "VoetbalOog_Pool::Name", "EqualTo", $sName );
				$oPoolsTmp = VoetbalOog_Pool_Factory::createObjectsFromDatabase( $oOptions );
				if ( $oPoolsTmp->count() > 0 )
					$sName = $oPoolsTmp->first()->getName(); // for right spelling
			}
			else
			{
				$this->view->adderrormessage = "Poolnaam bestaat al, kies een andere naam.";
			}
		}

		if ( $this->view->adderrormessage !== null )
			return;

		// Start : Add pool
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_Pool::CompetitionSeason", "EqualTo", $oCompetitionSeason );
		$oPools = VoetbalOog_Pool_Factory::createObjectsFromDatabase( $oOptions );

		$oDbWriter = VoetbalOog_Pool_Factory::createDbWriter();
		$oPools->addObserver( $oDbWriter );

		/** @var VoetbalOog_Pool $oPool */
		$oPool = VoetbalOog_Pool_Factory::createObject();
		$oPool->putId( "__NEW__" );
		$oPool->putName( $sName );
		$oPool->putCompetitionSeason( $oCompetitionSeason );
		$oPool->putStake( $nPoolStake );

		$oPools->add( $oPool );
		// End : Add pool

		$oDb = Zend_Registry::get("db");

		try
		{
			$oDefaultRoundBetConfigs = VoetbalOog_Round_BetConfig_Factory::createObjectsFromDatabaseExt( $oCompetitionSeason );
			$oDb->beginTransaction();
			$oDbWriter->write();

			// Begin : Write roundbetconfigs
			{
				$oRoundBetConfigDbWriter = VoetbalOog_Round_BetConfig_Factory::createDbWriter();
				$oRoundBetConfigsPool = $oPool->getBetConfigs();
				$oRoundBetConfigsPool->addObserver( $oRoundBetConfigDbWriter );

				$oRounds = $oCompetitionSeason->getRounds();
				foreach( $oRounds as $oRound )
				{
					$oRoundBetConfigs = $oDefaultRoundBetConfigs[ $oRound->getId() ];
					if ( $oRoundBetConfigs === null ) {
						$oRoundBetConfigs = VoetbalOog_Round_BetConfig_Factory::createObjects();
					}

					foreach ( $oRoundBetConfigs as $oRoundBetConfig )
					{
						$oRoundBetConfigPool = VoetbalOog_Round_BetConfig_Factory::createObject();
						$oRoundBetConfigPool->putId( "__NEW__".$oRoundBetConfig->getId() );
						$oRoundBetConfigPool->putRound( $oRound );
						$oRoundBetConfigPool->putBetType( $oRoundBetConfig->getBetType() );
						$oRoundBetConfigPool->putBetTime( $oRoundBetConfig->getBetTime() );
						$oRoundBetConfigPool->putPoints( $oRoundBetConfig->getPoints() );
						// DIFFERS
						$oRoundBetConfigPool->putPool( $oPool );

						$oRoundBetConfigsPool->add( $oRoundBetConfigPool );
					}
				}

				$oRoundBetConfigDbWriter->write();
			}
			// End : Write roundbetconfigs

			// Begin : Write payments
			{
				$oPayments = VoetbalOog_Pool_Payment_Factory::createDefault( $oPool );
				$oPaymentsTmp = VoetbalOog_Pool_Payment_Factory::createObjects();
				$oPaymentDbWriter = VoetbalOog_Pool_Payment_Factory::createDbWriter();
				$oPaymentsTmp->addObserver( $oPaymentDbWriter );
				foreach( $oPayments as $oPayment )
					$oPaymentsTmp->add( $oPayment );
				$oPaymentDbWriter->write();
			}
			// End : Write payments

			$oDb->commit();

			$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'auth');
			$sKey = hash( "sha256", $oPool->getName() . $cfgAuth->hashkey );

			// Begin : Add user to pool
			$sParams = "poolid/".$oPool->getId()."/";
			$sParams .= "btnjoin/submitted/";
			$sParams .= "admin/1/";
			$sParams .= "key/".$sKey."/";
			$this->redirect( Zend_Registry::get("baseurl")."pool/meedoen/".$sParams );
			// End : Add user to pool
		}
		catch ( Exception $e )
		{
			$oDb->rollback();
			$oPools->remove( $oPool );
			$this->view->adderrormessage = "pool kon niet worden toegevoegd : ".$e->getMessage();
		}
	}

	public function meedoenAction()
	{
		$nPoolId = (int) $this->getParam('poolid');
		if ( $nPoolId > 0 )
			$this->view->oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( $nPoolId );

		if ( $this->view->oPool === null ) {
			$this->view->errormessage = "de pool met id ".$nPoolId." kon niet gevonden worden";
			return;
		}

		$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'auth');
		$sKey = hash( "sha256", $this->view->oPool->getName() . $cfgAuth->hashkey );
		{
			$sKeyParam = str_replace( " ", "", $this->getParam('key') );
			$bValidKey = ( strlen( $sKeyParam ) >= 60 and strlen( $sKeyParam ) <= 64 ) and ( strpos( $sKey, $sKeyParam ) !== false );
			if ( $bValidKey !== true ) {
				$this->view->errormessage = "de meedoen-link die u heeft gekregen is niet correct";
				return;
			}
		}

		if ( $this->view->oUser === null ) {
			$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
			$oSession->joinpoolid = $nPoolId;
			$oSession->joinkey = $sKey;
			$this->redirect( Zend_Registry::get("baseurl")."user/login/" );
			die();
		}

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $this->view->oPool );
		$oOptions->addFilter( "VoetbalOog_Pool_User::User", "EqualTo", $this->view->oUser );
		$oPoolUserTmp = VoetbalOog_Pool_User_Factory::createObjectFromDatabase( $oOptions );
		if ( $oPoolUserTmp !== null ) {
			$this->view->errormessage = "je doet al mee met deze pool";
			return;
		}

		if ( Agenda_Factory::createDateTime() > $this->view->oPool->getStartDateTime() ) {
			$this->view->errormessage = "je kunt niet meer met deze pool meedoen, de pool is al begonnen";
			return;
		}

		$bAdmin = ( ( (int) $this->getParam('admin') ) === 1 );

		// Start : Add pooluser
		$oPoolUsers = VoetbalOog_Pool_User_Factory::createObjects();
		$oDbWriter = VoetbalOog_Pool_User_Factory::createDbWriter();
		$oPoolUsers->addObserver( $oDbWriter );

		$oPoolUser = VoetbalOog_Pool_User_Factory::createObject();
		$oPoolUser->putId( "__NEW__" );
		$oPoolUser->putUser( $this->view->oUser );
		$oPoolUser->putPool( $this->view->oPool );
		$oPoolUser->putAdmin( $bAdmin );
		$oPoolUser->putPaid( false );

		$oPoolUsers->add( $oPoolUser );
		// End : Add pooluser

		try
		{
			$oDbWriter->write();

			$oCache = ZendExt_Cache::getDefaultCache();
			$oCache->remove( 'pool'.$this->view->oPool->getId().'stand' );
			$oCache->remove( 'pool'.$this->view->oPool->getId().'alltimes' );

			if ( $bAdmin === true )
				$this->redirect( Zend_Registry::get("baseurl")."poolbeheer/index/poolid/".$nPoolId."/activetab/tabuitnodigen/welcome/true/" );
			else
				$this->redirect( Zend_Registry::get("baseurl")."pool/index/poolid/".$nPoolId."/welcome/true/" );

			die();
		}
		catch ( Exception $e)
		{
			$oPoolUsers->remove( $oPoolUser );
			$this->view->errormessage = "je kon niet worden toegevoegd aan de pool vanwege:".$e->getMessage();
		}

	}

	public function indexAction()
	{
		if ( $this->view->oPoolUser !== null and $this->view->bPoolHasStarted === false )
		{
			$this->voorspellingenAction();
			$this->render("voorspellingen");
			return;
		}

		$this->standAction();
		$this->render("stand");
		return;
	}

	public function berichtenAction()
	{
		$this->view->sPageTitle = "berichtenbord van de pool " . $this->view->oPool->getName() . " voor de VoetbalOog van de " . $this->view->oCompetitionSeason->getName();
		$this->view->sPageDescription = "plaats een bericht op het form | berichtenbord om spelregels, transfers, etc. met elkaar te bespreken.";

		$this->view->arrMenuItems  = $this->getMenuItems();
		$this->view->arrMenuItems["berichten"]["activeclass"] = true;

		$this->view->saved = ( $this->getParam("saved") === "on" );
		if ( strlen( $this->getParam("btnberichttoevoegen") ) === 0 )
			return;

		$sMessage = trim( $this->getParam('message') );
		if ( strlen ( $sMessage ) === 0 ) {
			$this->view->messageboarderrormessage = 'je kunt geen leeg bericht plaatsen';
			return;
		}

		$oDbWriter = VoetbalOog_Message_Factory::createDbWriter();

		$oMessages = VoetbalOog_Message_Factory::createObjects();
		$oMessages->addObserver( $oDbWriter );

		$oMessage = VoetbalOog_Message_Factory::createObject();
		$oMessage->putId("__NEW__");
		$sMessage = htmlspecialchars( $sMessage, ENT_COMPAT, "UTF-8" );
		$oMessage->putMessage( $sMessage );
		$oMessage->putDateTime( Agenda_Factory::createDateTime() );
		$oMessage->putPoolUser( $this->view->oPoolUser );

		$oMessages->add( $oMessage );

		try
		{
			$oDbWriter->write();
			$sOptions = "poolid/" . $this->view->oPool->getId() . "/saved/on/";
			$this->redirect( Zend_Registry::get("baseurl")."pool/berichten/" . $sOptions );
		}
		catch ( Exception $e)
		{
			$this->view->messageboarderrormessage = $e->getMessage();
		}
	}

	public function voorspellingenAction()
	{
		$this->getResponse()->insert("extrajsincludes", $this->_helper->AddIncludes() );

		$this->view->arrMenuItems  = $this->getMenuItems();
		$this->view->arrMenuItems["voorspellingen"]["activeclass"] = true;

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $this->view->oPool );
		$nNrOfBets = VoetbalOog_Bet_Factory::getNrOfObjectsFromDatabase( $oOptions );
		$this->view->bHasBets = ( $nNrOfBets > 0 );

		$this->view->bShowRead = ( $this->view->bPoolHasStarted === true );
		$this->view->bShowEdit = ( $this->view->oPoolUser !== null and $this->view->bPoolHasEnded === false ) || $this->view->emergency === true;

		if ( $this->view->bShowEdit === true and $this->view->bShowRead === false ) {
			$this->view->oPoolsToCopy = VoetbalOog_Pool_Factory::createObjectsWithSameRoundBetConfigFromDatabase( $this->view->oPoolUser );
			$this->view->bShowCopy = ( $this->view->oPoolsToCopy->count() > 0 );
		}

		// kijk wanneer deze waarde gezet moet worden
		$this->view->sbeteditcontrolid = "beteditcontrol";
		$this->view->sbetviewcontrolid = "betviewcontrol";

		if ( strlen ( $this->getParam('btnsavebets') ) > 0 ) {
			$this->view->messagesavebets = $this->_helper->SaveBets( $this->view->sbeteditcontrolid, $this->view->oPoolUser, $this->view->oNow );
			$this->view->betsactivetabid = "betsedit";
		}
		else if ( strlen ( $this->getParam('btncopybets') ) > 0 ) {
			$this->view->messagecopybets = $this->handleCopyBets( $this->view->oPoolUser );
			$this->view->betsactivetabid = "betsedit";
		}

		$sRoundNr = $this->getParam("roundnr");
		if ( strlen( $sRoundNr ) > 0 )
			$this->view->nRoundNr = (int) $sRoundNr;
	}

	public function standAction()
	{
		$this->view->arrMenuItems  = $this->getMenuItems();
		$this->view->arrMenuItems["stand"]["activeclass"] = true;

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_Pool::Name", "EqualTo", $this->view->oPool->getName() );
		$oOptions->addFilter( "VoetbalOog_Pool::Id", "NotEqualTo", $this->view->oPool );
		$this->view->bShowAlltimes = ( VoetbalOog_Pool_Factory::getNrOfObjectsFromDatabase( $oOptions ) > 0 );
	}

	public function standallertijdenAction()
	{
		$this->view->arrMenuItems  = $this->getMenuItems();
	}


	public function toernooiAction()
	{
		$this->getResponse()->insert("extrajsincludes", $this->_helper->AddIncludes() );

		$this->view->arrMenuItems  = $this->getMenuItems();
		$this->view->arrMenuItems["toernooi"]["activeclass"] = true;

		$this->view->oCompetitionSeason = $this->view->oPool->getCompetitionSeason();
	}

	public function getMenuItems()
	{
		$arrMenuItems = array( "stand" => array( "activeclass" => false, "action" => "pool/stand/", "glyph" => "flaticon-stand" ) );
		$arrMenuItems["voorspellingen"] = array( "activeclass" => false, "action" => "pool/voorspellingen/", "glyph" => "flaticon-voorspellingen" );
		$arrMenuItems["berichten"] = array( "activeclass" => false, "action" => "pool/berichten/", "glyph" => "flaticon-berichtenbord" );
		$arrMenuItems["toernooi"] = array( "activeclass" => false, "action" => "pool/toernooi/", "glyph" => "flaticon-three" );

		if ( $this->view->oPoolUser !== null and $this->view->oPoolUser->getAdmin() === true )
			$arrMenuItems["opties"] = array( "activeclass" => false, "action" => "poolbeheer/index/", "glyph" => "flaticon-instellingen" );

		return $arrMenuItems;
	}

	public function totaaloverzichtpdfAction()
	{
		ini_set('memory_limit', '64M');

		$oPdf = VoetbalOog_Pdf_Factory::createPoolTotal( $this->view->oPool, $this->view->oPoolUser );

		$this->_helper->pdf( $oPdf, "totaaloverzicht.pdf", "inline" );
	}

	public function mijnvoorspellingenpdfAction()
	{
		$oPdf = VoetbalOog_Pdf_Factory::createPoolForm( $this->view->oPoolUser );

		$this->_helper->pdf( $oPdf, "invulformulier.pdf", "inline" );
	}

	public function ajaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		if ( $this->getParam('method') === "getobject" )
		{
			$oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( (int) $this->getParam('poolid') );
			$nDataFlag = (int) $this->getParam('dataflag');

			// Needs to be set to get bets in JSON ( security )
			VoetbalOog_Bet_Factory::putSessionUser( $this->view->oUser );

			header('Content-type: application/json');
			// echo VoetbalOog_Pool_Factory::convertObjectToJSON( $oPool, $nDataFlag );
			echo json_encode( json_decode( VoetbalOog_Pool_Factory::convertObjectToJSON( $oPool, $nDataFlag ) ) );
		}
		else if ( $this->getParam('method') === "isnameavailable" )
		{
			$nCompetitionSeasonId = (int) $this->getParam('competitionseasonid');
			$sNewName = $this->getParam('newname');

			echo $this->isNameAvailableHelper( $nCompetitionSeasonId, $sNewName ) ? "true" : "false";
		}
		elseif ( $this->getParam('method') === "getavailables" )
		{
			$nCompetitionSeasonId = (int) $this->getParam('competitionseasonid');

			$oPools = VoetbalOog_Pool_Factory::createObjectsAvailable( $nCompetitionSeasonId, $this->view->oUser );

			// var_dump( $oPools->count() );
			header('Content-type: application/json');
			echo VoetbalOog_Pool_Factory::convertObjectsToJSON( $oPools );
		}
		elseif ( $this->getParam('method') === "getrecords" )
		{
			$this->view->bPositive = $this->getParam('positive') === "true";
			$nPoolId = (int) $this->getParam('poolid');
			$this->view->oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( $nPoolId );

			$oCache = ZendExt_Cache::getDefaultCache();
			$sCacheId = "pool".$this->view->oPool->getId()."alltimerecords".$this->view->bPositive;
			$sHtml = $oCache->load( $sCacheId );
			if( $sHtml === false or APPLICATION_ENV !== "production" )
			{
				$sHtml = $this->render( "records" );
				$oCache->save( $sHtml, $sCacheId, array('pool'.$this->view->oPool->getId() ) );
			}
			echo $sHtml;
		}
		elseif ( $this->getParam('method') === "getpooluseridalltimes" )
        {
            $oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( (int) $this->getParam('poolid') );
            list($oCompetitionSeasons,$arrAllTimeRankTotals, $arrAllTimeRankUsers) = VoetbalOog_Pool_Factory::getAllTimeRanking($oPool);
            reset($arrAllTimeRankTotals);
            $nUserId = key($arrAllTimeRankTotals);
            if ( $nUserId !== null and count($arrAllTimeRankTotals) > 0 ){
                $oOptions = Construction_Factory::createOptions();
                $oOptions->addFilter( "VoetbalOog_Pool_User::User", "EqualTo", $nUserId );
                $oOptions->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $oPool );
                $oPoolUser = VoetbalOog_Pool_User_Factory::createObjectFromDatabase( $oOptions  );
                if ( $oPoolUser !== null ){
                    echo $oPoolUser->getId();
                }
            }
        }
		else {
			echo "no input-param 'method'";
		}
		/*elseif ( $this->getParam('method') === "getusers" )
		{
			$nPoolId = (int) $this->getParam('poolid');

			$oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( $nPoolId );
			$oUsers = VoetbalOog_User_Factory::createObjectsFromPoolFromDatabase( $oPool, null, "VoetbalOog_Pool" );

			$sRetVal = "";
			foreach ( $oUsers as $oUser )
			{
				if ( strlen( $sRetVal ) !== 0 )
					$sRetVal .= ";";

				$sRetVal .= $oUser->getEmailAddress()."|".$oUser->getId();
			}

			echo $sRetVal;
		}*/
	}

	protected function isNameAvailableHelper( $vtCompetitionSeason, $sNewName )
	{
		if ( $vtCompetitionSeason !== null and is_int( $vtCompetitionSeason ) and $vtCompetitionSeason > 0 )
			$vtCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( $vtCompetitionSeason );

		return VoetbalOog_Pool_Factory::isNameAvailable( $vtCompetitionSeason, $this->view->oUser, $sNewName );
	}

	protected function handleCopyBets( $oPoolUser )
	{
		$nPoolToCopyFromId = (int) $this->getRequest()->getParam( "pooltocopyfromid" );
		$nPoolToCopyToId = (int) $this->getRequest()->getParam( "pooltocopytoid" );

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_Pool_User::User", "EqualTo", $oPoolUser->getUser() );
		$oOptions->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $nPoolToCopyFromId );
		$oPoolUserToCopyFrom = VoetbalOog_Pool_User_Factory::createObjectFromDatabase( $oOptions );

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_Pool_User::User", "EqualTo", $oPoolUser->getUser() );
		$oOptions->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $nPoolToCopyToId );
		$oPoolUserToCopyTo = VoetbalOog_Pool_User_Factory::createObjectFromDatabase( $oOptions );

		try
		{
			$handlerMiddleware     = VoetbalOog_Command_Main_Factory::getMiddleWare();
			$transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get( "db" ) );
			$commandBus            = new \League\Tactician\CommandBus( [ $transactionMiddleware, $handlerMiddleware ] );

			// command update RoundbetConfigs
			$copyBetsCommand = new VoetbalOog_Command_CopyBets( $oPoolUserToCopyFrom, $oPoolUserToCopyTo );
			$copyBetsCommand->putBus( $commandBus );
			$commandBus->handle( $copyBetsCommand );

			$this->view->copybetssuccessmessage = $copyBetsCommand->getSuccessMessage();;

		} catch ( Exception $e ) {
			$this->view->copybetserrormessage = "de voorspellingen konden niet worden gekopieerd : " . $e->getMessage();
		}
	}

}

?>

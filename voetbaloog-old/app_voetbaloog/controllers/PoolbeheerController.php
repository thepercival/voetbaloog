<?php

class PoolBeheerController extends Zend_Controller_Action
{
	public function preDispatch()
	{
		$nPoolId = (int) $this->getParam('poolid');
		if ( $nPoolId > 0 )
			$this->view->oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( $nPoolId );

		if ( $this->view->oPool === null ) {
			$sErrorMessage = urlencode( "de pool met id ".$this->getParam('poolid')." kon niet gevonden worden" );
			$this->redirect( Zend_Registry::get("baseurl") . "index/index/mainerrormessage/".$sErrorMessage."/" );
		}

		// Get pooluser
		if ( $this->view->oUser !== null )
		{
			$oFilters = Construction_Factory::createOptions();
			$oFilters->addFilter( "VoetbalOog_Pool_User::User", "EqualTo", $this->view->oUser );
			$oFilters->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $this->view->oPool );
			$this->view->oPoolUser = VoetbalOog_Pool_User_Factory::createObjectFromDatabase( $oFilters );
		}

		if ( $this->view->oPoolUser === null ) {
			$sErrorMessage = urlencode( "je doet niet mee aan deze pool" );
			$this->redirect( $this->view->urlcontroller . "/index/autherrormessage/".$sErrorMessage."/" );
		}
		if ( !$this->view->oPoolUser->getAdmin() ) {
			$sErrorMessage = urlencode( "je bent geen organisator van deze pool" );
			$this->redirect( $this->view->urlcontroller . "/index/autherrormessage/".$sErrorMessage."/" );
		}

		$oNow = Agenda_Factory::createDateTime();
		$this->view->oCompetitionSeason = $this->view->oPool->getCompetitionSeason();

		$this->view->bCompetitionSeasonHasEnded = ( $oNow > $this->view->oCompetitionSeason->getEndDateTime() );
		$this->view->bPoolHasStarted = ( $oNow > $this->view->oPool->getStartDateTime() );
		$this->view->bPoolHasEnded = ( $oNow > $this->view->oPool->getEndDateTime() );

		if ( $this->view->bPoolHasStarted === false )
		{
			$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'auth');
			$sKey = hash( "sha256", $this->view->oPool->getName() . $cfgAuth->hashkey );

			$this->view->joinlink = Zend_Registry::get("baseurl") . "pool/meedoen/poolid/" . $this->view->oPool->getId() . "/key/" . $sKey . "/";
		}
	}

	public function indexAction()
	{
		$this->view->autherrormessage = urldecode( $this->getParam("autherrormessage") );

		if ( strlen( $this->view->autherrormessage ) > 0 ) {
			$this->render("autherror");
			return;
		}

		if ( $this->view->bPoolHasStarted === false )
		{
			$this->uitnodigenAction();
			$this->render("uitnodigen");
			return;
		}

		$this->deelnemersAction();
		$this->render("deelnemers");
		return;
	}

	public function uitnodigenAction()
	{
		if ( $this->view->bPoolHasStarted === true )
		{
			$sErrorMessage = urlencode( "je kunt geen uitnodigingen meer versturen, omdat de pool al begonnen is" );
			$this->redirect( $this->view->urlcontroller . "/index/autherrormessage/".$sErrorMessage."/" );
		}

		$this->view->successmessage = urldecode( $this->getParam("successmessage") );
		$this->view->errormessage = urldecode( $this->getParam("errormessage") );

		$this->view->arrMenuItems  = $this->getMenuItems();
		$this->view->arrMenuItems["uitnodigen"]["activeclass"] = true;

		$this->view->bWelcome = ( strlen( $this->getParam('welcome') ) > 0 );

		// if ( strlen ( $this->getParam('btnuitnodigen') ) > 0 )
			// $this->handleUitnodigen();
	}

	/*private function handleUitnodigen()
	{
		$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini', 'auth');

		$sMessage = str_replace( PHP_EOL, "<br>", $this->view->messagedescription );

		$arrNamesSuccess = array(); $arrNamesError = array();

		$arrParams = $this->getRequest()->getParams(); // getUserParams
		foreach ( $arrParams as $sId => $sValue )
		{
			if ( strpos( $sId, 'emailreceiver_' ) !== false )
			{
				$nCounter = (int) substr( $sId, strlen( 'emailreceiver_' ) );
				$sName = $sValue;
				$sEmail = $arrParams[ 'emailreceiveraddress_' . $nCounter ];

				// get email from user from database
				if ( $sEmail === "automatisch" )
				{
					$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $sName );
					if ( $oUser === null )
						continue;
					$sEmail = $oUser->getEmailAddress();
				}

				try
				{
					$sMyMessage = str_replace( "**", $sName, $sMessage );
					if ( APPLICATION_ENV === "production" ) {
						RAD_Email::sendHtml( $sEmail, $this->view->messagesubject, $sMyMessage );
						RAD_Email::sendHtml( $cfgAuth->superadminemail, $sEmail ." => ".$this->view->messagesubject, $sMyMessage );
					}
					$arrNamesSuccess[] = $sName;
				}
				catch( Exception $e )
				{
					$arrNamesError[] = $sName;
				}
			}
		}

		if ( count( $arrNamesSuccess ) > 0 )
			$this->view->successmessage = "er zijn een uitnodiging verstuurd aan ".implode(", ", $arrNamesSuccess ).".";
		if ( count( $arrNamesError ) > 0 )
			$this->view->errormessage = "er zijn een uitnodiging verstuurd aan ".implode(", ", $arrNamesError ).".";
		if ( count( $arrNamesSuccess ) === 0 and count( $arrNamesError ) === 0 )
			$this->view->errormessage = "er zijn geen uitnodigingen verstuurd.";
	}*/

	public function inlegenwinstAction()
	{
		if ( $this->view->bPoolHasStarted === true )
		{
			$sErrorMessage = urlencode( "je kunt geen inleg & winst beheren, omdat de pool al begonnen is" );
			$this->redirect( $this->view->urlcontroller . "/index/autherrormessage/".$sErrorMessage."/" );
		}

		$this->view->successmessage = urldecode( $this->getParam("successmessage") );
		$this->view->errormessage = urldecode( $this->getParam("errormessage") );

		$this->view->arrMenuItems  = $this->getMenuItems();
		$this->view->arrMenuItems["inleg & winst"]["activeclass"] = true;

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_Pool_User::Admin", "NotEqualTo", true );
		$oOptions->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $this->view->oPool );
		$this->view->bPoolUsersNoAdminJoined = ( VoetbalOog_Pool_User_Factory::getNrOfObjectsFromDatabase( $oOptions ) > 0 );

		if ( strlen ( $this->getParam('btnuitkeringen') ) > 0 )
			$this->handleUitkeringen();
		else if ( strlen ( $this->getParam('btninleg') ) > 0 )
			$this->handleInleg();

		$this->getResponse()->insert("extrajsincludes", $this->_helper->AddIncludes() );
	}


	public function deelnemersAction()
	{
		$this->view->successmessage = urldecode( $this->getParam("successmessage") );
		$this->view->errormessage = urldecode( $this->getParam("errormessage") );

		$this->view->arrMenuItems  = $this->getMenuItems();
		$this->view->arrMenuItems["deelnemers"]["activeclass"] = true;

		if ( strlen ( $this->getParam('btnupdateusers') ) > 0 )
			$this->handleDeelnemers();
		else if ( strlen ( $this->getParam('btnsendreminderemail') ) > 0 )
			$this->handleReminder();
	}

	private function handleDeelnemers()
	{
		$oDbWriter = VoetbalOog_Pool_User_Factory::createDbWriter();

		$oPoolUsersToRemove = VoetbalOog_Pool_User_Factory::createObjects();
		$oPoolUsers = $this->view->oPool->getUsers();
		foreach ( $oPoolUsers as $oPoolUser )
		{
			$oPoolUser->addObserver( $oDbWriter );

			$bPaid = ( $this->getParam( "pooluserpaid-".$oPoolUser->getId() ) !== null );
			$oPoolUser->putPaid( $bPaid );

			$bRemove = ( $this->getParam( "pooluserremove-".$oPoolUser->getId() ) !== null );
			if ( $bRemove and !$oPoolUser->getAdmin() )
				$oPoolUsersToRemove->add( $oPoolUser );
		}

		$oPoolUsers->removeCollection( $oPoolUsersToRemove );
		$oPoolUsersToRemove->addObserver( $oDbWriter );
		$oPoolUsersToRemove->flush();

		try
		{
			if ( $oDbWriter->write() == true )
			{
				$oCache = ZendExt_Cache::getDefaultCache();
				$oCache->remove( 'pool'.$this->view->oPool->getId().'stand' );

				//Controls_Tab::$m_bIncludeFilesIncluded = false;

				$this->view->successmessage = "deelnemers bijgewerkt";
			}
		}
		catch ( Exception $oException )
		{
			$this->view->errormessage = "deelnemers niet bijgewerkt: ".$oException->getMessage();
		}
	}

	private function handleReminder()
	{
		$this->view->errormessage = "";

		$sHeader = filter_var( $this->getParam("reminderemailheader"), FILTER_SANITIZE_STRING );
		$sContent = "Beste pooldeelnemer,<br><br>Je hebt nog niet alle voorspellingen ingevoerd. Binnekort verloopt de deadline. Ga naar <a href=\"".Zend_Registry::get("baseurl")."\">".Zend_Registry::get("baseurl")."</a> om al je voorspellingen in te voeren.<br><br>";
		$sContentPost = filter_var( $this->getParam("reminderemailcontentpost"), FILTER_SANITIZE_STRING );

		$arrUsers = explode( ",", $this->getParam("pooluserstoremind") );
		foreach( $arrUsers as $sUserName )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "VoetbalOog_User::Name", "EqualTo", $sUserName );
			$oOptions->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $this->view->oPool );
			$oPoolUser = VoetbalOog_Pool_User_Factory::createObjectFromDatabase( $oOptions );
			if ( $oPoolUser === null )
				continue;
			try
			{
				if ( APPLICATION_ENV === "production" ) {
					$sEmailAddress = $oPoolUser->getUser()->getEmailAddress();
					RAD_Email::sendHtml( $sEmailAddress, $sHeader, $sContent . $sContentPost );
					$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'auth');
					RAD_Email::sendHtml( $cfgAuth->superadminemail, $sEmailAddress ." => ".$sHeader, $sContent . $sContentPost );
				}
			}
			catch( Exception $e )
			{
				$this->view->errormessage .= "er kon geen herinnering worden verstuurd aan ".$sUserName." : ".$e->getMessage()."<br>";
			}
		}

		if ( strlen( $this->view->errormessage ) === 0 )
			$this->view->successmessage = "de herinneringen zijn verstuurd";
	}



	private function getEmailMessageValues()
	{
		$this->view->messagesubject = $this->getParam("messagesubject");
		if ( $this->view->messagesubject === null )
			$this->view->messagesubject = "uitnodiging voetbalpool ".$this->view->oPool->getCompetitionSeason()->getAbbreviation()." ( ".$this->view->oPool->getName()." )";

		$this->view->messagedescription = $this->getParam("messagedescription");
		if ( $this->view->messagedescription === null )
		{
			$oUCUsers = VoetbalOog_User_Factory::createObjectsFromDatabaseExt( $this->view->oPool, null, "VoetbalOog_Pool" );
			$sUCUserNames = "niemand";
			if ( $oUCUsers->count() > 0 )
				$sUCUserNames = (string) $oUCUsers;

			$this->view->messagedescription =
				"Beste **,".PHP_EOL.
				"".PHP_EOL.
				"Voor het aankomende ".$this->view->oPool->getCompetitionSeason()->getName()." willen we weer een pool gaan doen.".PHP_EOL.
				"Hierbij wil ik je uitnodigen om ook mee te doen.".PHP_EOL.
				"Je kunt de poolvoospellingen invullen op " . Zend_Registry::get("baseurl") . PHP_EOL.
				"".PHP_EOL.
				"Je gebruikersnaam is : **".PHP_EOL.
				"De naam van de pool is : ".$this->view->oPool->getName().PHP_EOL.
				"Let op! Dit is dus niet het wachtwoord waarmee je inlogt!".PHP_EOL.
				"".PHP_EOL.
				"Al aangemeld zijn : ".$sUCUserNames.PHP_EOL.
				"".PHP_EOL.
				"Veel succes en plezier!".PHP_EOL.
				"".PHP_EOL.
				"groet ".$this->view->oUser->getName()
			;
		}
	}

	public function voorspellingenAction()
	{
		$this->view->successmessage = urldecode( $this->getParam("successmessage") );
		$this->view->errormessage = urldecode( $this->getParam("errormessage") );

		$this->view->arrMenuItems  = $this->getMenuItems();
		$this->view->arrMenuItems["voorspellingen"]["activeclass"] = true;

		$this->getResponse()->insert("extrajsincludes", $this->_helper->AddIncludes() );
		// if ( strlen ( $this->getParam('btnuitnodigen') ) > 0 )
		// $this->handleUitnodigen();
	}

	private function handleInleg()
	{
		$oDbWriter = VoetbalOog_Pool_Factory::createDbWriter();

		$this->view->oPool->addObserver( $oDbWriter );

		$this->view->oPool->putStake( (int) $this->getParam( "poolstake" ) );

		try
		{
			if ( $oDbWriter->write() == true )
			{
				$oCache = ZendExt_Cache::getDefaultCache();
				$oCache->remove( 'pool'.$this->view->oPool->getId().'stand' );

				//Controls_Tab::$m_bIncludeFilesIncluded = false;

				$this->view->successmessage = "inleg per deelnemer bijgewerkt";
			}
		}
		catch ( Exception $oException )
		{
			$this->view->errormessage = "onbekende fout: " . $oException->getMessage();
		}
	}

	private function handleUitkeringen()
	{
		$oDbWriter = VoetbalOog_Pool_Payment_Factory::createDbWriter();

		$oPayments = $this->view->oPool->getPayments();

		$oPayments->addObserver( $oDbWriter );
		$oPayments->flush();

		$arrParams = $this->_getAllParams();
		foreach ( $arrParams as $sId => $sValue )
		{
			if ( strpos( $sId, "paymentplace" ) !== false )
			{
				$nPlace = (int) str_replace( "paymentplace", "", $sId );
				$nTimes = (int) $sValue;

				$oPayment = VoetbalOog_Pool_Payment_Factory::createObject();
				$oPayment->putId("__NEW__".$nPlace);
				$oPayment->putPool( $this->view->oPool );
				$oPayment->putPlace( $nPlace );
				$oPayment->putTimesStake( $nTimes );
				$oPayments->add( $oPayment );
			}
		}

		try
		{
			if ( $oDbWriter->write() == true )
			{
				$oCache = ZendExt_Cache::getDefaultCache();
				$oCache->remove( 'pool'.$this->view->oPool->getId().'stand' );

				$this->view->paymentseditmessage = "winstuitkeringen bijgewerkt";
			}
		}
		catch ( Exception $oException )
		{
			$this->view->paymentsediterrormessage = "Onbekende fout: ".$oException->getMessage();
		}
	}

	public function getMenuItems()
	{
		$arrMenuItems = array();
		if ( $this->view->bPoolHasStarted === false )
		{
			$arrMenuItems["uitnodigen"] = array( "activeclass" => false, "action" => "poolbeheer/uitnodigen/", "glyph" => "glyphicon glyphicon-envelope" );
			$arrMenuItems["inleg & winst"] = array( "activeclass" => false, "action" => "poolbeheer/inlegenwinst/", "glyph" => "glyphicon glyphicon-euro" );
		}
		$arrMenuItems["deelnemers"] = array( "activeclass" => false, "action" => "poolbeheer/deelnemers/", "glyph" => "glyphicon glyphicon-user" );
		$arrMenuItems["voorspellingen"] = array( "activeclass" => false, "action" => "poolbeheer/voorspellingen/", "glyph" => "flaticon-voorspellingen" );

		$arrMenuItems["terug"] = array( "activeclass" => false, "action" => "pool/index/", "glyph" => "glyphicon glyphicon-hand-left", "nicename" => " terug" );

		return $arrMenuItems;
	}
}

?>
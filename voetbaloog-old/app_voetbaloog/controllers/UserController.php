<?php

class UserController extends Zend_Controller_Action
{
	protected $m_arrCaptchaQuestions;
	protected $m_arrCaptchaAnswers;

	public function preDispatch()
	{
		$this->m_arrCaptchaQuestions = ["8 + 2","6 + 5","7 + 5","7 + 6","8 + 6","9 + 6"];
		$this->m_arrCaptchaAnswers = [10,11,12,13,14,15];
	}

	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::destroy();

		// \TBS\Auth::getInstance()->clearIdentity();

		$sPath = ( APPLICATION_ENV === "production" ) ? "/" : "/" . APPLICATION_NAME;
		setcookie('rememberme', null, 0, $sPath );

		$this->_helper->viewRenderer->setNoRender();
		$this->redirect( Zend_Registry::get("baseurl")."index/index/");
	}

	public function loginAction()
	{
		$this->view->bHidePopup = true;
		$this->view->poolname = null;
		{
			$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
			if ( $oSession->joinpoolid > 0 and strlen( $oSession->joinkey ) > 0 ) {
				$this->view->bHidePopup = false;
				$oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( $oSession->joinpoolid );
				if ( $oPool !== null )
					$this->view->poolname = $oPool->getName() . " ( " . $oPool->getCompetitionSeason()->getAbbreviation() . " ) ";
			}
		}

		$cfgWeb = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'web');
		$this->view->formurl = $cfgWeb->map.$this->getRequest()->getControllerName()."/".$this->getRequest()->getActionName()."/";

		$this->view->username = strtolower( trim( $this->getParam("username") ) );
		if ( strlen( $this->getParam("btnlogin") ) > 0 and strlen( $this->view->username ) > 0 )
		{
			$this->_helper->viewRenderer->setNoRender();
			$sCustomParams = $this->getHelper("GetUserParams")->direct( true );

			// change email to username
			if ( strpos( $this->view->username, "@" ) !== false ) {
				$oOptions = Construction_Factory::createOptions();
				$oOptions->addFilter( "VoetbalOog_User::EmailAddress", "EqualTo", $this->view->username );
				$oUserTmp = VoetbalOog_User_Factory::createObjectFromDatabase( $oOptions );
				if ( $oUserTmp !== null )
					$this->view->username = $oUserTmp->getName();
			}

			$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $this->view->username );
			$sActivationMessage = $this->checkUserOnActive( $oUser );

			$arrDbLoginOptions = $this->getDbLoginOptions( $oUser );

			$sBaseUrl = $this->_helper->login( APPLICATION_NAME, false, $sCustomParams, $arrDbLoginOptions, $sActivationMessage );

			$this->redirect( $sBaseUrl );
		}
		else
		{
			$this->view->arrCustomParams = $this->getHelper("GetUserParams")->direct();
			$this->view->loginmessage = urldecode( $this->getParam('loginmessage') );
		}
	}

	protected function getDbLoginOptions( $oUser )
	{
		$arrDbLoginOptions = array();

		$arrDbLoginOptions["username"] = $this->view->username;
		if ( $oUser !== null )
		{
			$arrDbLoginOptions["username"] = $oUser->getName();
			$arrDbLoginOptions["hashtype"] = $oUser->getHashType();
			$arrDbLoginOptions["salted"] = $oUser->getSalted();
		}

		return $arrDbLoginOptions;
	}

	protected function checkUserOnActive( $oUser )
	{
		$sMessage = null;

		if ( $oUser !== null and $oUser->getActivationKey() !== null )
			$sMessage = "Je account is nog niet geactiveerd, activeer je account door op de link in je email, die naar ".$oUser->getEmailAddress()." is verzonden, te klikken. Kijk eventueel bij de ongewenste email.";

		return $sMessage;
	}

	protected function checkUserName( $sUserName )
	{
		if( strlen( $sUserName ) < 3 )
			return "je gebruikersnaam moet minimaal 3 karakters bevatten";
		else if( !ctype_alnum( $sUserName ) )
			return "je gebruikersnaam(".$sUserName.") bevat andere karakters dan a-Z of 0-9";
		else if( strlen( $sUserName ) > VoetbalOog_User::MAX_LENGTH_NAME )
			return "je gebruikersnaam(".$sUserName.") mag niet meer dan ".VoetbalOog_User::MAX_LENGTH_NAME." karakters bevatten";

		return true;
	}

	public function registerAction()
	{
		$sPassword = trim( $this->getParam('password') );
		$sRepeatPassword = trim( $this->getParam('repeatpassword') );
		$this->view->emailaddress = trim( $this->getParam('emailaddress') );

		$this->view->messageemail = array();
		$this->view->messageuser = array();
		$this->view->messagepassword = array();
		$this->view->messagegender = array();
		$this->view->messagecaptcha = array();

		$this->view->captchaindex = random_int(0, count($this->m_arrCaptchaQuestions)-1 );
		$this->view->captchadescription = $this->m_arrCaptchaQuestions[$this->view->captchaindex];

		$sPreMessage = urldecode( $this->getParam('premessage') );
		if ( strlen( $sPreMessage ) > 0 )
		{
			$this->view->editmessage = array( $sPreMessage );
			return;
		}

		$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini', 'auth');
		if ( strlen( $cfgAuth->salt ) === 0 )
		{
			$this->view->editmessage = array( "geen zout gezet." );
			return;
		}

		if ( strlen( $this->getParam('btnsubmit') ) === 0 )
			return;

		$this->view->username = strtolower( trim( $this->getParam('username') ) );

		$vtRetVal = $this->checkUserName( $this->view->username );
		if ( $vtRetVal !== true )
		{
			$this->view->editmessage = array( $vtRetVal );
			return;
		}

		$this->view->editmessage = $this->inputCheckRegister( $sPassword, $sRepeatPassword );

		if ( $this->view->editmessage !== null and count( $this->view->editmessage ) > 0 )
			return;

		$this->captchadescription = $this->m_arrCaptchaAnswers[random_int(0, count($this->m_arrCaptchaAnswers)-1)];

		$oUser = VoetbalOog_User_Factory::createObject();
		$oUser->putId( "__NEW__" );
		$oUser->putName( $this->view->username );
		$oUser->putPassword( hash( $cfgAuth->hashtype, $cfgAuth->salt . $sPassword ) );
		$oUser->putHashType( $cfgAuth->hashtype );
		$oUser->putSalted( true );
		$sActivationKey = hash( $cfgAuth->hashtype, APPLICATION_NAME . Agenda_Factory::createDateTime() );
		$oUser->putActivationKey( $sActivationKey );
		$oUser->putLatestLoginDateTime( Agenda_Factory::createDateTime() );
		$oUser->putEmailAddress( $this->view->emailaddress );
		$oUser->putSystem( false );
		$oDateOfBirth = null;
		{
			$sDateOfBirth = $this->getParam('dateofbirth');
			if ( strlen ( $sDateOfBirth ) > 0 )
				$oDateOfBirth = Agenda_Factory::createDate( $sDateOfBirth );
		}
		$oUser->putDateOfBirth( $oDateOfBirth );

		$oDbWriter = VoetbalOog_User_Factory::createDbWriter();

		$oUsers = VoetbalOog_User_Factory::createObjects();
		$oUsers->addObserver( $oDbWriter );

		$oUsers->add( $oUser );

		try
		{
			if ( $oDbWriter->write() === true )
			{
				//write a role to a user
				$oRoles = RAD_Auth_Role_Factory::createObjectsFromDatabase();

				$oUserRoleDbWriter = RAD_Auth_Role_Factory::createUserDbWriter( $oUser );
				$oUserRoles = RAD_Auth_Role_Factory::createObjects();
				$oUserRoles->addObserver( $oUserRoleDbWriter );

				$oUserRoles->add( $oRoles[ APPLICATION_NAME . "_standarduser"] );

				if ( $oUserRoleDbWriter->write() === true )
				{
					$sMessage =
							"<div style=\"color:#135113; font-size:20px;\">VoetbalOog</div>"."<br>".
							"<br>".
							"Hallo ".$oUser->getName().","."<br>".
							"<br>".
							"Bedankt voor het registreren bij <span style=\"color:#135113;\">VoetbalOog</span>. Hierbij ontvang je je gebruikersgegevens: <br>".
							"<br>".
							"website : ".Zend_Registry::get('baseurl')."<br>".
							"gebruikersnaam : ".$oUser->getName()."<br>".
							"<br>".
							"<span style=\"font-weight:bold;\">Klik <a href=\"".Zend_Registry::get('baseurl')."user/activate/id/".$oUser->getId()."/key/".$sActivationKey."/\">hier</a> om uw account te activeren.</span><br>".
							"<br>".
							"Wanneer je je account niet binnen een maand activeert, wordt je account automatisch verwijderd.<br>".
							"<br>".
							"groeten van <span style=\"color:#135113;\">VoetbalOog</span>";

					if ( APPLICATION_ENV === "production" )
					{
						RAD_Email::sendHtml( $this->view->emailaddress, "VoetbalOog registratiegegevens", $sMessage );
						RAD_Email::sendHtml( $cfgAuth->superadminemail, $this->view->emailaddress ." => VoetbalOog registratiegegevens", $sMessage );
					}
					else
					{
						echo $sMessage;
					}
					$this->view->succescode = true;
				}
			}
		}
		catch( Exception $e )
		{
			$this->view->editmessage[] = "je bent niet toegevoegd als gebruiker: ".$e->getMessage();
		}
	}

	protected function inputCheckRegister( $sPassword, $sRepeatPassword )
	{
		$this->inputCheckEmail( false );

		{
			if ( strlen( $this->view->username ) === 0 )
				$this->view->messageuser[] = "gebruikersnaam is niet ingevuld";
			else
			{
				if ( strlen( $this->view->username ) > VoetbalOog_User::MAX_LENGTH_NAME )
					$this->view->messageuser[] = "gebruikersnaam is te lang(max " .VoetbalOog_User::MAX_LENGTH_NAME." karakters)";

				$oOptions = Construction_Factory::createOptions();
				$oOptions->addFilter( "VoetbalOog_User::Name", "EqualTo", $this->view->username );
				$oUsers = VoetbalOog_User_Factory::createObjectsFromDatabase( $oOptions );
				if ( $oUsers->count() > 0 )
					$this->view->messageuser[] = "gebruikersnaam ".$this->view->username." is al in gebruik";

				if ( preg_match("/^[A-Za-z0-9]+$/", $this->view->username ) !== 1 )
					$this->view->messageuser[] = "je kunt alleen alphanumerieke karakters gebruiken in je gebruikersnaam";

				$captchaAnswer = null;
				if( $this->getParam("captchaindex") !== null &&
					is_numeric($this->getParam("captchaindex")) &&
					array_key_exists($this->getParam("captchaindex"), $this->m_arrCaptchaAnswers )
				) {
					$captchaAnswer = $this->m_arrCaptchaAnswers[(int)$this->getParam("captchaindex")];
				}

				if( $captchaAnswer !== ((int)$this->getParam("captchainput")) ) {
					$this->view->messagecaptcha[] = "de som van de twee getallen is onjuist";
				}
			}
		}

		$this->inputCheckPassword( $sPassword, $sRepeatPassword );

		$this->inputCheckGender();

		return ( array_merge( $this->view->messageemail, $this->view->messageuser, $this->view->messagepassword, $this->view->messagegender, $this->view->messagecaptcha ) );
	}

	protected function inputCheckHome( $sPassword, $sRepeatPassword )
	{
		$this->inputCheckEmail( true );

		if ( strlen( $sPassword ) > 0 or strlen( $sRepeatPassword ) > 0 )
			$this->inputCheckPassword( $sPassword, $sRepeatPassword );

		$this->inputCheckGender();

		return ( array_merge( $this->view->messageemail, $this->view->messagepassword, $this->view->messagegender ) );
	}

	protected function inputCheckEmail( $bExistingUser )
	{
		if ( strlen( $this->view->emailaddress ) === 0 )
			$this->view->messageemail[] = "emailadres is niet ingevuld";
		else
		{
			if ( $bExistingUser === false and APPLICATION_ENV === "production" )
			{
				$oOptions = Construction_Factory::createOptions();
				$oOptions->addFilter( "VoetbalOog_User::EmailAddress", "EqualTo", $this->view->emailaddress );
				$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $oOptions );
				if ( $oUser !== null )
					$this->view->messageemail[] = "emailadres ".$this->view->emailaddress." is al in gebruik. bent je je account-gegevens kwijt, klik dan <a href=\"".Zend_Registry::get("baseurl")."user/forgetpassword/emailaddress/".urlencode( $this->view->emailaddress )."/\">hier</a>";
			}
		}
	}

	protected function inputCheckPassword( $sPassword, $sRepeatPassword )
	{
		if ( strlen( $sPassword ) === 0 or $sPassword !== $sRepeatPassword )
			$this->view->messagepassword[] = "wachtwoord is niet gelijk of niet ingevuld";
		else if ( strlen( $sPassword ) < 3 )
			$this->view->messagepassword[] = "je wachtwoord dient uit minimaal 3 karakters te bestaan";
	}

	protected function inputCheckGender()
	{
		if ( $this->view->malechecked !== null and $this->view->femalechecked !== null )
			$this->view->messagegender[] = "kies maximaal 1 geslacht";
	}

	public function activateAction()
	{
		$nUserId = (int) $this->getParam('id');

		$oUser = null;
		if ( $nUserId > 0 )
			$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $nUserId );

		if ( $oUser === null )
		{
			$this->view->errormessage = "
					Je gebruikersnaam kon niet worden gevonden.<br>
					Waarschijnlijk is het langer als een maand geleden dat je je account hebt aangemaakt<br>
					en is je account automatisch verwijderd.<br>
					Je kunt opnieuw een account aanmaken door <a href=\"".Zend_Registry::get("baseurl")."user/register/\">hier</a> te klikken.
				";
		}
		else
		{
			$sActivationKey = $this->getParam('key');
			if ( $oUser->getActivationKey() === null )
			{
				$this->view->errormessage = "Je account is al geactiveerd. <a href=\"".Zend_Registry::get("baseurl")."user/login/\" type=\"button\" class=\"btn btn-default\"><span class=\"glyphicon glyphicon-log-in\"></span> Log in</a> om mee te doen met een pool.</div>";
			}
			else if ( $oUser->getActivationKey() !== $sActivationKey )
			{
				$this->view->errormessage = "Je activatiecode komt niet overeen met de activatiecode in ons systeem. Heb je misschien de activatiecode gewijzigd?";
			}
		}

		if ( $this->view->errormessage === null )
		{
			$oDbWriter = VoetbalOog_User_Factory::createDbWriter();
			$oUser->addObserver( $oDbWriter );

			$oUser->putLatestLoginDateTime( null );
			$oUser->putActivationKey( null );

			try
			{
				$oDbWriter->write();
				$sAction = $this->_helper->SyncUserWithDb()->execute( $oUser->getName(), 'index/index/' );
				$this->redirect( Zend_Registry::get("baseurl") . $sAction . "mainsuccessmessage/".urlencode( "je account is geactiveerd" )."/" );

				die();
			}
			catch( Exception $e )
			{
				$this->view->errormessage = "je account is niet geactiveerd: ".$e->getMessage();
			}
		}
	}

	public function forgetpasswordlinkAction()
	{
		if ( strlen( $this->getParam("btnsubmit") ) === 0 )
			return;

		$sUserName = strtolower( trim( $this->getParam('username') ) );
		if ( strlen ( $sUserName ) === 0 ) {
			$this->view->errormessage = "je hebt geen emailadres of gebruikersnaam ingevuld";
        }

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_User::EmailAddress", "EqualTo", $sUserName );
		$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $oOptions );

		if ( $oUser === null )
			$this->view->errormessage = "voor emailadres ".$sUserName." kan geen account worden gevonden";

		if ( $this->view->errormessage !== null )
			return;

		$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini', 'auth');
		$oTomorrow = Agenda_Factory::createDate(); $oTomorrow->modify("+1 days");
		$sHash = hash( "sha256", $oUser->getId() . $cfgAuth->salt . $oTomorrow->toString( Agenda_DateTime::STR_SQLDATE ) );
		$sUrl = Zend_Registry::get("baseurl") . "user/forgetpassword/userid/".$oUser->getId()."/hash/".$sHash."/";

		$sMessage = "Hallo ".$oUser->getName().",<br><br>".
		"Met onderstaande link kun je een nieuw wachtwoord instellen. De link is geldig tot en met ".$oTomorrow->toString( "l j F" ).".<br><br>".
		"<a href=\"".$sUrl."\">nieuw wachtwoord instellen</a><br><br>".
		"groeten van voetbaloog!";

		if ( APPLICATION_ENV === "production" )
		{
			RAD_Email::sendHtml( $oUser->getEmailAddress(), "link voor nieuw wachtwoord", $sMessage );
			RAD_Email::sendHtml( $cfgAuth->superadminemail, $oUser->getEmailAddress() ." => VoetbalOog nieuw wachtwoord", $sMessage );
		}
		else
		{
			echo $sMessage;
		}

		$this->view->successmessage = "Je ontvangt een email over enkele seconden. Hierin staat een link waarmee je een nieuw wachtwoord kunt instellen. Deze link is tot en met morgen geldig.";
		$this->view->errormessage = "Let op : De email kan ook in de spam-folder van je email-programma zijn terecht gekomen.";
	}

	public function forgetpasswordAction()
	{
		$nUserId = (int) $this->getParam('userid');
		if ( $nUserId > 0 )
			$this->view->oInputUser = VoetbalOog_User_Factory::createObjectFromDatabase( $nUserId );

		$this->view->hash = $this->getParam('hash');

		if ( strlen( $this->getParam('btnsubmit') ) === 0 )
			return;

		if ( $this->view->oInputUser === null )
		{
			$this->view->errormessage = "de gebruiker kan niet gevonden worden";
			return;
		}

		$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini', 'auth');
		$oDate = Agenda_Factory::createDate();
		$sHashToday = hash( "sha256", $this->view->oInputUser->getId() . $cfgAuth->salt . $oDate->toString( Agenda_DateTime::STR_SQLDATE ) );
		$oDate->modify("+1 days");
		$sHashTomorrow = hash( "sha256", $this->view->oInputUser->getId() . $cfgAuth->salt . $oDate->toString( Agenda_DateTime::STR_SQLDATE ) );
		if ( $this->view->hash !== $sHashToday and $this->view->hash !== $sHashTomorrow ) {
			$this->view->errormessage = "de aanvraag is verlopen, vraag <a href=\"".Zend_Registry::get("baseurl")."user/forgetpasswordlink/\">hier</a> opnieuw je nieuwe wachtwoord aan";
			return;
		}

		$this->view->messagepassword = array();
		$sPassword = $this->getParam("password");
		$this->inputCheckPassword( $sPassword, $this->getParam("repeatpassword") );
		if ( count( $this->view->messagepassword ) > 0 ) {
			$this->view->errormessage = $this->view->messagepassword[0];
			return;
		}

		try
		{
			$oDbWriter = VoetbalOog_User_Factory::createDbWriter();
			$this->view->oInputUser->addObserver( $oDbWriter );

			$this->view->oInputUser->putPassword( hash( $this->view->oInputUser->getHashType(), $cfgAuth->salt . $sPassword ) );
			$this->view->oInputUser->putSalted( true );

			$oDbWriter->write();

			$this->view->successmessage = true;
		}
		catch( Exception $e )
		{
			$this->view->errormessage = "je wachtwoord kon niet worden aangepast : " . $e->getMessage();
		}
	}

	public function homeAction()
	{
		$this->view->emailaddress = $this->view->oUser->getEmailAddress();
		$this->view->username = $this->view->oUser->getName();

		$this->view->malechecked = $this->view->oUser->getGender() === "m" ? "checked" : null;
		$this->view->femalechecked = $this->view->oUser->getGender() === "v" ? "checked" : null;

		// callback facebook
		$this->view->savemessage = array();
		$sMessage = $this->getParam('message');
		if ( strlen( $sMessage ) > 0 )
			$this->view->savemessage[] = $sMessage;

		if ( strlen ( $this->getParam('btnchange') ) === 0 )
			return;

		$this->view->messageemail = array();
		$this->view->messagepassword = array();
		$this->view->messagegender = array();

		$this->view->nMaxImageWidth = 150;
		$this->view->nMaxImageHeight = 200;

		$sPassword = trim( $this->getParam('password') );
		$sRepeatPassword = trim( $this->getParam('repeatpassword') );
		$this->view->emailaddress = strtolower( trim( $this->getParam('emailaddress') ) );
		$this->view->username = strtolower( trim( $this->getParam('username') ) );
		$this->view->malechecked = $this->getParam('genderm') === "m" ? "checked" : null;
		$this->view->femalechecked = $this->getParam('genderf') === "v" ? "checked" : null;

		$this->view->editmessage = $this->inputCheckHome( $sPassword, $sRepeatPassword );

		$vtRetVal = $this->checkUserName( $this->view->username );
		if ( $vtRetVal !== true )
			$this->view->editmessage = array( $vtRetVal );

		if ( $this->view->editmessage !== null and count( $this->view->editmessage ) > 0 )
			return;

		// create writer
		$oUserWriter = VoetbalOog_User_Factory::createDbWriter();

		$this->view->oUser->addObserver( $oUserWriter );

		if ( $this->view->malechecked !== null )
			$this->view->oUser->putGender( "m" );
		elseif ( $this->view->femalechecked !== null )
			$this->view->oUser->putGender( "v" );
		else
			$this->view->oUser->putGender( null );

		$this->view->oUser->putEmailaddress( $this->view->emailaddress );
		$this->view->oUser->putName( $this->view->username );

		$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini', 'auth');
		$this->view->oUser->putHashType( $cfgAuth->hashtype );
		if( strlen( $sPassword ) > 0 )
			$this->view->oUser->putPassword( hash( $this->view->oUser->getHashType(), $cfgAuth->salt . $sPassword ) );
		$this->view->oUser->putSalted( true );

		if ( count( $this->view->editmessage ) === 0 )
		{
			$bRemoveAvatar = ( $this->getParam("removeavatar") === "on" );
			$this->saveImage( $bRemoveAvatar );
		}

		if ( count( $this->view->editmessage ) === 0 )
		{
			try
			{
				$oUserWriter->write();
				$this->view->savemessage[] = "veranderingen opgeslagen";
				$this->cleanCachePools( $this->view->oUser );
			}
			catch( Exception $e )
			{
				$this->view->editmessage[] = "het wegschrijven is niet gelukt: " . $e->getMessage();
			}
		}

		$this->view->oUser->flushObservers();
	}

	protected function cleanCachePools( $oUser )
	{
		$oCache = ZendExt_Cache::getDefaultCache();
		$oPoolUsers = $oUser->getPoolUsers();
		foreach( $oPoolUsers as $oPoolUser )
			$oCache->clean( Zend_Cache::CLEANING_MODE_MATCHING_TAG,	array( 'pool'.$oPoolUser->getPool()->getId() ) );
	}

	protected function saveImage( $bRemoveAvatar )
	{
		$arrOptions = array(
			"max_image_width" => 150,
			"max_image_height" => 200,
			"min_aspect_ratio" => 0.6,
			"max_aspect_ratio" => 1
		);
		$vtPicture = $this->_helper->GetImage( "avatar", $arrOptions );

		if ( is_bool( $vtPicture ) and $vtPicture === false )
		{
			return;
		}
		elseif ( $vtPicture[0] === false )
		{
			$this->view->editmessage[] = $vtPicture[1];
		}
		else
		{
			$this->view->oUser->putPicture( $vtPicture[1] );
		}

		if ( $bRemoveAvatar === true )
			$this->view->oUser->putPicture( null );
	}

	public function feedbackAction()
	{

	}

	public function facebookloginAction()
	{
		$this->loginThirdParty( "facebook", $this->getParam('code') );
	}

	public function googleloginAction()
	{
		$this->loginThirdParty( "google", $this->getParam('code') );
	}

	/*
	object(TBS\Auth\Identity\Twitter)#266 (3)
	{
		["_api":protected]=> object(TBS\Resource\Twitter)#257 (3)
		{
			["_accessToken":protected]=> object(Zend_Oauth_Token_Access)#255 (3)
			{
				["_params":protected]=> array(4)
				{
					["oauth_token"]=> string(50) "?"
					["oauth_token_secret"]=> string(45) "?"
					["user_id"]=> string(9) "?"
					["screen_name"]=> string(11) "?"
				}
				["_response":protected]=> NULL
				["_httpUtility":protected]=> object(Zend_Oauth_Http_Utility)#254 (0) { }
			}
			["_options":protected]=> array(5)
			{
				["consumerKey"]=> string(22) "?"
				["consumerSecret"]=> string(39) "?"
				["callbackUrl"]=> string(43) "http://www.voetbaloog.nl/user/twitterlogin/"
				["siteUrl"]=> string(29) "https://api.twitter.com/oauth"
				["authorizeUrl"]=> string(39) "https://api.twitter.com/oauth/authorize"
			}
			["data":protected]=> array(1) { ["profile"]=> string(0) "" }
		}
		["_id":protected]=> NULL
		["_name":protected]=> string(7) "twitter" }
	}
	*/
	public function twitterloginAction()
	{
		$this->loginThirdParty( "twitter", $this->getParam('oauth_token') );
	}

	/**
	 * id, email, gender, username, picture
	 *
	 * @param unknown_type $oAuth
	 * @param unknown_type $sProvider "google", "facebook"
	 */
	protected function getProviderProperties( $oAuth, $sProvider )
	{
		$arrProps = array();

		$oProvider = $oAuth->getIdentity()->get( $sProvider );

		if ( $oProvider === false )
			throw new Exception( "eigenschappen van ".$sProvider."-account konden niet worden opgevraagd", E_ERROR );

		$arrProfile = $oProvider->getApi()->getProfile();

		if ( array_key_exists( "error", $arrProfile ) and is_array( $arrProfile["error"]->errors ) )
			return $arrProfile["error"]->errors[0]->message;

		$arrProps[ "id" ] = $arrProfile["id"];
		$arrProps[ "email" ] = $arrProfile["email"];
		if ( array_key_exists( "gender", $arrProfile ) )
			$arrProps[ "gender" ] = substr( $arrProfile["gender"], 0, 1 );

		if ( $sProvider === "google" ) {
			$sUserName = "";
			if ( array_key_exists( "link", $arrProfile ) )
				$sUserName = substr( $arrProfile["link"], strrpos( $arrProfile["link"], "+" ) + 1 );
			$arrProps[ "username" ] = $sUserName;
		}
		else
			$arrProps[ "username" ] = $arrProfile["username"];
		$arrProps[ "username" ] = strtolower( $arrProps[ "username" ] );

		if ( $sProvider === "google" )
			$arrProps[ "picture" ] = $arrProfile["picture"];
		else
			$arrProps["picture"] = $oProvider->getApi()->getPicture();

		return $arrProps;
	}

	protected function getAdapter( $sProvider, $sCode )
	{
		if ( $sProvider === "facebook" )
			return new TBS\Auth\Adapter\Facebook( $sCode );
		else if ( $sProvider === "google" )
			return new TBS\Auth\Adapter\Google( $sCode );
		else if ( $sProvider === "twitter" )
			return new TBS\Auth\Adapter\Twitter( $_GET );
		throw new Exception( "Unknown adapter!", E_ERROR );
	}

	protected function loginThirdParty( $sProvider, $vtCode )
	{
		$sErrorMessage = null;

		$oAuth = TBS\Auth::getInstance();

		$result = null;
		if ( ( is_string( $vtCode ) and strlen( $vtCode ) > 0 )
			or ( is_array( $vtCode ) and count( $vtCode ) > 0 )
		)
		{
			$adapter = $this->getAdapter( $sProvider, $vtCode );
			$result = $oAuth->authenticate($adapter);
		}

		if ( $result === null or $result->isValid() !== true )
			$sErrorMessage = "je kon niet geautoriseerd worden via je ".$sProvider."-account";

		if ( $sErrorMessage !== null )
			$this->redirect( Zend_Registry::get("baseurl") .  "user/login/loginmessage/".urlencode( $sErrorMessage )."/" );

		$arrProviderProperties = $this->getProviderProperties( $oAuth, $sProvider );
		if ( is_string( $arrProviderProperties ) )
			$this-redirect( Zend_Registry::get("baseurl") . "user/login/loginmessage/".urlencode( $arrProviderProperties )."/" );

		$sIdProperty = $sProvider === "google" ? "GoogleId" : ( $sProvider === "facebook" ? "FacebookId" : "TwitterId" );
		$bUpdateIdProperty = false;
		$oUser = null;
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "VoetbalOog_User::" . $sIdProperty, "EqualTo", $arrProviderProperties["id"] );
			$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $oOptions );
			if ( $oUser === null )
			{
				$oOptions = Construction_Factory::createOptions();
				$oOptions->addFilter( "VoetbalOog_User::EmailAddress", "EqualTo", $arrProviderProperties["email"] );
				$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $oOptions );
				$bUpdateIdProperty = true;
			}
		}

		$sUserName = null;
		if ( $oUser === null ) // check username
		{
			// begin name new user
			$sUserName = strtolower( trim( $arrProviderProperties["username"] ) );
			$sUserName = str_replace( array("-", "_"), '', $sUserName );
			if ( strlen( $sUserName ) > 15 )
				$sUserName = substr( $sUserName, 0, 15 );

			$vtRetVal = $this->checkUserName( $sUserName );
			if ( $vtRetVal !== true and $sProvider === "google" )
			{
				$sUserName = trim( $arrProviderProperties["email"] );
				$sUserName = strtolower( substr( $sUserName, 0, strpos( $sUserName, "@" ) ) );
				$sUserName = str_replace( array("-", "_"), '', $sUserName );
				if ( strlen( $sUserName ) > 15 )
					$sUserName = substr( $sUserName, 0, 15 );
			}
			// end name new user

			$vtRetVal = $this->checkUserName( $sUserName );
			if ( $vtRetVal !== true )
				$this->redirect( Zend_Registry::get("baseurl") . "user/login/loginmessage/".urlencode( $vtRetVal )."/" );

			$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $sUserName );
			if ( $oUser !== null )
				$this->redirect( Zend_Registry::get("baseurl") . "user/login/loginmessage/".urlencode( "je gebruikersnaam ".$sUserName." wordt al gebruikt door iemand met een ander emailadres" ) );
		}

		$sAction = null;

		if ( $oUser === null ) // create account
		{
			$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini', 'auth');

			$oUser = VoetbalOog_User_Factory::createObject();
			$oUser->putId( $sUserName );
			$oUser->putName( $sUserName );
			$oUser->putHashType( $cfgAuth->hashtype );
			$oUser->putSalted( true );
			if ( array_key_exists( "gender", $arrProviderProperties ) )
				$oUser->putGender( $arrProviderProperties[ "gender" ] );
			$oUser->putActivationKey( null );
			$oUser->putEmailAddress( $arrProviderProperties[ "email" ] );
			$oUser->putSystem( false );
			MetaData_Factory::putValue( $oUser, "VoetbalOog_User::".$sIdProperty, $arrProviderProperties["id"] );

			$oUser->putPicture( file_get_contents( $arrProviderProperties[ "picture" ] ) );

			$oDbWriter = VoetbalOog_User_Factory::createDbWriter();
			$oUsers = VoetbalOog_User_Factory::createObjects();
			$oUsers->addObserver( $oDbWriter );
			$oUsers->add( $oUser );

			try
			{
				if ( $oDbWriter->write() === true )
				{
					$oRoles = RAD_Auth_Role_Factory::createObjectsFromDatabase();

					$oUserRoleDbWriter = RAD_Auth_Role_Factory::createUserDbWriter( $oUser );
					$oUserRoles = RAD_Auth_Role_Factory::createObjects();
					$oUserRoles->addObserver( $oUserRoleDbWriter );
					$oUserRoles->add( $oRoles[ APPLICATION_NAME . "_standarduser"] );

					$oUserRoleDbWriter->write();
				}
			}
			catch( Exception $e )
			{
				$this->redirect( Zend_Registry::get("baseurl") . "user/login/loginmessage/".urlencode( "je bent niet toegevoegd als gebruiker: ".$e->getMessage() )."/" );
				die();
			}
		}
		else // update account
		{
			$bUndoActivation = ( $this->checkUserOnActive( $oUser ) !== null );

			$oDbWriter = VoetbalOog_User_Factory::createDbWriter();
			$oUser->addObserver( $oDbWriter );
			if ( $bUndoActivation )
				$oUser->putActivationKey( null );
			if ( $bUpdateIdProperty )
				MetaData_Factory::putValue( $oUser, "VoetbalOog_User::".$sIdProperty, $arrProviderProperties["id"] );

			try { $oDbWriter->write(); } catch( Exception $e ) { }
			$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
			$sAction = $oSession->previouscontroller . "/" . $oSession->previousaction . "/";
            if ( $oSession->previouspoolid !== null ) {
                $sAction .= "poolid/".$oSession->previouspoolid."/";
            }
            $oSession->previouscontroller = null;
            $oSession->previousaction = null;
            $oSession->previouspoolid = null;
		}

		// let user login
		$sAction = $this->_helper->SyncUserWithDb()->execute( $oUser->getName(), $sAction );
		$this->redirect( Zend_Registry::get("baseurl") . $sAction );
	}

	public function facebookredirectAction()
	{
		die("inloggen met facebook wordt niet meer ondersteund, klik <a href=".Zend_Registry::get("baseurl").">hier</a> om weer naar de home-pagina te gaan");
		// $this->generalRedirect( TBS\Auth\Adapter\Facebook::getAuthorizationUrl() );
	}

	public function googleredirectAction()
	{
		$this->generalRedirect( TBS\Auth\Adapter\Google::getAuthorizationUrl() );
	}

	public function twitterredirectAction()
	{
		die("inloggen met twitter wordt niet meer ondersteund, klik <a href=".Zend_Registry::get("baseurl").">hier</a> om weer naar de home-pagina te gaan");
		// $this->generalRedirect( TBS\Auth\Adapter\Twitter::getAuthorizationUrl() );
	}

	public function generalRedirect( $sUrl )
	{
		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );

		$oSession->previouscontroller = $this->getParam('previouscontroller');
		if ( strlen( $oSession->previouscontroller ) === 0 )
			$oSession->previouscontroller = "index";
		$oSession->previousaction = $this->getParam('previousaction');
		if ( strlen( $oSession->previousaction ) === 0 )
			$oSession->previousaction = "index";
        $oSession->previouspoolid = $this->getParam('poolid');

		$this->redirect( $sUrl );
	}

	public function extendsessionAction()
	{
		$response = $this->getResponse()->clearBody();
		$this->_helper->viewRenderer->setNoRender();
		echo "session extended( ".microtime()." )";
		die();
	}

	public function simulateAction()
	{
		$this->_helper->viewRenderer->setNoRender();

		$this->_helper->simuleer();
	}

	public function authAction()
	{
		$this->view->errormessage = urldecode( $this->getParam("errormessage") );
	}
}

?>

<?php

/**
 * Action Helper for loading forms
 *
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Apps_Helper_Login extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Constructor: initialize plugin loader
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	public function direct( $szAppName, $bBackToIndex = false, $szCustomParams, $arrDbLoginOptions = array(), $sMessage = null )
	{
		$objActionController = $this->getActionController();

		$szLoginAction = $objActionController->getRequest()->getControllerName()."/".$objActionController->getRequest()->getActionName()."/";

		$szAction = $this->getAction( $bBackToIndex );
		if ( strlen ( $szAction ) > 0 and $bBackToIndex === false )
			$szAction .= $szCustomParams;

		$sUserName = $arrDbLoginOptions['username'];

		$szPassword = $this->getRequest()->getParam('password');
		if ( strlen( $sUserName ) === 0 )
		{
			$szAction = $szLoginAction.$szCustomParams."loginmessage/".urlencode("gebruikersnaam is leeg")."/";
		}
		elseif ( strlen( $szPassword ) === 0 )
		{
			$szAction = $szLoginAction.$szCustomParams."loginmessage/".urlencode("wachtwoord is leeg")."/";
		}
		elseif ( strlen( $sMessage ) > 0 )
		{
			$szAction = $szLoginAction.$szCustomParams."loginmessage/".urlencode( $sMessage )."/";
		}
		else
		{
			try
			{
				$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH.'/configs/config.ini', 'auth');

				if ( array_key_exists( "hashtype", $arrDbLoginOptions ) === false )
					$arrDbLoginOptions["hashtype"] = $cfgAuth->hashtype;
				RAD_Auth_Login::putDbHashType( $arrDbLoginOptions["hashtype"] );

				if ( array_key_exists( "salted", $arrDbLoginOptions ) === true and $arrDbLoginOptions["salted"] === true )
					RAD_Auth_Login::putDbSalt( $cfgAuth->salt );

				RAD_Auth_Login::login( $sUserName, $szPassword, $szAppName );

				$oSyncUserWithDb = Zend_Controller_Action_HelperBroker::getStaticHelper('SyncUserWithDb');
				$szAction = $oSyncUserWithDb->execute( $sUserName, $szAction );
				// var_dump($szAction); die();
			}
			catch ( Exception $objException )
			{
				$szAction = $szLoginAction.$szCustomParams."loginmessage/".urlencode( $objException->getMessage() )."/";
			}
		}

		return Zend_Registry::get('baseurl').$szAction;
	}

	protected function getAction( $bBackToIndex )
	{
		$szPreviousModule = $this->getRequest()->getParam('previousmodule');
		$szPreviousController = $this->getRequest()->getParam('previouscontroller');
		$szPreviousAction = $this->getRequest()->getParam('previousaction');

		$szAction = "";
		if ( strlen ( $szPreviousModule ) > 0 )
			$szAction .= $szPreviousModule."/";
		if ( strlen ( $szPreviousController ) > 0 )
			$szAction .= $szPreviousController."/";
		if ( strlen ( $szPreviousAction ) > 0 )
			$szAction .= $szPreviousAction."/";

		if ( strlen ( $szAction ) === 0 and $bBackToIndex === true )
			$szAction = "index/index/";

		return $szAction;
	}
}

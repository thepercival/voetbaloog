<?php

/**
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class VoetbalOog_Helper_SyncUserWithDb extends Zend_Controller_Action_Helper_Abstract
{
	public function direct()
	{
		return $this;
	}

	public function execute( $sUserName, $sPreviousAction )
	{
		$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $sUserName );

		$oRoles = RAD_Auth_Role_Factory::createObjectsFromDatabase();

		$oUserDbWriter = VoetbalOog_User_Factory::createDbWriter();

		$cfgAuth = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini', 'auth');

		if ( $oUser !== null )
		{
			$oUser->addObserver( $oUserDbWriter );

			if ( strtolower( $oUser->getHashType() ) !== strtolower( $cfgAuth->hashtype ) or $oUser->getSalted() !== true )
			{
				$oUser->putPassword( hash( $cfgAuth->hashtype, $cfgAuth->salt . $this->getRequest()->getParam("password") ) );
				$oUser->putHashType( $cfgAuth->hashtype );
				$oUser->putSalted( true );
			}

			$oUser->putLatestLoginDateTime( Agenda_Factory::createDateTime() );
			$oUser->putLatestLoginIpAddress( $_SERVER['REMOTE_ADDR'] );

			if ( strlen( $oUser->getCookieSessionToken() ) === 0 )
			{
				$sToken = $this->buildToken();
				if ( strlen( $sToken ) > 127 )
					$oUser->putCookieSessionToken( $sToken );
			}

			$oUserDbWriter->write();
		}

		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
		$oSession->userid = $oUser->getId();

		if ( $this->getRequest()->getParam( "rememberme" ) === "on" )
		{
			$sPath = ( APPLICATION_ENV === "production" ) ? "/" : "/" . APPLICATION_NAME;
			setcookie('rememberme', $oUser->getCookieSessionToken(), time() + 6048000 /* 70 dagen */, $sPath );
		}

		if ( $oSession->joinpoolid > 0 and strlen( $oSession->joinkey ) > 0 )
		{
			$sAction = "pool/meedoen/poolid/" . $oSession->joinpoolid . "/key/" . $oSession->joinkey . "/";
			$oSession->joinpoolid = null;
			$oSession->joinkey = null;

			return $sAction;
		}

		$sAction = $sPreviousAction;
		return $sAction;
	}

	// @author http://codeascraft.etsy.com/2012/07/19/better-random-numbers-in-php-using-devurandom/
	protected function devurandom_rand($min = 0, $max = 0x7FFFFFFF)
	{
		$diff = $max - $min;
		if ($diff < 0 || $diff > 0x7FFFFFFF) {
			throw new RuntimeException('Bad range');
		}
        $bytes = random_bytes ( 4 );
		if ($bytes === false || strlen($bytes) != 4) {
			throw new RuntimeException('Unable to get 4 bytes');
		}
		$ary = unpack('Nint', $bytes);
		$val = $ary['int'] & 0x7FFFFFFF; // 32-bit safe
		$fp = (float) $val / 2147483647.0; // convert to [0,1]

		return round($fp * $diff) + $min;
	}

	protected function buildToken( $nLength = 128, $sCharMap = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
	{
		$nMapLength = mb_strlen( $sCharMap )-1;
		$sToken = '';
		while ( $nLength-- )
			$sToken .= mb_substr( $sCharMap, $this->devurandom_rand( 0, $nMapLength ), 1 );

		return $sToken;
	}
}
?>
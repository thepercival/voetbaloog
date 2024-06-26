<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Login.php 4191 2015-06-22 19:10:16Z thepercival $
 * @since	  File available since Release 4.0
 * @package		Auth
 */

/**
 *	@package	Auth
 */
class RAD_Auth_Login
{
	protected static $m_sDbHashType = null;
	protected static $m_sDbSalt = null;

	/**
	 * Constructs the class, protected constructor
	 */
	protected function __construct(){}

	public static function putDbHashType( $sHashType )
	{
		static::$m_sDbHashType = $sHashType;
	}

	public static function putDbSalt( $sDbSalt )
	{
		static::$m_sDbSalt = $sDbSalt;
	}

	public static function login( $vtUserName, $sPassword, $sModuleName )
	{
		$authAdapter = static::getAuthAdapter( $vtUserName, $sPassword );
		// Perform the authentication, saving the result
		$objAuth = Zend_Auth::getInstance();
		$objAuth->setStorage( new Zend_Auth_Storage_Session( $sModuleName ) );
		try
		{
			$result = $objAuth->authenticate( $authAdapter );
		}
		catch( Exception $e )
		{
			throw new Exception( $e->getMessage() );
		}
		if ( $result->getCode() !== Zend_Auth_Result::SUCCESS )
		{
			// $sMessages = implode( "<br>", $result->getMessages() );
			throw new Exception( "combinatie gebruikersnaam-wachtwoord is onjuist"
				// ."<br>".$sMessages
			);
		}

		// begin : anti-hack-hack
		Zend_Session::regenerateId();
		// end : anti-hack-hack
	}

	protected static function getAuthAdapter( $vtUserName, $sPassword )
	{
		if ( static::$m_sDbHashType === null )
			throw new Exception( "no dbhashtype set!", E_ERROR );
		$authAdapter = new Zend_Auth_Adapter_DbTable( Zend_Registry::get("db") );
		$authAdapter->setTableName( "UsersExt" );
		$authAdapter->setIdentityColumn( "LoginName" );
		$authAdapter->setCredentialColumn( "Password" );
		$authAdapter->setIdentity( $vtUserName );
		if ( static::$m_sDbSalt !== null )
			$sPassword = static::$m_sDbSalt . $sPassword;
		$authAdapter->setCredential( hash( static::$m_sDbHashType, $sPassword ) );

		return $authAdapter;
	}
}

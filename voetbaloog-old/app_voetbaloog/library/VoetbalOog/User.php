<?php

/**
 * @copyright2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: User.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link	 http://www.voetbaloog.nl/
 * @since	File available since Release 1.0
 * @package	VoetbalOog
 */

/**
 * @package	VoetbalOog
 */
class VoetbalOog_User extends RAD_Auth_User implements VoetbalOog_User_Interface
{
	// VoetbalOog_User_Interface
	protected $m_oPoolUsers;			// Collection
	protected $m_vtPicture;				// stream
	protected $m_sGender;				// string
	protected $m_oDateOfBirth;			// DateTime
	protected $m_sHashType;				// string
	protected $m_bSalted;				// bool
	protected $m_sActivationKey;		// string
	protected $m_sFacebookId;			// string
	protected $m_sGoogleId;				// string
	protected $m_sTwitterId;			// string
	protected $m_sCookieSessionToken;	// string
	protected $m_bPeriodicEmail;		// bool

	CONST MAX_LENGTH_NAME = 15;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @see VoetbalOog_User_Interface::getPoolUsers()
	 */
	public function getPoolUsers( $p_oOptions = null)
	{
		if ( $p_oOptions !== null )
		{
			$p_oOptions->addFilter( "VoetbalOog_Pool_User::User", "EqualTo", $this->getId() );
			return VoetbalOog_Pool_User_Factory::createObjectsFromDatabase( $p_oOptions );
		}
		else if ( $this->m_oPoolUsers === null )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "VoetbalOog_Pool_User::User", "EqualTo", $this->getId() );
			$this->m_oPoolUsers = VoetbalOog_Pool_User_Factory::createObjectsFromDatabase( $oOptions );
		}
		return $this->m_oPoolUsers;
	}

	/**
	 * @see VoetbalOog_User_Interface::getPicture()
	 */
	public function getPicture()
	{
		return $this->m_vtPicture;
	}

	/**
	 * @see VoetbalOog_User_Interface::putPicture()
	 */
	public function putPicture( $vtPicture )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_User::Picture", $this->m_vtPicture, $vtPicture );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_vtPicture = $vtPicture;
	}

	/**
	 * @see VoetbalOog_User_Interface::getGender()
	 */
	public function getGender()
	{
		return $this->m_sGender;
	}

	/**
	 * @see VoetbalOog_User_Interface::putGender()
	 */
	public function putGender( $sGender )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_User::Gender", $this->m_sGender, $sGender );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sGender = $sGender;
	}

	/**
	 * @see VoetbalOog_User_Interface::getDateOfBirth()
	 */
	public function getDateOfBirth()
	{
		if ( is_string( $this->m_oDateOfBirth ) )
			$this->m_oDateOfBirth = Agenda_Factory::createDateTime( $this->m_oDateOfBirth );
		return $this->m_oDateOfBirth;
	}

	/**
	 * @see VoetbalOog_User_Interface::putDateOfBirth()
	 */
	public function putDateOfBirth( $oDateOfBirth )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_User::DateOfBirth", $this->m_oDateOfBirth, $oDateOfBirth );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oDateOfBirth = $oDateOfBirth;
	}

	/**
	 * @see VoetbalOog_User_Interface::getHashType()
	 */
	public function getHashType()
	{
		return $this->m_sHashType;
	}

	/**
	 * @see VoetbalOog_User_Interface::putHashType()
	 */
	public function putHashType( $sHashType )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_User::HashType", $this->m_sHashType, $sHashType );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sHashType = $sHashType;
	}

	/**
	* @see VoetbalOog_User_Interface::getSalted()
	*/
	public function getSalted()
	{
		return $this->m_bSalted;
	}

	/**
	 * @see VoetbalOog_User_Interface::putSalted()
	 */
	public function putSalted( $bSalted )
	{
		$bSalted = ( ( (int) $bSalted ) === 1 );
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_User::Salted", $this->m_bSalted, $bSalted );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_bSalted = $bSalted;
	}

	/**
	 * @see VoetbalOog_User_Interface::getPeriodicEmail()
	 */
	public function getPeriodicEmail()
	{
		return $this->m_bPeriodicEmail;
	}

	/**
	 * @see VoetbalOog_User_Interface::putPeriodicEmail()
	 */
	public function putPeriodicEmail( $bPeriodicEmail )
	{
		$bPeriodicEmail = ( ( (int) $bPeriodicEmail ) === 1 );
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_User::PeriodicEmail", $this->m_bPeriodicEmail, $bPeriodicEmail );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_bPeriodicEmail = $bPeriodicEmail;
	}

	/**
	 * @see VoetbalOog_User_Interface::getActivationKey()
	 */
	public function getActivationKey()
	{
		return $this->m_sActivationKey;
	}

	/**
	 * @see VoetbalOog_User_Interface::putActivationKey()
	 */
	public function putActivationKey( $sActivationKey )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_User::ActivationKey", $this->m_sActivationKey, $sActivationKey );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sActivationKey = $sActivationKey;
	}

	/**
	 * @see VoetbalOog_User_Interface::getFacebookId()
	 */
	public function getFacebookId()
	{
		return $this->m_sFacebookId;
	}

	/**
	 * @see VoetbalOog_User_Interface::putFacebookId()
	 */
	public function putFacebookId( $sFacebookId )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_User::FacebookId", $this->m_sFacebookId, $sFacebookId );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sFacebookId = $sFacebookId;
	}

	/**
	 * @see VoetbalOog_User_Interface::getGoogleId()
	 */
	public function getGoogleId()
	{
		return $this->m_sGoogleId;
	}

	/**
	 * @see VoetbalOog_User_Interface::putGoogleId()
	 */
	public function putGoogleId( $sGoogleId )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_User::GoogleId", $this->m_sGoogleId, $sGoogleId );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sGoogleId = $sGoogleId;
	}

	/**
	 * @see VoetbalOog_User_Interface::getTwitterId()
	 */
	public function getTwitterId()
	{
		return $this->m_sTwitterId;
	}

	/**
	 * @see VoetbalOog_User_Interface::putTwitterId()
	 */
	public function putTwitterId( $sTwitterId )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_User::TwitterId", $this->m_sTwitterId, $sTwitterId );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sTwitterId = $sTwitterId;
	}
	/**
	 * @see VoetbalOog_User_Interface::getCookieSessionToken()
	 */
	public function getCookieSessionToken()
	{
		return $this->m_sCookieSessionToken;
	}

	/**
	 * @see VoetbalOog_User_Interface::putCookieSessionToken()
	 */
	public function putCookieSessionToken( $sCookieSessionToken )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_User::CookieSessionToken", $this->m_sCookieSessionToken, $sCookieSessionToken );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sCookieSessionToken = $sCookieSessionToken;
	}
}
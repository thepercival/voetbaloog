<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: User.php 4264 2015-12-23 22:41:38Z thepercival $
 * @since	  File available since Release 4.0
 * @package	Auth
 */

/**
 * @package	Auth
 */
class RAD_Auth_User implements RAD_Auth_User_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
	// RAD_Auth_User_Interface
	protected $m_sName;  					// string
	protected $m_sPassword;  				// string
	protected $m_sEmailAddress;			// string
	protected $m_oLatestLoginDateTime;	// DateTime
	protected $m_sLatestLoginIpAddress;	// string
	protected $m_bSystem;  					// bool
	protected $m_oRoles;  				// Patterns_Collection
	protected $m_sPreferences;				// string

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

	public function __construct(){}

	/**
	 * @see RAD_Auth_User_Interface::getName()
	 */
	public function getName()
	{
		return $this->m_sName;
	}

	/**
	 * @see RAD_Auth_User_Interface::putName()
	 */
	public function putName( $sName )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class() . "::Name", $this->m_sName, $sName );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sName = $sName;
	}

	/**
	 * @see RAD_Auth_User_Interface::getPassword()
	 */
	public function getPassword()
	{
		return $this->m_sPassword;
	}

	/**
	 * @see RAD_Auth_User_Interface::putPassword()
	 */
	public function putPassword( $sPassword )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class() . "::Password", $this->m_sPassword, $sPassword );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sPassword = $sPassword;
	}

	/**
	 * @see RAD_Auth_User_Interface::getEmailAddress()
	 */
	public function getEmailAddress()
	{
		return $this->m_sEmailAddress;
	}

	/**
	 * @see RAD_Auth_User_Interface::putEmailAddress()
	 */
	public function putEmailAddress( $sEmailAddress )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class() . "::EmailAddress", $this->m_sEmailAddress, $sEmailAddress );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sEmailAddress = $sEmailAddress;
	}

	/**
	 * @see RAD_Auth_User_Interface::getLatestLoginDateTime()
	 */
	public function getLatestLoginDateTime()
	{
		return $this->m_oLatestLoginDateTime;
	}

	/**
	 * @see RAD_Auth_User_Interface::putLatestLoginDateTime()
	 */
	public function putLatestLoginDateTime( $oLatestLoginDateTime )
	{
		if ( $oLatestLoginDateTime !== null and is_string( $oLatestLoginDateTime ) )
			$oLatestLoginDateTime = Agenda_Factory::createDateTime( $oLatestLoginDateTime );

		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class() . "::LatestLoginDateTime", $this->m_oLatestLoginDateTime, $oLatestLoginDateTime );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oLatestLoginDateTime = $oLatestLoginDateTime;
	}

	/**
	 * @see RAD_Auth_User_Interface::getLatestLoginIpAddress()
	 */
	public function getLatestLoginIpAddress()
	{
		return $this->m_sLatestLoginIpAddress;
	}

	/**
	 * @see RAD_Auth_User_Interface::putLatestLoginIpAddress()
	 */
	public function putLatestLoginIpAddress( $sLatestLoginIpAddress )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class() . "::LatestLoginIpAddress", $this->m_sLatestLoginIpAddress, $sLatestLoginIpAddress );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sLatestLoginIpAddress = $sLatestLoginIpAddress;
	}

	/**
	 * @see RAD_Auth_User_Interface::getSystem()
	 */
	public function getSystem()
	{
		return $this->m_bSystem;
	}

	/**
	 * @see RAD_Auth_User_Interface::putSystem()
	 */
	public function putSystem( $bSystem )
	{
		$bSystem = ( ( (int) $bSystem ) === 1 );
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class() . "::System", $this->m_bSystem, $bSystem );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_bSystem = $bSystem;
	}

	/**
	 * Defined by RAD_Auth_User_Interface;gets the root menuitem for this user. Each user can have roles.
	 * Dependant on which roles a user has, he gets to see some menuitems.
	 *
	 * @see RAD_Auth_User_Interface::getMenu()
	 */
	public function getMenu( $sModuleName, $sMenuItemName = "root" )
	{
		return RAD_Auth_MenuItem_Factory::getRootMenuItem( $this->getRoles(), $sModuleName, $sMenuItemName );
	}

	/**
	 * @see RAD_Auth_User_Interface::getRoles()
	 */
	public function getRoles( $sModuleName = null )
	{
		if ( $sModuleName !== null )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "RAD_Auth_Role::Id", "StartsWith", $sModuleName . "_" );
			return RAD_Auth_Role_Factory::createObjectsFromDatabaseExt( $this, $oOptions );
		}
		if ( $this->m_oRoles === null )
			$this->m_oRoles = RAD_Auth_Role_Factory::createObjectsFromDatabaseExt( $this );
		return $this->m_oRoles;
	}

	/**
	 * @see RAD_Auth_User_Interface::hasRole()
	 */
	public function hasRole( $sRoleName )
	{
		$oRoles = $this->getRoles();
		return ( $oRoles[ $sRoleName ] !== null );
	}

	/**
	 * @see RAD_Auth_User_Interface::getPreferences()
	 */
	public function getPreferences()
	{
		return $this->m_sPreferences;
	}

	/**
	 * @see RAD_Auth_User_Interface::putPreferences()
	 */
	public function putPreferences( $sPreferences )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class() . "::Preferences", $this->m_sPreferences, $sPreferences );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sPreferences = $sPreferences;
	}

	/**
	 * @see RAD_Auth_User_Interface::getPreference()
	 */
	public function getPreference( $sKey )
	{
		$sPreferences = $this->getPreferences();

		$arrPreferences = array();
		// explode to $arrPreferences
		if ( $sPreferences !== null )
		{
			$arrItems = explode(";", $sPreferences);
			for( $nI = 0 ; $nI < count( $arrItems ) ; $nI++ )
			{
				$arrIdValue = explode("=", $arrItems[$nI]);
				$arrPreferences [ $arrIdValue[0] ] = $arrIdValue[1];
			}
		}

		if ( array_key_exists( $sKey, $arrPreferences ) === false )
			return null;

		return $arrPreferences[$sKey];
	}

	/**
	 * @see RAD_Auth_User_Interface::putPreference()
	 */
	public function putPreference( /* variable param list */ )
	{
		$sPreferences = $this->getPreferences();

		$arrPreferences = array();
		// explode to $arrPreferences
		if ( $sPreferences !== null )
		{
			$arrItems = explode(";", $sPreferences);
			for( $nI = 0 ; $nI < count( $arrItems ) ; $nI++ )
			{
				$arrIdValue = explode("=", $arrItems[$nI]);
				$arrPreferences [ $arrIdValue[0] ] = $arrIdValue[1];
			}
		}

		// put value
		for( $nI = 0; $nI < func_num_args() ; $nI+=2 )
			$arrPreferences[ func_get_arg( $nI ) ] = func_get_arg( $nI+1 );

		// implode to $sPreferences
		{
			$sPreferences = "";
			foreach( $arrPreferences as $sPreferenceId => $sPreferenceValue )
			{
				$sPreferences .= $sPreferenceId."=".$sPreferenceValue.";";
			}
			$sPreferences = substr( $sPreferences, 0, strlen( $sPreferences ) - 1 );
		}

		$this->putPreferences( $sPreferences );
	}
}
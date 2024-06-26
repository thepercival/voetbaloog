<?php

/**
 * @copyright 2007 Coen Dunnink
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Id: Message.php 4261 2015-12-23 21:07:47Z thepercival $
 * @since File available since Release 4.0
 * @package Message
 */

/**
 * @package Message
 */
class Message implements Message_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
	// Message_Interface
	protected $m_sSubject; 			// string
	protected $m_szDescription; 	// string
	protected $m_objFromUser; 		// RAD_Auth_User
	protected $m_objToUser; 		// RAD_Auth_User
	protected $m_objInputDateTime; 	// DateTime
	protected $m_nState; 			// int
	protected $m_nContext; 			// int

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

	public static $m_nStateNew = 1;
	public static $m_nContextFeedBack = 1;
	public static $m_nContextLogin = 2;

	/**
	* @see Message_Interface::getSubject()
	*/
	public function getSubject()
	{
		return $this->m_sSubject;
	}

	/**
	* @see Message_Interface::putSubject()
	*/
	public function putSubject( $sSubject )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Message::Subject", $this->m_sSubject, $sSubject );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_sSubject = $sSubject;
	}

	/**
	* @see Message_Interface::getDescription()
	*/
	public function getDescription()
	{
		return $this->m_szDescription;
	}

	/**
	*
	* @see Message_Interface::putDescription()
	*/
	public function putDescription( $szDescription )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Message::Description", $this->m_szDescription, $szDescription );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_szDescription = $szDescription;
	}

	/**
	*
	* @see Message_Interface::getFromUser()
	*/
	public function getFromUser()
	{
		if ( $this->m_objFromUser !== null and is_int( $this->m_objFromUser ) )
			$this->m_objFromUser = RAD_Auth_User_Factory::createObjectFromDatabase( $this->m_objFromUser );
		return $this->m_objFromUser;
	}

	/**
	*
	* @see Message_Interface:: putFromUser()
	*/
	public function putFromUser( $objFromUser )
	{
		$this->m_objFromUser = $objFromUser;
	}

	/**
	*
	* @see Message_Interface::getToUser()
	*/
	public function getToUser()
	{
		if ( $this->m_objToUser !== null and is_int( $this->m_objToUser ) )
			$this->m_objToUser = RAD_Auth_User_Factory::createObjectFromDatabase( $this->m_objToUser );
		return $this->m_objToUser;
	}

	/**
	* @see Message_Interface:: putToUser()
	*/
	public function putToUser( $objToUser )
	{
		$this->m_objToUser = $objToUser;
	}

	/**
	*
	* @see Message_Interface::getInputDateTime()
	*/
	public function getInputDateTime()
	{
		return $this->m_objInputDateTime;
	}

	/**
	*
	* @see Message_Interface::putInputDateTime()
	*/
	public function putInputDateTime( $objInputDateTime )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Message::InputDateTime", $this->m_objInputDateTime, $objInputDateTime );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_objInputDateTime = $objInputDateTime;
	}

	/**
	*
	* @see Message_Interface::getState()
	*/
	public function getState()
	{
		return $this->m_nState;
	}

	/**
	*
	* @see Message_Interface:: putState()
	*/
	public function putState( $nState )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Message::State", $this->m_nState, $nState );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_nState = $nState;
	}

	/**
	*
	* @see Message_Interface::getContext()
	*/
	public function getContext()
	{
		return $this->m_nContext;
	}

	/**
	*
	* @see Message_Interface:: putContext()
	*/
	public function putContext( $nContext )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Message::Context", $this->m_nContext, $nContext );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_nContext = $nContext;
	}

}



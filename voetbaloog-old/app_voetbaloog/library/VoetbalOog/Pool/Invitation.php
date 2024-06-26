<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: User.php 580 2013-11-20 15:28:51Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pool_Invitation implements VoetbalOog_Pool_Invitation_Interface, Patterns_Idable_Interface, Patterns_ObservableObject_Interface
{
	// VoetbalOog_Pool_Invitation_Interface
	protected $m_oPool;					// VoetbalOog_Pool
	protected $m_oInviter;				// VoetbalOog_User
	protected $m_oSendDateTime;			// DateTime
	protected $m_oInvitee;				// VoetbalOog_User
	protected $m_sInviteeEmailAddress;  // string
	protected $m_oAcceptedDateTime;		// DateTime
	protected $m_oRejectedDateTime;		// DateTime

	use Patterns_Idable_Trait, Patterns_ObservableObject_Trait;

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::getPool()
	 */
	public function getPool()
	{
		if ( is_int( $this->m_oPool ) )
			$this->m_oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( $this->m_oPool );

		return $this->m_oPool;
	}

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::putPool()
	 */
	public function putPool( $oPool )
	{
		$this->m_oPool = $oPool;
	}

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::getInviter()
	 */
	public function getInviter()
	{
		if ( is_int( $this->m_oInviter ) )
			$this->m_oInviter = VoetbalOog_User_Factory::createObjectFromDatabase( $this->m_oInviter );

		return $this->m_oInviter;
	}

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::putInviter()
	 */
	public function putInviter( $oInviter )
	{
		$this->m_oInviter = $oInviter;
	}

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::getSendDateTime()
	 */
	public function getSendDateTime()
	{
		return $this->m_oSendDateTime;
	}

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::putSendDateTime()
	 */
	public function putSendDateTime( $oSendDateTime )
	{
		if ( is_string( $oSendDateTime ) )
			$oSendDateTime = Agenda_Factory::createDateTime( $oSendDateTime );

		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::SendDateTime", $this->m_oSendDateTime, $oSendDateTime );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_oSendDateTime = $oSendDateTime;
	}

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::getInvitee()
	 */
	public function getInvitee()
	{
		if ( is_int( $this->m_oInvitee ) )
			$this->m_oInvitee = VoetbalOog_User_Factory::createObjectFromDatabase( $this->m_oInvitee );

		return $this->m_oInvitee;
	}

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::putInvitee()
	 */
	public function putInvitee( $oInvitee )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::Invitee", $this->m_oInvitee, $oInvitee );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_oInvitee = $oInvitee;
	}

	/**
	 * @see VoetbalOog_Referee_Interface::getInviteeEmailAddress()
	 */
	public function getInviteeEmailAddress()
	{
		return $this->m_sInviteeEmailAddress;
	}

	/**
	 * @see VoetbalOog_Referee_Interface::putInviteeEmailAddress()
	 */
	public function putInviteeEmailAddress( $sInviteeEmailAddress )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::InviteeEmailAddress", $this->m_sInviteeEmailAddress, $sInviteeEmailAddress );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sInviteeEmailAddress = $sInviteeEmailAddress;
	}

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::getAcceptedDateTime()
	 */
	public function getAcceptedDateTime()
	{
		return $this->m_oAcceptedDateTime;
	}

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::putAcceptedDateTime()
	 */
	public function putAcceptedDateTime( $oAcceptedDateTime )
	{
		if ( is_string( $oAcceptedDateTime ) )
			$oAcceptedDateTime = Agenda_Factory::createDateTime( $oAcceptedDateTime );

		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::AcceptedDateTime", $this->m_oAcceptedDateTime, $oAcceptedDateTime );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_oAcceptedDateTime = $oAcceptedDateTime;
	}

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::getRejectedDateTime()
	 */
	public function getRejectedDateTime()
	{
		return $this->m_oRejectedDateTime;
	}

	/**
	 * @see VoetbalOog_Pool_Invitation_Interface::putRejectedDateTime()
	 */
	public function putRejectedDateTime( $oRejectedDateTime )
	{
		if ( is_string( $oRejectedDateTime ) )
			$oRejectedDateTime = Agenda_Factory::createDateTime( $oRejectedDateTime );

		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::RejectedDateTime", $this->m_oRejectedDateTime, $oRejectedDateTime );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_oRejectedDateTime = $oRejectedDateTime;
	}
}
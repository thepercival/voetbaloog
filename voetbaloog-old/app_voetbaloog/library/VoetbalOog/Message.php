<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Message.php 1050 2015-12-28 21:03:53Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Message implements VoetbalOog_Message_Interface, Patterns_Idable_Interface, Patterns_ObservableObject_Interface
{
	// VoetbalOog_Message_Interface
	protected $m_sMessage;			// string
	protected $m_oPoolUser;			// VoetbalOog_Pool_User_Interface
	protected $m_oDateTime;			// Zend_DateExt_Interface

	use Patterns_Idable_Trait, Patterns_ObservableObject_Trait;

	/**
	 * @see Voetbal_PoulePlace_Interface::getMessage()
	 */
	public function getMessage()
	{
		return $this->m_sMessage;
	}

	/**
	 * @see Voetbal_PoulePlace_Interface::putMessage()
	 */
	public function putMessage( $sMessage )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Message::Message", $this->m_sMessage, $sMessage );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sMessage = $sMessage;
	}

	/**
	 * @see VoetbalOog_Message_Interface::getPoolUser()
	 */
	public function getPoolUser()
	{
		if ( is_int( $this->m_oPoolUser ) )
			$this->m_oPoolUser = VoetbalOog_Pool_User_Factory::createObjectFromDatabase( $this->m_oPoolUser );

		return $this->m_oPoolUser;
	}

	/**
	 * @see VoetbalOog_Message_Interface:: putPoolUser()
	 */
	public function putPoolUser( $oPoolUser )
	{
		$this->m_oPoolUser = $oPoolUser;
	}

	/**
	 * @see VoetbalOog_Message_Interface::getDateTime()
	 */
	public function getDateTime()
	{
		return $this->m_oDateTime;
	}

	/**
	 * @see VoetbalOog_Message_Interface::putDateTime()
	 */
	public function putDateTime( $oDateTime )
	{
		if ( is_string( $oDateTime ) )
			$oDateTime = Agenda_Factory::createDateTime( $oDateTime );

		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "VoetbalOog_Message::DateTime", $this->m_oDateTime, $oDateTime );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oDateTime = $oDateTime;
	}
}
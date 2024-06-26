<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Membership.php 776 2014-03-05 08:37:12Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Team_Membership extends MemberShip implements Voetbal_Team_Membership_Interface
{
	// Voetbal_Team_Membership_Interface
	protected $m_vtPicture;			// stream

	/**
	 * Constructs the class
	 */
	public function __construct() { parent::__construct(); }

	/**
	* @see Voetbal_Team_Membership_Interface::getPicture()
	*/
	public function getPicture()
	{
		return $this->m_vtPicture;
	}

	/**
	 * @see Voetbal_Team_Membership_Interface::putPicture()
	 */
	public function putPicture( $vtPicture )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class() . "::Picture", $this->m_vtPicture, $vtPicture );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_vtPicture = $vtPicture;
	}

	/**
	 * @see Membership_Interface::getClient()
	 */
	public function getClient()
	{
		if ( is_int( $this->m_objClient ) )
			$this->m_objClient = Voetbal_Person_Factory::createObjectFromDatabase( $this->m_objClient );

		return $this->m_objClient;
	}

	/**
	 * @see Membership_Interface::getProvider()
	 */
	public function getProvider()
	{
		if ( is_int( $this->m_objProvider ) )
			$this->m_objProvider = Voetbal_Team_Factory::createObjectFromDatabase( $this->m_objProvider );

		return $this->m_objProvider;
	}
}
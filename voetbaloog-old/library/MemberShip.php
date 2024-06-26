<?php

/**
 * MemberShip.php
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: MemberShip.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 *
 * @package    MemberShip
 */


/**
 *
 * @package MemberShip
 */
class MemberShip extends Agenda_TimeSlot implements MemberShip_Interface, Import_Importable_Interface
{
	// MemberShip_Interface
	protected $m_objClient;  			// Patterns_Idable_Interface
	protected $m_objProvider;  			// Patterns_Idable_Interface

	use Import_Importable_Trait;

	public function __construct()
  	{
  		parent::__construct();
  	}

  	/**
	 * @see MemberShip_Interface::getClient()
	 */
	public function getClient()
	{
		return $this->m_objClient;
	}

	/**
	 * @see MemberShip_Interface::putClient()
	 */
	public function putClient( $objClient )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::Client", $this->m_objClient, $objClient );
  			$this->notifyObservers( $objObjectChange );
		}
		$this->m_objClient = $objClient;
	}

	/**
	 * @see MemberShip_Interface::getProvider()
	 */
	public function getProvider()
	{
		return $this->m_objProvider;
	}

	/**
	 * @see MemberShip_Interface::putProvider()
	 */
	public function putProvider( $objProvider )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::Provider", $this->m_objProvider, $objProvider );
  			$this->notifyObservers( $objObjectChange );
		}
		$this->m_objProvider = $objProvider;
	}
}
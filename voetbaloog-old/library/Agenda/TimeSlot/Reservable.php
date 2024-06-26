<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Reservable.php 4157 2015-05-06 12:17:47Z thepercival $
 * @package    Agenda
 */

/**
 * @package Agenda
 */
class Agenda_TimeSlot_Reservable extends Agenda_TimeSlot implements Agenda_TimeSlot_Reservable_Interface
{
	// Agenda_TimeSlot_Reservable_Interface
	protected $m_nState;						// int

	public static $m_nStateRosterReserved = 0;
	public static $m_nStateFree = 1;
	public static $m_nStateReserved = 2;

	/**
	 * @see Agenda_TimeSlot_Reservable_Interface::getState()
	 */
	public function getState()
	{
		return $this->m_nState;
	}

	/**
	 * @see Agenda_TimeSlot_Reservable_Interface::putState()
	 */
	public function putState( $nState )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Agenda_TimeSlot_Reservable::State", $this->m_nState, $nState );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_nState = $nState;
	}
}
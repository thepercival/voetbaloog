<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Agenda.php 4261 2015-12-23 21:07:47Z thepercival $
 *
 * @package    Agenda
 */

/**
 * @package Agenda
 */
class Agenda implements Agenda_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
	// Agenda_Interface
	protected $m_objTimeslots;  // Patterns_Collection_Interface

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;


	/**
	 * Defined by Agenda_Interface; gets The timeslots of an agenda
	 *
	 * @see Agenda_Interface::getTimeSlots()
	 */
	public function getTimeSlots( $objPeriod = null )
	{
		if ( $objPeriod !== null )
			throw new Exception("The Period should be a filter on all the timeslots which are already gotten from the db!", E_ERROR );

		if ( $this->m_objTimeslots === null )
			$this->m_objTimeslots = Agenda_Factory::createTimeSlots();
		
		return $this->m_objTimeslots;
	}

	/**
	 * Defined by Agenda_Interface; gets The timeslots of an agenda
	 *
	 * @see Agenda_Interface::getTimeSlots()
	 */
	public function addTimeSlots( $objTimeSlotsParam )
	{
		$objTimeSlots = $this->getTimeSlots();
		return $objTimeSlots->addCollection( $objTimeSlotsParam );
	}
}
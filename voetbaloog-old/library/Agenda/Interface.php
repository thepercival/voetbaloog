<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4554 2019-08-12 14:37:34Z thepercival $
 * @package    Agenda
 */

/**
 * @package Agenda
 */
interface Agenda_Interface
{
	/**
	 * gets The timeslots of an agenda
	 *
	 * @param 	Agenda_TimeSlot|Agenda_TimeSlot_Collection $objPeriod 	Options for a filter on the timeslots
	 * @return 	Patterns_Collection_Interface	The timeslots of an agenda
	 */
	public function getTimeSlots( $objPeriod = null );
	/**
	 * adds timeslots to an agenda
	 *
	 * @param 	Agenda_TimeSlot_Collection $objTimeSlotsParam	The timeslots which will be added
	 * @return 	bool true if succeeded false if not
	 */
	public function addTimeSlots( $objTimeSlotsParam );
}
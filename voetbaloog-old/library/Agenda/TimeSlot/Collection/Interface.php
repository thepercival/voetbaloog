<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 * @package    Agenda
 */

/**
 * @package Agenda
 */
interface Agenda_TimeSlot_Collection_Interface
{
	/**
	 * return true if is in Agenda_TimeSlot_Collection
	 *
	 * @param Agenda_TimeSlot_Interface $objTimeSlot	    The timeslot to look for
	 * @param int                       $nRange	            The datepart in which the duration should be returned
	 * @param bool                      $bInvertCompare		Checks if the comparison chould be inverted
	 * @return 	bool true if is in timeslotcollection
	 */
	public function has( $objTimeSlot, $nRange = Agenda_TimeSlot::EXCLUDE_NONE, $bInvertCompare = false );
	/**
	 * return the timeslots which are in $objTimeSlotToBeIn
	 *
	 * @param Agenda_TimeSlot $objTimeSlotToBeIn	The timeslot where to look in
	 * @return Patterns_Collection_Interface 	The timeslots which are in $objTimeSlotToBeIn
	 */
	public function getOverlappingTimeSlots( Agenda_TimeSlot_Interface $objTimeSlotToBeIn, $nRange = 0 );
	/**
	 * return the timeslot which overlapses the datetime
	 *
	 * @param DateTime  $oDateTime	The datetime to look for
	 * @return Agenda_TimeSlot	The timeslot which overlapses the datetime
	 */
	public function getByDateTime( DateTime $oDateTime = null );
	/**
	 * return if the date is in one of the timeslots
	 *
	 * @param DateTime  $oDateTime	The datetime to look for
	 * @return bool 	true | false    If the datetime to look for is in one of the timeslots
	 */
	public function hasDateTime( DateTime $oDateTime = null );
	/**
	 * returns the duration
	 *
	 * @param int $nZendDateTimePart	The datepart in which the duration should be returned
	 * @return 	int the duration
	 */
	public function getDuration( $nZendDateTimePart );
	/**
	 * returns true if start- and enddatetimes are equal
	 *
	 * @param	Patterns_Collection			$oTimeSlots
	 * @return 	bool true if equals
	 */
	public function equals( $oTimeSlots );
}
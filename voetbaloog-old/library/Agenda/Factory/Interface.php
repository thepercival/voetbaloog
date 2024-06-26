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
interface Agenda_Factory_Interface
{
	/**
	 * Creates a timeslot, only one of the two parameters can be NULL
	 *
	 * @param	DateTime|string $vtStartDateTime The startdatetime of the timeslot
	 * @param	DateTime|string $vtEndDateTime The enddatetime of the timeslot
	 * @return 	Agenda_TimeSlot_Interface	The created timeslot
	 */
	public static function createTimeSlot( $vtStartDateTime = null, $vtEndDateTime = null );
	/**
	 * Creates an agenda
	 *
	 * @param	string $szId The id of the agenda
	 * @return 	Agenda_Interface	The created agenda
	 */
	public static function createAgenda( $szId );
	/**
	 * Creates a collection of TimeSlots
	 *
	 * @return 	Agenda_TimeSlot_Collection	The created collection of TimeSlots
	 */
	public static function createTimeSlots();
	/**
	 * Creates a date, if $szDate is not set, the current date will
	 * be returned. If $szDate is not valid null will be returned.
	 *
	 * @param string $szDate  The date to be created
	 * @return 	DateTime	The date
	 */
	public static function createDate( $szDate = null );

	/**
	 * Creates a datetime, if $szDateTime is not set, the current datetime will
	 * be returned. If $szDateTime is not valid null will be returned.
	 *
	 * @param string $szDateTime  The datetime to be created
	 * @return 	DateTime	The datetime
	 */
	public static function createDateTime( $szDateTime = null );
	/**
	 * Creates a day
	 *
	 * @param 	DateTime	$objDate  The date
	 * @return 	Agenda_TimeSlot_Interface	The day
	 */
	public static function createDay( $objDate = null );
	/**
	 * returns the duration
	 *
	 * @param 	DateTime	$objStartDateTime  	The datetime
	 * @param 	DateTime	$objEndDateTime		The datetime
	 * @param 	int 		$nZendDateTimePart	The datepart in which the duration should be returned
	 * @param	bool		$bFloored			If result should be floored
	 * @return 	int the duration
	 */
	public static function getDuration( $objStartDateTime, $objEndDateTime, $nZendDateTimePart = null, $bFloored = false );
	/**
	* returns the overlap duration
	*
	* @param 	Agenda_TimeSlot	$oTimeSlotA  	The first timeslot
	* @param 	Agenda_TimeSlot	$oTimeSlotB		The second timeslot
	* @return 	Agenda_TimeSlot	the overlap
	*/
	public static function createOverlap( $oTimeSlotA, $oTimeSlotB );
}
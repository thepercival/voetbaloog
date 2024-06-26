<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4585 2020-05-02 09:44:21Z thepercival $
 * @package    Agenda
 */

/**
 * @package Agenda
 */
interface Agenda_TimeSlot_Interface
{
	/**
	 * gets the startdatetime
	 *
	 * @return 	Agenda_DateTime	The startdatetime
	 */
	public function getStartDateTime(): Agenda_DateTime;
	/**
	 * gets the startdatetime
	 *
	 * @param 	DateTime	$objStartDateTime	The startdatetime
	 * @return 	null
	 */
	public function putStartDateTime( $objStartDateTime );
	/**
	 * gets the enddatetime
	 *
	 * @return 	DateTime	The enddatetime
	 */
	public function getEndDateTime();
	/**
	 * gets the enddatetime
	 *
	 * @param 	DateTime	$objEndDateTime	The enddatetime
	 * @return 	null
	 */
	public function putEndDateTime( $objEndDateTime );
	/**
	 * return true if DateTime is within this timeslot
	 *
	 * @param   Agenda_DateTime $oDateTime	    The datetime
	 * @return 	bool            true            if date is within this
	 */
	public function isIn( Agenda_DateTime $oDateTime );
	/**
	 * return true if $this is overlapping with $objTimeSlot
	 *
	 * @param Agenda_TimeSlot_Interface $objTimeSlot	The timeslot to look in
	 * @return 	bool true if is within $objTimeSlot
	 */
	public function overlapses( $objTimeSlot, $nRange = 0 );
	/**
	 * returns the duration
	 *
	 * @param 	int 	$nZendDateTimePart	The datepart in which the duration should be returned
	 * @param	bool	$bFloored			If result should be floored
	 * @return 	int the duration
	 */
	public function getDuration( $nZendDateTimePart = null, $bFloored = false );
	/**
	 * returns true if start- and enddatetime is equal
	 *
	 * @param	Agenda_TimeSlot			$oTimeSlot
	 * @return 	bool true if equals
	 */
	public function equals( $oTimeSlot );
}
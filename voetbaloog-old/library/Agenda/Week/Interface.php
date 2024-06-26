<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 * @package    Agenda
 */

/**
 * @package Agenda
 */
interface Agenda_Week_Interface
{
    /**
     * writes the name of the Week.
     *
     * @return null
     */
    public function putName($szName);
    /**
	 * gets the name of the Week
	 *
	 * @return  string	the name of the Week
	 */
	public function getName();
	/**
	 * gets the days
	 *
	 * @return  Agenda_TimeSlot_Collection	the days of the week
	 */
	public function getDays();
	/**
	 * gets the days without the weekend
	 *
	 * @return  Agenda_TimeSlot_Collection	the days of the week without the weekend
	 */
	public function getDaysWithOutWeekend();
}

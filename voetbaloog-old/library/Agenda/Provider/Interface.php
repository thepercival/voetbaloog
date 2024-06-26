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
interface Agenda_Provider_Interface
{
	/**
	 * gets The agenda
	 *
	 * @param 	Agenda_TimeSlot | Agenda_TimeSlot_Collection 	$objPeriod 	Options for a filter on the timeslots
	 * @param 	Patterns_Collection_Interface 	$objFilters 	The agendafilter
	 * @return 	Agenda_Interface	The agenda
	 */
	public function getAgenda( $objPeriod = null, $objFilters = null );
}
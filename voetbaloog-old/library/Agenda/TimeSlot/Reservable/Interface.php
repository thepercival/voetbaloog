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
interface Agenda_TimeSlot_Reservable_Interface
{
	/**
	 * gets the State
	 *
	 * @return 	int	the State
	 */
	public function getState();
	/**
	 * puts the State
	 *
	 * @param int $nState the State
	 * @return 	null
	 */
	public function putState( $nState );

}
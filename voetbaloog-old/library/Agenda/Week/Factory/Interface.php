<?php

/**
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 *
 * @package    Agenda  
 */

/**
 * @package Agenda
 */
interface Agenda_Week_Factory_Interface
{
	/**
	 * Creates a week
	 * 
	 * @param Agenda_DateTime 					$oDate	the date within the week
	 * @return Agenda_Week_Interface				the week
	 */
	public static function createObjectFromDate( $oDate );
	/**
	 * Creates a week
	 * 
	 * @param mixed $vtWeekId					the id of the week
	 * @param mixed $vtWeekName				the name of the week
	 * @return Agenda_Week_Interface	the week
	 */
	public static function createObjectExt( $vtWeekId = null, $vtWeekName = null );
}

?>
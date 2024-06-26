<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    MemberShip
 */

/**
 * Defines the interface MemberShip_Factory_Interface.
 *
 * @package    MemberShip
 */
interface MemberShip_Factory_Interface
{
	/**
	 * creates a MemberShip
	 *
	 * @param 	MemberShip_Provider_Interface   $oProvider  The membershipprovider
	 * @param 	Patterns_Idable_Interface	    $oClient    The membershipclient
	 * @return  MemberShip_Interface	The MemberShip
	 */
	public static function createObject( $oProvider, $oClient );
	/**
	 * creates a collection for MemberShips
	 *
	 * @return  Patterns_Collection		The collection for MemberShips
	 */
	public static function createObjects();
	/**
	 * get memberships filters
	 *
	 * @param	string							$szClassName		The classname
	 * @param 	MemberShip_Provider_Interface 	$objProvider 		The membershipprovider
	 * @param 	Patterns_Idable_Interface 		$objClient 			The membershipclient
	 * @param 	DateTime | Agenda_TimeSlot		$objDateTimeSlot	The date or the timeslot
	 * @return  Patterns_Collection_Interface	The collection of filters
	 */
	public static function getMembershipFilters( $szClassName, $objProvider, $objClient, $objDateTimeSlot );
	/**
	 * get memberships filters
	 *
	 * @param 	Patterns_Idable_Interface	        $oClient 			The membershipclient
	 * @param 	MemberShip_Provider_Interface 		$oProvider 			The membershipprovider
	 * @param 	Agenda_TimeSlot				        $oTimeSlotParam		The timeslot
	 * @return  Patterns_Collection_Interface	The collection of filters
	 */
	public static function getOverlaps( $oClient, $oProvider, $oTimeSlotParam );
}
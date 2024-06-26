<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 914 2014-08-24 16:32:52Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Person_Interface
{
	/**
	* gets the gender
	*
	* @return 	string	The gender
	*/
	public function getGender();
	/**
	 * puts the Gender
	 *
	 * @param 	string	$sGender	The gender
	 * @return 	null
	 */
	public function putGender( $sGender );
	/**
	* gets the teammembershipplayers
	*
    * @param    Agenda_TimeSlot | DateTime	$vtDateTimeSlot		the datetime or timeslot, default is null
	* @return   MemberShip_Collection	The teammembershipplayers
	*/
	public function getPlayerMemberships( $vtDateTimeSlot = null ): MemberShip_Collection;
	/**
	* gets the teamstaffmemberships
	*
	* @return 	MemberShip_Collection	The teamstaffmemberships
	*/
	public function getStaffMemberMemberships(): MemberShip_Collection;
}
<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 955 2014-09-15 16:08:29Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Team_Interface
{
	/**
	 * gets the Name
	 *
	 * @return 	string	the Name
	 */
	public function getName();
	/**
	 * puts the Name
	 *
	 * @param string $sName the Name which will be set
	 * @return 	null
	 */
	public function putName( $sName );
	/**
	 * gets the Abbreviation
	 *
	 * @return 	string	the Abbreviation
	 */
	public function getAbbreviation();
	/**
	 * puts the Abbreviation
	 *
	 * @param string $sAbbreviation the Abbreviation which will be set
	 * @return 	null
	 */
	public function putAbbreviation( $sAbbreviation );
	/**
	 * gets the ImageName
	 *
	 * @return 	string	the ImageName
	 */
	public function getImageName();
	/**
	 * puts the ImageName
	 *
	 * @param string $sImageName the ImageName which will be set
	 * @return 	null
	 */
	public function putImageName( $sImageName );
	/**
	 * gets the Association
	 *
	 * @return 	Voetbal_Association_Interface	the Association
	 */
	public function getAssociation();
	/**
	 * puts the Association
	 *
	 * @param Voetbal_Association_Interface	$oAssociation the Association
	 * @return 	null
	 */
	public function putAssociation( $oAssociation );
	/**
	 * gets the player memberships
	 *
	 * @param Agenda_TimeSlot | DateTime 		$oDateTimeSlot 	periode of datum
	 * @param Construction_Option_Collection	$p_oOptions		options
	 * @return 	MemberShip_Collection	The player memberships
	 */
	public function getPlayerMemberships( $oDateTimeSlot, Construction_Option_Collection $p_oOptions = null );
	/**
	 * gets the player memberships
	 *
	 * @param Agenda_TimeSlot | DateTime 		$oDateTimeSlot 	periode of datum
	 * @param Construction_Option_Collection	$p_oOptions		options
	 * @return 	Patterns_Collection					The persons
	 */
	public function getPlayerPersons( $oDateTimeSlot, $p_oOptions = null );
	/**
	* gets the staffmember memberships
	*
	* @param Agenda_TimeSlot | DateTime $oDateTimeSlot periode of datum
	* @return 	MemberShip_Collection	The staffmember memberships
	*/
	public function getStaffMemberships( $oDateTimeSlot );
}
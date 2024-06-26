<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Team_Membership_Interface
{
	/**
	* gets the Picture
	*
	* @return 	string|null	The Picture
	*/
	public function getPicture();
	/**
	 * puts the Picture
	 *
	 * @param 	string|null	$vtPicture	The Picture
	 * @return 	null
	 */
	public function putPicture( $vtPicture );
}
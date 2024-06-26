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
interface Voetbal_Team_Membership_Factory_Interface
{
	/**
	 * gets the image stream of a playerperiod
	 *
	 * @param 	int 				$nId		the playerperiodid
	 * @return 	string|null
	 */
	public static function getPicture( $nId );
}
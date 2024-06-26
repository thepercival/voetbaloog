<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    MemberShip
 */

/**
 * @package MemberShip
 */
interface MemberShip_Provider_Interface
{
	/**
	 * gets the Memberships
	 *
	 * @param 	DateTime $oDateTime The datetime for which the memberships should be collected
	 * @return 	Patterns_Collection	the Memberships
	 */
	public function getMemberships( $oDateTime );
}
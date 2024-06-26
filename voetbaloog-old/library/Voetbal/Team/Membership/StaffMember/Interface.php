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
interface Voetbal_Team_Membership_StaffMember_Interface
{
	/**
	 * gets the Importance
	 *
	 * @return 	int			the Importance
	 */
	public function getImportance();
	/**
	 * puts the Importance
	 *
	 * @param int $nImportance the Importance which will be set
	 * @return 	null
	 */
	public function putImportance( $nImportance );
	/**
	* gets the FunctionX
	*
	* @return 	string			the function
	*/
	public function getFunctionX();
	/**
	 * puts the Function
	 *
	 * @param string $sFunctionX the Function which will be set
	 * @return 	null
	 */
	public function putFunctionX( $sFunctionX );
}
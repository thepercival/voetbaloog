<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 *
 * @package    Source
 */

/**
 * @package Auth
 */
interface RAD_Auth_Db_Reader_Interface
{
	/**
	 * Creates objects for a certain user and role
	 *
	 * @param RAD_Auth_User							            $oUser		The user
	 * @param null|RAD_Auth_Role|string|Patterns_Collection		$oRole		The role
	 * @param Construction_Option_Collection	                $oOptions	The construction options
	 * @return Patterns_Collection	A collection of objects
	 */	
	public function createAuthorizedObjects( RAD_Auth_User $oUser, $oRole, Construction_Option_Collection $oOptions = null ): Patterns_Collection;
}
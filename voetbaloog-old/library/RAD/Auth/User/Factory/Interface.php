<?php

/**
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 *
 * @package    Auth
 */

/**
 *
 * @package Auth
 */
interface RAD_Auth_User_Factory_Interface
{
	/**
	 * Creates a collection of objects from a role from the database
	 *
	 * @param RAD_Auth_Role_Interface 		$oRole		The Role
	 * @param Patterns_Collection		$oOptions	The constructionOptions
	 * @return Patterns_Collection					The Users
	 */
	public static function createObjectsForRoleFromDatabase( $oRole, $oOptions = null );
}
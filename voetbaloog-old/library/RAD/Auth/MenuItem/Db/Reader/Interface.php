<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 *
 * @package    Auth
 */


/**
 *
 *
 * @package    Auth
 */
interface RAD_Auth_MenuItem_Db_Reader_Interface
{
	/**
	 * gets the menuitems from the database for certain roles
	 *
	 * @param 	Patterns_Collection_Interface 	$objRoles	A collection of roles
	 * @param  string			$szModuleName	The modulename
	 * @param  string			$szMenuItemName	The menuitemname
	 * @return 	Patterns_Collection_Interface		A collection of roles from the database
	 */
	public function getMenuItems( $objRoles, $szModuleName, $szMenuItemName );
}
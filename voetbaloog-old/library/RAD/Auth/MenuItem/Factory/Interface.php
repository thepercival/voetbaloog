<?php

/**
 * RAD_Auth_MenuItem_Factory_Interface.php
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 *
 * @package    Auth
 */

/**
 * Defines the interface RAD_Auth_MenuItem_Factory_Interface.
 *
 * @package    Auth
 */
interface RAD_Auth_MenuItem_Factory_Interface
{
	/**
	 * gets the menu item from the database
	 *
	 * @param  Patterns_Collection 	$objRoles	A collection of roles
	 * @param  string			$szModuleName	The modulename
	 * @param  string			$szMenuItemName	The menuitemname
	 * @return RAD_Auth_MenuItem    The root menuitems
	 */
	public static function getRootMenuItem( $objRoles, $szModuleName, $szMenuItemName = "root" );
	/**
	 * gets the menu item from the database for the default user
	 *
	 * @param  string			$szModuleName	The modulename
	 * @param  string			$szMenuItemName	The menuitemname
	 * @return Patterns_Collection		The root menuitems for the default user
	 */
	public static function getDefaultMenuItem( $szModuleName, $szMenuItemName = "root" );
}
<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
interface RAD_Auth_Role_Factory_Interface
{
	/**
	 * creates the actiondbwriter
	 *
	 * @param  RAD_Auth_Role_Interface	$oRole		The role
	 * @return null
	 */
	public static function createActionDbWriter( $oRole );
	/**
	 * creates the MenuItemdbwriter
	 *
	 * @param  RAD_Auth_Role_Interface	$oRole		The role
	 * @return null
	 */
	public static function createMenuItemDbWriter( $oRole );
	/**
	 * creates the userdbwriter
	 *
	 * @param  RAD_Auth_User	$oUser		The user
	 * @return null
	 */
	public static function createUserDbWriter( $oUser );
	/**
	 * returns the guest role
	 *
	 * @return RAD_Auth_Role			The guest-role
	 */
	public static function getGuest();
}
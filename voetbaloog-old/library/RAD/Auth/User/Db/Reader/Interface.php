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
interface RAD_Auth_User_Db_Reader_Interface
{
	/**
	 * Creates a collection of objects from an event from the database
	 *
	 * @param RAD_Auth_Role_Interface 	$objRole	The Role
	 * @param Patterns_Collection_Interface		$oOptions	The constructionOptions
	 * @return Patterns_Collection_Interface	The Users
	 */
	public function createObjectsForRole( $objRole, $oOptions = null );
}
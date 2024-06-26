<?php

/**
 *
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
 * @package    Auth
 */
interface RAD_Auth_Role_Interface
{
	/**
	 * gets the system property, tells if the role can be changed by anyone
	 *
	 * @return bool
	 */
	public function getSystem();
	/**
	 * puts the system property, tells if the role can be changed by anyone
	 *
	 * @param  bool	$bSystem	The description of the menuitem
	 * @return null
	 */
	public function putSystem( $bSystem );
}
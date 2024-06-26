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
 * @package Auth
 */
interface RAD_Auth_Action_Interface
{
	/**
	 * gets the Name
	 *
	 * @return 	string	the Name
	 */
	public function getName();
	/**
	 * puts the Name
	 *
	 * @param string $szName the Name which will be set
	 * @return 	null
	 */
	public function putName( $szName );
	/**
	 * gets the Module
	 *
	 * @return 	string	the Module
	 */
	public function getModule();
	/**
	 * puts the Module
	 *
	 * @param string $szModule the Module which will be set
	 * @return 	null
	 */
	public function putModule( $szModule );
}
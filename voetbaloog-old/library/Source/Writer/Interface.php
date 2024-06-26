<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 *
 * @package	   Source
 */

/**
 *
 *
 * @package Source
 */
interface Source_Writer_Interface
{
	/**
	 * gets the objectproperties which should be written
	 *
	 * @return Patterns_Collection_Interface the objectproperties which should be written
	 */
	public function getObjectPropertiesToWrite();
	/**
	 * write things to the source
	 *
	 * @return bool true if succeeded, else false
	 */
	public function write();
}
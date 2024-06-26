<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Construction
 */

/**
 * @package    Construction
 */
interface Construction_Option_Interface
{
	/**
	 * gets the ObjectProperty
	 *
	 * @return  string	the ObjectProperty
	 */
	public function getObjectProperty();
	/**
	 * puts the ObjectProperty
	 *
	 * @param  string		$sObjectProperty	The ObjectProperty
	 * @return  null
	 */
	public function putObjectProperty( $sObjectProperty );
}
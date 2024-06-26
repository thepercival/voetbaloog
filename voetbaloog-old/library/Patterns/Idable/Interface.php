<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Patterns
 */

/**
 * @package    Patterns
 */
interface Patterns_Idable_Interface
{
	/**
	 * gets the Id
	 *
	 * @return  mixed	the Id
	 */
	public function getId();
	/**
	 * puts the Id
	 *
	 * @param  mixed		$vtId	The Id Default a integer is used.
	 * @return  null
	 */
	public function putId( $vtId );
}
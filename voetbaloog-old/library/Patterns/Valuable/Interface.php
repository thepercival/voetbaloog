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
 * @package    Patterns
 */

/**
 * @package    Patterns
 */
interface Patterns_Valuable_Interface
{
	/**
	 * gets the Value
	 *
	 * @return  mixed	the Value
	 */
	public function getValue();
	/**
	 * puts the Value
	 *
	 * @param  mixed		$vtValue	The Value
	 * @return  null
	 */
	public function putValue( $vtValue );
}
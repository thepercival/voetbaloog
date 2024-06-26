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
 * @package    Controls
 */

/**
 * @package    Controls
 */
interface Controls_Interface
{
	/**
	 * gets the sourceobject
	 *
	 * @return  mixed the sourceobject
	 */
	public function getSource();
	/**
	 * puts the sourceobject
	 *
	 * @param  Patterns_Idable_Interface	$objObject	the sourceobject
	 * @return null
	 */
	public function putSource( $objObject );
}
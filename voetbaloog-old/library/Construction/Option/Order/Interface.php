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
 * @package    Construction
 */

/**
 *
 *
 * @package    Construction
 */
interface Construction_Option_Order_Interface
{
	/**
	 * gets the order
	 *
	 * @return  bool		true for descending, false for ascending
	 */
	public function getDescending();
	/**
	 * puts the order
	 *
	 * @param  bool		$bDescending	true for descending, false for ascending
	 * @return  null
	 */
	public function putDescending( $bDescending );
}
<?php

/**
 * Interface.php
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 *
 * @package    Chart
 */

/**
 * @package    Chart
 */
interface RAD_Chart_Factory_Interface
{
	/**
	 * creates a barchart
	 *
	 * @return  string	an image html tag
	 */
	public static function createBar();
	/**
	 * creates a piechart
	 *
	 * @return  string	an image html tag
	 */
	public static function createPie();
}
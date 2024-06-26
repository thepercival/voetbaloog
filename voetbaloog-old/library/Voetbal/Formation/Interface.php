<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 955 2014-09-15 16:08:29Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Formation_Interface
{
	/**
	 * @return Patterns_Collection  the lines
	 */
	public function getLines();
	/**
	 * @return string the name
	 */
	public function getName();
}
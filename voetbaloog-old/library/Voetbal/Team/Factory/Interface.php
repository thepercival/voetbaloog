<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 871 2014-06-30 16:07:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Team_Factory_Interface
{
	/**
	 * creates the line
	 *
	 * @param   int     $nLine  the line
	 * @return 	Voetbal_Team_Line	the line
	 */
	public static function createLine( $nLine );
	/**
	 * gets the available lines
	 *
	 * @return 	Patterns_Collection		the available lines
	 */
	public static function getAvailableLines();
}
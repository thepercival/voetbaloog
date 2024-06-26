<?php

/**
 *

 *
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 *
 * @package VoetbalOog
 */
interface VoetbalOog_Pool_Payment_Factory_Interface
{
	/**
	 * gets the default payments
	 *
	 * @param VoetbalOog_Pool_Interface $oPool
	 * @return Patterns_Collection the default payments
	 */
	public static function createDefault( $oPool );	
}
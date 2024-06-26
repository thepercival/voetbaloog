<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 627 2013-12-15 20:18:35Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_QualifyRule_Factory_Interface
{
	/**
	 * gets the poulename
	 *
	 * @param 	int 	$nNrOfFromPoulePlace    the nr of from-places
	 * @param 	int 	$nNrOfToPoulePlaces	    the nr of to-places
	 * @param 	int 	$nConfigNr	            the confignr, default is 1
	 * @return 	array	   the config
	 */
	public static function getConfig( $nNrOfFromPoulePlace, $nNrOfToPoulePlaces, $nConfigNr = 1 );
}
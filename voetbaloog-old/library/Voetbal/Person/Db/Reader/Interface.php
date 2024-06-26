<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Person_Db_Reader_Interface
{
	/**
	 * haal de topscorers op
	 *
	 * @param 	Construction_Option_Collection	$oOptions		the options
	 * @return 	Patterns_Collection
	 */
	public function getTopscorers( Construction_Option_Collection $oOptions = null );
}
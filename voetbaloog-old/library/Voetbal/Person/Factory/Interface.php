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
interface Voetbal_Person_Factory_Interface
{
	/**
	 * haal de topscorers op
	 *
	 * @param 	Construction_Option_Collection	$oOptions		the options
	 * @return 	Patterns_Collection
	 */
	public static function getTopscorers( Construction_Option_Collection $oOptions = null ): Patterns_Collection;
	/**
	 * checks if name is nameinsertion
	 *
	 * @param 	string	$sNameInsertion		the NameInsertion
	 * @return 	bool
	 */
	public static function isNameInsertion( $sNameInsertion );
	/**
	 * haal array met voornaam, achternaam en tussenvoegsel uit naam
	 *
	 * @param 	string	$sName		the name
	 * @return 	array
	 */
	public static function getNameParts( $sName );
}
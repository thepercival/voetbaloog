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
interface Voetbal_CompetitionSeason_Factory_Interface
{
	/**
	 * create published competitionseasons which are (not yet) started and (not yet) ended
	 *
	 * @param 	bool|null 	    	                $bStarted	if the competitionseason has started
	 * @param 	bool|null     		                $bEnded		if the competitionseason has ended
	 * @param 	Construction_Option_Collection	$oOptions	the default is null
	 * @return 	Patterns_Collection
	 */
	public static function createObjectsFromDatabaseCustom( $bStarted, $bEnded, Construction_Option_Collection $oOptions = null ): Patterns_Collection;
	/**
	 * create competitionseasons which have teams placed
	 *
	 * @param 	Construction_Option_Collection		$oOptions	the default is null
	 * @return 	Patterns_Collection
	 */
	public static function createObjectsFromDatabaseWithTeams( Construction_Option_Collection $oOptions = null ): Patterns_Collection;
}
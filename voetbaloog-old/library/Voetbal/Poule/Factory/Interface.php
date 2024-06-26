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
interface Voetbal_Poule_Factory_Interface
{
	/**
	 * gets the poulename
	 *
	 * @param 	int 	$nNrOfRounds	the number of rounds
	 * @param 	int 	$nRoundNumber	the number of rounds
	 * @param 	int 	$nNrOfPoules	the number of rounds
	 * @param 	int 	$nNrOfTeams	the number of rounds
	 * @return 	string	the roundname
	 */
	public static function getPouleName( $nNrOfRounds, $nRoundNumber, $nNrOfPoules, $nNrOfTeams );
	/**
	 * gets the number of games
	 *
	 * @param 	int 	$nNrOfPoulePlaces	the number of pouleplaces
	 * @param 	int 	$bSemiCompetition	full or semi competition
	 * @return 	int		the number of games
	 */
	public static function getNrOfGames( $nNrOfPoulePlaces, $bSemiCompetition );
}
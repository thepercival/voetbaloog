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
interface Voetbal_CompetitionSeason_Db_Reader_Interface
{
	/**
	 * create published competitionseasons which are (not yet) started and (not yet) ended
	 *
	 * @param 	bool|null 		                $bStarted	if the competitionseason has started
	 * @param 	bool|null                		$bEnded		if the competitionseason has ended
	 * @param 	Construction_Option_Collection	$oOptions	the default is null
	 * @return 	Patterns_Collection
	 */
	public function createObjectsCustom( $bStarted, $bEnded, Construction_Option_Collection $oOptions = null ): Patterns_Collection;
	/**
	 * create competitionseasons which have teams placed
	 *
	 * @param 	Construction_Option_Collection		$oOptions	the default is null
	 * @return 	Patterns_Collection
	 */
	public function createObjectsWithTeams( Construction_Option_Collection $oOptions = null ): Patterns_Collection;
	/**
	 * get last pouleround numer
	 *
	 * @param 	Voetbal_CompetitionSeason	$oCompetitionSeason	the competitionseason
	 * @return 	int
	 */
	public function getLastPouleRoundNumber( $oCompetitionSeason );
}
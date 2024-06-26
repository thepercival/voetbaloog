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
interface Voetbal_Round_Interface
{
	/**
	 * gets the Name
	 *
	 */
	public function getName();
	/**
	 * puts the Name
	 *
	 * @param string $sName the Name which will be set
	 * @return 	null
	 */
	public function putName( $sName );
	/**
	 * gets the display name
	 *
	 * @return 	string	the displayName
	 */
	public function getDisplayName();
	/**
	 * gets the Poules
	 *
	 * @return 	Patterns_Collection	The Poules
	 */
	public function getPoules();
	/**
	 * gets the PoulePlaces
	 *
	 * @return 	Patterns_Collection	The Places
	 */
	public function getPoulePlaces();
	/**
	 * gets the Teams
	 *
	 * @return 	Patterns_Collection	The Teams
	 */
	public function getTeams();
	/**
	 * gets the Games
	 *
	 * @param 	bool 							$bByDate sorteervolgorde
	 * @param 	Construction_Option_Collection 	$oOptions
	 * @return 	Patterns_Collection	The Games
	 */
	public function getGames( $bByDate = false, $oOptions = null );
	/**
	 * gets the CompetitionSeason
	 *
	 * @return 	Voetbal_CompetitionSeason_Interface	the CompetitionSeason
	 */
	public function getCompetitionSeason();
	/**
	 * puts the CompetitionSeason
	 *
	 * @param Voetbal_CompetitionSeason_Interface $oCompetitionSeason the CompetitionSeason which will be set
	 * @return 	null
	 */
	public function putCompetitionSeason( $oCompetitionSeason );
	/**
	 * gets the Number
	 *
	 * @return 	int	the Number
	 */
	public function getNumber();
	/**
	 * puts the Number
	 *
	 * @param int $nNumber the Number which will be set
	 * @return 	null
	 */
	public function putNumber( $nNumber );
	/**
	 * true if is first round
	 *
	 * @return 	bool True if it is the first round
	 */
	public function isFirstRound();
	/**
	 * true if is last round
	 *
	 * @return 	bool True if it is the last round
	 */
	public function isLastRound();
	/**
	 * gets the SemiCompetition
	 *
	 * @return 	bool	the SemiCompetition
	 */
	public function getSemiCompetition();
	/**
	 * puts the SemiCompetition
	 *
	 * @param bool $bSemiCompetition the SemiCompetition which will be set
	 * @return 	null
	 */
	public function putSemiCompetition( $bSemiCompetition );
	/**
	 * gets the state
	 *
	 * @return 	int	the state
	 */
	public function getState();
	/**
	* gets if the poule needs ranking
	*
	* @return 	bool	if needs ranking
	*/
	public function needsRanking();
}
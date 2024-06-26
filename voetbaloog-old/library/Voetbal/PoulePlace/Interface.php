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
interface Voetbal_PoulePlace_Interface
{
	/**
	 * gets the display name
	 *
	 * @return 	string	the displayName
	 */
	public function getDisplayName();
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
	 * gets the Poule
	 *
	 * @return 	Voetbal_Poule_Interface	the Poule
	 */
	public function getPoule();
	/**
	 * puts the Poule
	 *
	 * @param Voetbal_Poule_Interface $oPoule the Poule which will be set
	 * @return 	null
	 */
	public function putPoule( $oPoule );
	/**
	 * gets the Team
	 *
	 * @return 	Voetbal_Team	the Team
	 */
	public function getTeam();
	/**
	 * puts the Team
	 *
	 * @param Voetbal_Team $oTeam the Team which will be set
	 * @return 	null
	 */
	public function putTeam( $oTeam );
	/**
	 * get the from QualifyRule
	 *
	 * @return 	Voetbal_QualifyRule_PoulePlace	the From QualifyRule
	 */
	public function getFromQualifyRule();
	/**
	 * get the to QualifyRule
	 *
	 * @return 	Voetbal_QualifyRule_PoulePlace|null	the to QualifyRule
	 */
	public function getToQualifyRule(): ?Voetbal_QualifyRule_PoulePlace;
	/**
	 * gets the Games
	 *
	 * @return 	Patterns_Collection	the Games
	 */
	public function getGames(): Patterns_Collection;
	/**
	 * gets the NrOfPlayedGames
	 *
	 * @param	Patterns_Collection	$oGames			the games which to check
	 * @param	int					$nGameStates	the gamestates to check
	 * @return 	int	the NrOfPlayedGames
	 */
	public function getNrOfPlayedGames( $oGames, $nGameStates = Voetbal_Factory::STATE_PLAYED );
	/**
	 * gets the Points
	 *
	 * @param	Patterns_Collection	$oGames			the games which to check
	 * @param	int					$nGameStates	the gamestates to check
	 * @return 	int	the Points
	 */
	public function getPoints( $oGames, $nGameStates = Voetbal_Factory::STATE_PLAYED );
	/**
	 * gets the GoalDifference
	 *
	 * @param	Patterns_Collection	$oGames			the games which to check
	 * @param	int					$nGameStates	the gamestates to check
	 * @return 	int	the GoalDifference
	 */
	public function getGoalDifference( $oGames, $nGameStates = Voetbal_Factory::STATE_PLAYED );
	/**
	 * gets the NrOfGoalsScored
	 *
	 * @param	Patterns_Collection	$oGames			the games which to check
	 * @param	int					$nGameStates	the gamestates to check
	 * @return 	int	the NrOfGoalsScored
	 */
	public function getNrOfGoalsScored( $oGames, $nGameStates = Voetbal_Factory::STATE_PLAYED );
	/**
	 * gets the NrOfGoalsReceived
	 *
	 * @param	Patterns_Collection	$oGames			the games which to check
	 * @param	int					$nGameStates	the gamestates to check
	 * @return 	int	the NrOfGoalsReceived
	 */
	public function getNrOfGoalsReceived( $oGames, $nGameStates = Voetbal_Factory::STATE_PLAYED );
	/**
	 * gets the Ranking
	 *
	 * @return 	int	the Ranking
	 */
	public function getRanking();
	/**
	 * puts the Ranking
	 *
	 * @param int $nRanking the Ranking which will be set
	 * @return 	null
	 */
	public function putRanking( $nRanking );
	/**
	* gets the PenaltyPoints
	*
	* @return 	int	the PenaltyPoints
	*/
	public function getPenaltyPoints();
	/**
	 * puts the PenaltyPoints
	 *
	 * @param int $nPenaltyPoints the PenaltyPoints which will be set
	 * @return 	null
	 */
	public function putPenaltyPoints( $nPenaltyPoints );
}
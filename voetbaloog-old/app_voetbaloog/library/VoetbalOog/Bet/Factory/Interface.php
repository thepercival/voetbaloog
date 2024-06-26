<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
interface VoetbalOog_Bet_Factory_Interface
{
	/**
	 * create a bet of type score
	 *
	 * @return 	VoetbalOog_Bet_Score_Interface	the Bet
	 */
	public static function createScore();
	/**
	 * create a bet of type result
	 *
	 * @return 	VoetbalOog_Bet_Result_Interface	the Bet
	 */
	public static function createResult();
	/**
	 * create a bet of type Qualify
	 *
	 * @return 	VoetbalOog_Bet_Qualify_Interface	the Bet
	 */
	public static function createQualify();
	/**
	 * get the bets by roundbetconfig, than by gameid or pouleplaceid
	 *
	 * @param 	VoetbalOog_Pool_User	$oPoolUser	the pooluser
	 * @return 	Patterns_Collection	the bets
	 */
	public static function createObjectsForPoolUserFromDatabase( $oPoolUser );
	/**
	 * haal beste bets results en bets standen streaks op voor aantal pools
	 *
	 * @param	int								$nBetType		the bettype
	 * @param	boolean							$bCorrect		if is good or bad
	 * @param 	Construction_Option_Collection	$oOptions		the options
	 * @return 	Patterns_Collection
	 */
	public static function getStreaksFromDatabase( $nBetType, $bCorrect, $oOptions = null );
	/**
	 * haal beste bets qualifying op voor aantal pools
	 *
	 * @param	boolean							$bCorrect		if is good or bad
	 * @param 	Construction_Option_Collection	$oOptions		the options
	 * @return 	Patterns_Collection
	 */
	public static function getQualifyingFromDatabase( $bCorrect, $oOptions = null );
	/**
	 * get points from bets
	 *
	 * @param 	VoetbalOog_Pool_User	$oPoolUser	the pooluser
	 * @param 	Voetbal_Round		$oRound		the round
	 * @return 	int	the points
	 */
	public static function getPoints( $oPoolUser, $oRound = null );
	/**
	 * get result from goals
	 *
	 * @param 	int		$nHomeGoals		the homegoals
	 * @param 	int		$nAwayGoals		the awaygoals
	 * @return 	int		the result
	 */
	public static function getResult( $nHomeGoals, $nAwayGoals );
}
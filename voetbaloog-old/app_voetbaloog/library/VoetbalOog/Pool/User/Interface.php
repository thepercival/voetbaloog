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
interface VoetbalOog_Pool_User_Interface
{
	/**
	 * gets the Pool
	 *
	 * @return 	VoetbalOog_Pool_Interface	the Pool
	 */
	public function getPool();
	/**
	 * puts the Pool
	 *
	 * @param VoetbalOog_Pool_Interface $oPool the Pool which will be set
	 * @return 	null
	 */
	public function putPool( $oPool );
	/**
	 * gets the User
	 *
	 * @return 	VoetbalOog_User_Interface	the User
	 */
	public function getUser();
	/**
	 * puts the User
	 *
	 * @param VoetbalOog_User_Interface $oUser the User which will be set
	 * @return 	null
	 */
	public function putUser( $oUser );
	/**
	 * gets the Admin
	 *
	 * @return 	bool	the Admin
	 */
	public function getAdmin();
	/**
	 * puts the Admin
	 *
	 * @param bool $bAdmin the Admin which will be set
	 * @return 	null
	 */
	public function putAdmin( $bAdmin );
	/**
	 * gets the Paid
	 *
	 * @return 	bool	the Paid
	 */
	public function getPaid();
	/**
	 * puts the Paid
	 *
	 * @param bool $bPaid the Paid which will be set
	 * @return 	null
	 */
	public function putPaid( $bPaid );
	/**
	 * gets the points for a round or total
	 *
	 * @param	Voetbal_Round 	$oRound the default is null
	 * @return 	int	the points
	 */
	public function getPoints( $oRound = null );
	/**
	 * gets the ranking
	 *
	 * @return 	int	the ranking
	 */
	public function getRanking();
	/**
	 * puts the ranking
	 *
	 * @param 	int $nRanking the ranking which will be set
	 * @return 	null
	 */
	public function putRanking( $nRanking );
	/**
	 * gets the number of Bets
	 *
	 * @return 	int	the number of Bets
	 */
	public function getNrOfBets();
	/**
	 * gets the number of wins
	 * true		: only previous
	 * false	: previous and this included, if played
	 *
	 * @param bool $bOnlyPrevious  which wins
	 * @return 	int	number of previous wins
	 */
	public function getNrOfWins( $bOnlyPrevious = true );
	/**
	 * gets the bets for a roundbetconfig
	 *
	 * @param	VoetbalOog_Round_BetConfig 	$oRoundBetConfig the default is null
	 * @return 	Patterns_Collection	the bets
	 */
	public function getBets( $oRoundBetConfig = null );
}
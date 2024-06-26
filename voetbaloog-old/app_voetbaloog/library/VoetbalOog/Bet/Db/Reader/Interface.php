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
interface VoetbalOog_Bet_Db_Reader_Interface
{
	/**
	 * get the bets by roundbetconfig, than by gameid or pouleplaceid
	 *
	 * @param 	VoetbalOog_Pool_User	$oPoolUser	the pooluser
	 * @return 	Patterns_Collection	the bets
	 */
	public function createObjectsForPoolUser( $oPoolUser );
	/**
	 * haal beste bets results en bets standen streaks op voor aantal pools
	 *
	 * @param	int								$nBetType		the bettype
	 * @param	boolean							$bCorrect		if is good or bad
	 * @param 	Construction_Option_Collection  $oOptions		the options
	 * @return 	Patterns_Collection
	 */
	public function getStreaks( $nBetType, $bCorrect, $oOptions = null );
	/**
	 * haal beste bets qualifying op voor aantal pools
	 *
	 * @param	boolean							$bCorrect		if is good or bad
	 * @param 	Construction_Option_Collection	$oOptions		the options
	 * @return 	Patterns_Collection
	 */
	public function getQualifying( $bCorrect, $oOptions = null );
	/**
	 * get points from bets
	 *
	 * @param 	VoetbalOog_Pool_User	$oPoolUser	the pooluser
	 * @return 	array	the points
	 */
	public function getPoints( $oPoolUser );
}
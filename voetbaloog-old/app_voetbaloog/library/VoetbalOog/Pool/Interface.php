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
interface VoetbalOog_Pool_Interface
{
	/**
	 * gets the Name
	 *
	 * @return 	string	the Name
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
	 * gets the CompetitionSeason
	 *
	 * @return 	Voetbal_CompetitionSeason_Interface	the CompetitionSeason
	 */
	public function getCompetitionSeason();
	/**
	 * puts the CompetitionSeason
	 *
	 * @param Voetbal_CompetitionSeason_Interface $oCompetitionSeason the CompetitionSeason
	 * @return 	null
	 */
	public function putCompetitionSeason( $oCompetitionSeason );
	/**
	 * gets the users
	 *
	 * @param	bool	$bByRanking  the ranking ( default is false )
	 * @return 	Patterns_Collection	the Users
	 */
	public function getUsers( $bByRanking = false );
	/**
	 * gets the payments
	 *
	 * @return 	Patterns_Collection	the Payments
	 */
	public function getPayments();
	/**
	 * gets the Picture
	 *
	 * @return 	string|null	The Picture
	 */
	public function getPicture();
	/**
	 * gets the Picture
	 *
	 * @param 	string|null	$vtPicture	The Picture
	 * @return 	null
	 */
	public function putPicture( $vtPicture );
	/**
	 * gets the nr of available bets
	 *
	 * @param Voetbal_Round $oRound     for the round which will be given
	 * @param bool          $bAsArray   as array
	 * @return 	int
	 */
	public function getNrOfAvailableBets( $oRound = null, $bAsArray = false);
	/**
	 * gets the Stake
	 *
	 * @return 	string	the Stake
	 */
	public function getStake();
	/**
	 * puts the Stake
	 *
	 * @param int $nStake the Stake which will be set
	 * @return 	null
	 */
	public function putStake( $nStake );
	/**
	 * gets the BetConfigs
	 *
	 * @param	Voetbal_Round			$oRound = null	The round
	 * @return 	Patterns_Collection			The BetConfigs
	 */
	public function getBetConfigs( $oRound = null );
	/**
	 * gets the BetTypes
	 *
	 * @param	Voetbal_Round			$oRound = null	The round
	 * @return 	int										The BetTypes
	 */
	public function getBetTypes( $oRound );
}
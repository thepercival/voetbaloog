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
 *	Overzicht mogelijkheden:
 *	 Ronde			BetType				BetTime
 *	 =====			=======				=======
 *	 1ste ronde 	resultaat/score		voor het begin van de wedstrijd
 *										voor het begin van ronde
 *	 tussen ronde 	resultaat/score		voor het begin van de wedstrijd
 *	 									voor het begin van de ronde
 *	 				gekwalificeerden	voor het begin van het toernooi
 *	 									voor de eerste kwalificatiewedstrijd
 *	 laatste ronde	gekwalificeerden	voor het begin van het toernooi
 *	 									voor de eerste kwalificatiewedstrijd
 *
 *	 BetType i.c.m. BetTime moet gelijk zijn voor alle ronden!!!!!
 *
 * @package VoetbalOog
 */
interface VoetbalOog_Round_BetConfig_Interface
{
	/**
	 * gets the Round
	 *
	 * @return 	string	the Round
	 */
	public function getRound();
	/**
	 * puts the Round
	 *
	 * @param Voetbal_Round $oRound the Round which will be set
	 * @return 	null
	 */
	public function putRound( $oRound );
	/**
	 * gets the BetType
	 *
	 * @return 	int	the BetType
	 */
	public function getBetType();
	/**
	 * puts the BetType
	 *
	 * @param int $nBetType the BetType which will be set
	 * @return 	null
	 */
	public function putBetType( $nBetType );
	/**
	 * gets the BetTime
	 *
	 * @return 	int	the BetTime
	 */
	public function getBetTime();
	/**
	 * puts the BetTime
	 *
	 * @param int $nBetTime the BetTime which will be set
	 * @return 	null
	 */
	public function putBetTime( $nBetTime );
	/**
	 * gets the Points
	 *
	 * @return 	string	the Points
	 */
	public function getPoints();
	/**
	 * puts the Points
	 *
	 * @param int $nPoints the Points which will be set
	 * @return 	null
	 */
	public function putPoints( $nPoints );
	/**
	* gets the deadline
	*
	* @param Voetbal_Game 	$oGame 		the game
	* @return 	null
	*/
	public function getDeadLine( $oGame = null );
	/**
	 * gets the Pool
	 *
	 * @return 	string	the Pool
	 */
	public function getPool();
	/**
	 * puts the Pool
	 *
	 * @param VoetbalOog_Pool_Interface $oPool the Pool which will be set
	 * @return 	null
	 */
	public function putPool( $oPool );
}
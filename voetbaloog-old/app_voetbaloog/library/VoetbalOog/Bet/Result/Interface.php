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
interface VoetbalOog_Bet_Result_Interface
{
	/**
	 * gets the Game
	 *
	 * @return 	Voetbal_Game_Interface	the Game
	 */
	public function getGame();
	/**
	 * puts the Game
	 *
	 * @param Voetbal_Game_Interface $oGame the Game which will be set
	 * @return 	null
	 */
	public function putGame( $oGame );
	/**
	 * gets the Result
	 *
	 * @return 	int	the Result
	 */
	public function getResult();
	/**
	 * puts the Result
	 *
	 * @param int $nResult the Result which will be set
	 * @return 	null
	 */
	public function putResult( $nResult );
}
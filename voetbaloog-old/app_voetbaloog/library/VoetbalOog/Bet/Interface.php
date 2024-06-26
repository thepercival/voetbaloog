<?php

/**
 *

 *
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 *
 * @package VoetbalOog
 */
interface VoetbalOog_Bet_Interface
{
	/**
	 * gets the PoolUser
	 *
	 * @return 	VoetbalOog_Pool_User_Interface	the PoolUser
	 */
	public function getPoolUser();
	/**
	 * puts the PoolUser
	 *
	 * @param VoetbalOog_Pool_User_Interface $oPoolUser the PoolUser which will be set
	 * @return 	null
	 */
	public function putPoolUser( $oPoolUser );
	/**
	 * gets the RoundBetConfig
	 *
	 * @return 	VoetbalOog_Pool_User_Interface	the RoundBetConfig
	 */
	public function getRoundBetConfig();
	/**
	 * puts the RoundBetConfig
	 *
	 * @param VoetbalOog_Pool_User_Interface $oRoundBetConfig the RoundBetConfig which will be set
	 * @return 	null
	 */
	public function putRoundBetConfig( $oRoundBetConfig );
	/**
	 * gets the if correct
	 *
	 * @return 	bool 		if bets is correct
	 */
	public function getCorrect();
	/**
	 * puts if correct
	 *
	 * @param bool			$bCorrect		if is correct
	 * @return 	null
	 */
	public function putCorrect( $bCorrect );
	/**
	* checks if are equal
	*
	* @param 	Patterns_Idable_Interface		$oObject		the compare object
	* @return 	bool		true | false
	*/
	public function isCorrect( $oObject );
	/**
	 * gets the Name
	 *
	 * @return 	string	the Name
	 */
	public function getName();
	/**
	 * gets the points
	 *
	 * @return 	int	the points
	 */
	public function getPoints();
	/**
	* gets the deadline
	*
	* @return 	null
	*/
	public function getDeadLine();
}
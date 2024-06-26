<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 929 2014-08-31 18:12:20Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Goal_Interface
{
	/**
	 * gets the GameParticipation
	 *
	 * @return 	Voetbal_Game_Participation	the GameParticipation
	 */
	public function getGameParticipation();
	/**
	 * puts the GameParticipation
	 *
	 * @param Voetbal_Game_Participation $oGameParticipation the GameParticipation which will be set
	 * @return 	null
	 */
	public function putGameParticipation( $oGameParticipation );
	/**
	* gets the Minute
	*
	* @return 	int	the Minute
	*/
	public function getMinute();
	/**
	 * puts the Minute
	 *
	 * @param int $nMinute the Minute which will be set
	 * @return 	null
	 */
	public function putMinute( $nMinute );
	/**
	 * gets the OwnGoal
	 *
	 * @return 	bool	if OwnGoal
	 */
	public function getOwnGoal();
	/**
	 * puts the OwnGoal
	 *
	 * @param bool $bOwnGoal the OwnGoal which will be set
	 * @return 	null
	 */
	public function putOwnGoal( $bOwnGoal );
	/**
	 * gets the Penalty
	 *
	 * @return 	bool	if Penalty
	 */
	public function getPenalty();
	/**
	 * puts the Penalty
	 *
	 * @param bool $bPenalty the Penalty which will be set
	 * @return 	null
	 */
	public function putPenalty( $bPenalty );
    /**
     * gets the AssistGameParticipation
     *
     * @return 	Voetbal_Game_Participation	the AssistGameParticipation
     */
    public function getAssistGameParticipation();
    /**
     * puts the AssistGameParticipation
     *
     * @param Voetbal_Game_Participation $oAssistGameParticipation the AssistGameParticipation which will be set
     * @return 	null
     */
    public function putAssistGameParticipation( $oAssistGameParticipation );
}
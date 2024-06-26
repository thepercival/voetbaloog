<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 927 2014-08-30 08:55:15Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Game_Participation_Interface
{
	/**
	 * gets the Game
	 *
	 * @return 	Voetbal_Game	the Game
	 */
	public function getGame();
	/**
	 * puts the Game
	 *
	 * @param Voetbal_Game $oGame the Game which will be set
	 * @return 	null
	 */
	public function putGame( $oGame );
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
	* gets the TeamMembershipPlayer
	*
	* @return 	Voetbal_Team_Membership_Player	the TeamMembershipPlayer
	*/
	public function getTeamMembershipPlayer();
	/**
	 * puts the TeamMembershipPlayer
	 *
	 * @param Voetbal_Team_Membership_Player $oTeamMembershipPlayer the TeamMembershipPlayer which will be set
	 * @return 	null
	 */
	public function putTeamMembershipPlayer( $oTeamMembershipPlayer );
	/**
	 * gets the YellowCardOne
	 *
	 * @return 	int	the YellowCardOne
	 */
	public function getYellowCardOne();
	/**
	 * puts the YellowCardOne
	 *
	 * @param int $nYellowCardOne the YellowCardOne which will be set
	 * @return 	null
	*/
	public function putYellowCardOne( $nYellowCardOne );
	/**
	 * gets the YellowCardTwo
	 *
	 * @return 	int	the YellowCardTwo
	 */
	public function getYellowCardTwo();
	/**
	 * puts the YellowCardTwo
	 *
	 * @param int $nYellowCardTwo the YellowCardTwo which will be set
	 * @return 	null
	*/
	public function putYellowCardTwo( $nYellowCardTwo );
	/**
	 * gets the RedCard
	 *
	 * @return 	int	the RedCard
	 */
	public function getRedCard();
	/**
	 * puts the RedCard
	 *
	 * @param int $nRedCard the RedCard which will be set
	 * @return 	null
	*/
	public function putRedCard( $nRedCard );
	/**
	 * gets the In
	 *
	 * @return 	int	the In
	 */
	public function getIn();
	/**
	 * puts the In
	 *
	 * @param int $nIn the In which will be set
	 * @return 	null
	*/
	public function putIn( $nIn );
	/**
	 * gets the Out
	 *
	 * @return 	int	the Out
	 */
	public function getOut();
	/**
	 * puts the Out
	 *
	 * @param int $nOut the Out which will be set
	 * @return 	null
	*/
	public function putOut( $nOut );
	/**
	 * gets the goals
	 *
	 * @return 	Patterns_Collection	the Goals
	 */
	public function getGoals();
}
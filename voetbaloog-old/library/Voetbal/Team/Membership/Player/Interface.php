<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 973 2014-12-20 21:23:08Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Team_Membership_Player_Interface
{
    /**
     * gets the Line
     *
     * @return 	Voetbal_Team_Line		the Line
     */
    public function getLine();
    /**
     * puts the Line
     *
     * @param Voetbal_Team_Line $oLine the line which will be set
     * @return 	null
     */
    public function putLine( $oLine );

	/**
	 * gets the BackNumber
	 *
	 * @return 	int			the BackNumber
	 */
	public function getBackNumber();
	/**
	 * puts the BackNumber
	 *
	 * @param int $nBackNumber the backnumber which will be set
	 * @return 	null
	 */
	public function putBackNumber( $nBackNumber );
	/**
	* gets the Games
	*
	* @return 	Patterns_Collection	The Games
	*/
	public function getGames();
	/**
	* gets the Goals
	*
	* @return 	Patterns_Collection	The Goals
	*/
	public function getGoals();
	/**
	 * gets the gamedetails
	 *
	 * @param Voetbal_Poule 		$oPoule                 the poule
	 * @param RAD_Range | int     	$vtGameNumberRange 		gamerange
	 * @return 	Patterns_Collection	        the gamedetails
	 */
	public function getGameDetails( Voetbal_Poule $oPoule, $vtGameNumberRange = null );
}
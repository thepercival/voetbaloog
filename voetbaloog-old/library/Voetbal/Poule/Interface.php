<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Poule_Interface
{
    /**
     * gets the Number
     *
     * @return 	int	the Number
     */
    public function getNumber();
    /**
     * puts the Number
     *
     * @param int $nNumber the Number which will be set
     * @return 	null
     */
    public function putNumber( $nNumber );
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
     * gets the display name
     *
     * @param bool  $bWithPrefix with the prefix
     * @return 	string	the displayName
     */
    public function getDisplayName( $bWithPrefix );
	/**
	 * gets the Places
	 *
	 * @param int $nGameStates	the gamestates which should be filtered on
	 * @return 	Patterns_Collection	The Places
	 */
	public function getPlaces( $nGameStates = Voetbal_Factory::STATE_PLAYED ): Patterns_Collection;
	/**
	 * gets the Places
	 *
	 * @param int $nGameStates	the gamestates which should be filtered on
	 * @return 	Patterns_Collection	The Places
	 */
	public function getPlacesByRank( $nGameStates = Voetbal_Factory::STATE_PLAYED ): Patterns_Collection;
	/**
	 * gets the Teams
	 *
	 * @return 	Patterns_Collection	The Teams
	 */
	public function getTeams(): Patterns_Collection;
	/**
	 * gets the Games
	 *
	 * @return 	Patterns_Collection	The Games
	 */
	public function getGames(): Patterns_Collection;
    /**
     * @param Construction_Option_Collection|null $oOptions
     * @return Patterns_Collection
     */
    public function getGamesByDate( Construction_Option_Collection $oOptions = null ): Patterns_Collection;
	/**
	 * gets the Round
	 *
	 * @return 	Voetbal_Round_Interface	the Round
	 */
	public function getRound();
	/**
	 * puts the Round
	 *
	 * @param Voetbal_Round_Interface $oRound the Round which will be set
	 * @return 	null
	 */
	public function putRound( $oRound );
	/**
	 * gets the state
	 *
	 * @return 	int	if state
	 */
	public function getState();
	/**
	* gets if the poule needs ranking
	*
	* @return 	bool	if needs ranking
	*/
	public function needsRanking();
    /**
     * if has the gameround the state specified
     *
     * @param int $nGameNumber the gamenumber
     * * @param int $nState the state
     * @return 	bool	if has the gameround the state specified
     */
    public function hasGameRoundState( int $nGameNumber, int $nState );
}
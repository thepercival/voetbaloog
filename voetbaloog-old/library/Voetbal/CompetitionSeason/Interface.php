<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 721 2014-02-05 19:01:34Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_CompetitionSeason_Interface
{
	/**
	 * gets the Season
	 *
	 * @return 	Voetbal_Season_Interface	the Season
	 */
	public function getSeason();
	/**
	 * puts the Season
	 *
	 * @param Voetbal_Season_Interface	$oSeason the Season
	 * @return 	null
	 */
	public function putSeason( $oSeason );
	/**
	 * gets the Competition
	 *
	 * @return 	Voetbal_Competition_Interface	the Competition
	 */
	public function getCompetition();
	/**
	 * puts the Competition
	 *
	 * @param Voetbal_Competition_Interface	$oCompetition the Competition
	 * @return 	null
	 */
	public function putCompetition( $oCompetition );
	/**
	 * gets the Abbreviation
	 * @return 	string	the Abbreviation
	 */
	public function getAbbreviation();
	/**
	 * gets the Name
	 *
	 * @return 	string	the Name
	 */
	public function getName();
	/**
	 * gets the Rounds
	 *
	 * @return 	Patterns_Collection	The Rounds
	 */
	public function getRounds(): Patterns_Collection;
	/**
	 * gets the Poules
	 *
	 * @return 	Patterns_Collection	The Poules
	 */
	public function getPoules(): Patterns_Collection;
	/**
	 * gets the Games
	 *
	 * @param 	bool $bByDate	order
	 * @param	Construction_Option_Collection	$oOptions The construction options
	 * @return 	Patterns_Collection	The Games
	 */
	public function getGames( $bByDate = false, Construction_Option_Collection $oOptions = null ): Patterns_Collection;
	/**
	 * gets if there are games
	 *
	 * @param	bool		$bWithoutStartDateTime default false
	 * @return 	bool	if there are games
	 */
	public function hasGames( $bWithoutStartDateTime = false );
	/**
	 * gets the previous Round, null if it is the first round
	 *
	 * @param 	Voetbal_Round	    $oRoundToFind   The Round To Find
	 * @return 	Voetbal_Round|null	The Previous Round
	 */
	public function getPreviousRound( Voetbal_Round $oRoundToFind ): ?Voetbal_Round;
	/**
	 * gets the next Round, null if there is no
	 *
	 * @param 	Voetbal_Round	    $oRoundToFind   The Round To Find
	 * @return 	Voetbal_Round|null	The Previous Round
	 */
	public function getNextRound( Voetbal_Round $oRoundToFind ): ?Voetbal_Round;
	/**
	 * gets the Teams
	 *
	 * @return 	Patterns_Collection     The Teams
	 */
	public function getTeams(): Patterns_Collection;
	/**
	 * gets the Teams which are still in the race for the championship
	 *
	 * @return 	Patterns_Collection	The Teams
	 */
	public function getTeamsInTheRace(): Patterns_Collection;
	/**
	 * gets the state
	 *
	 * @return 	int	the state
	 */
	public function getState();
	/**
	 * gets if Public
	 *
	 * @return 	bool	if Public
	 */
	public function getPublic();
	/**
	 * puts if Public
	 *
	 * @param 	bool	$bPublic puts if Public
	 * @return 	null
	 */
	public function putPublic( $bPublic );
	/**
	 * gets the BetConfigs
	 *
	 * @param	Voetbal_Round			$oRound = null	The round
	 * @return 	Patterns_Collection			The BetConfigs
	 */
	// public function getBetConfigs( $oRound = null ): Patterns_Collection;
	/**
	 * gets the BetTypes
	 *
	 * @param	Voetbal_Round			$oRound	The round
	 * @return 	int						The BetTypes
	 */
	// public function getBetTypes( $oRound );
	/**
	 * gets the Association
	 *
	 * @return 	Voetbal_Association_Interface	the Association
	 */
	public function getAssociation();
	/**
	 * puts the Association
	 *
	 * @param Voetbal_Association_Interface	$oAssociation the Association
	 * @return 	null
	 */
	public function putAssociation( $oAssociation );
	/**
	 * gets the PromotionRule
	 *
	 * @return 	int		the PromotionRule
	 */
	public function getPromotionRule();
	/**
	 * puts the Competition
	 *
	 * @param int	$nPromotionRule the PromotionRule
	 * @return 	null
	 */
	public function putPromotionRule( $nPromotionRule );
	/**
	* gets the nrofminutesgame
	*
	* @return 	int	the nrofminutesgame
	*/
	public function getNrOfMinutesGame();
	/**
	 * puts the nrofminutesgame
	 *
	 * @param 	int	$nNrOfMinutesGame puts the nrofminutesgame
	 * @return 	null
	 */
	public function putNrOfMinutesGame( $nNrOfMinutesGame );
	/**
	* gets the nrofminutesextratime
	*
	* @return 	int	the nrofminutesextratime
	*/
	public function getNrOfMinutesExtraTime();
	/**
	 * puts the nrofminutesextratime
	 *
	 * @param 	int	$nNrOfMinutesExtraTime puts the nrofminutesextratime
	 * @return 	null
	 */
	public function putNrOfMinutesExtraTime( $nNrOfMinutesExtraTime );
	/**
	* gets the winpointsaftergame
	*
	* @return 	int	the winpointsaftergame
	*/
	public function getWinPointsAfterGame();
	/**
	 * puts the winpointsaftergame
	 *
	 * @param 	int	$nWinPointsAfterGame puts the winpointsaftergame
	 * @return 	null
	 */
	public function putWinPointsAfterGame( $nWinPointsAfterGame );
	/**
	* gets the winpointsafterextratime
	*
	* @return 	int	the winpointsafterextratime
	*/
	public function getWinPointsAfterExtraTime();
	/**
	 * puts the winpointsafterextratime
	 *
	 * @param 	int	$nWinPointsAfterExtraTime puts the winpointsafterextratime
	 * @return 	null
	 */
	public function putWinPointsAfterExtraTime( $nWinPointsAfterExtraTime );
	/**
	 * gets the ImageName
	 *
	 * @return 	string	the ImageName
	 */
	public function getImageName();
	/**
	* gets the Topscorers
	*
	* @param 	int 			$nMaxNrOfPersons	the max.
	* @return 	Patterns_Collection		The topscorers as persons
	*/
	public function getTopscorers( $nMaxNrOfPersons = null ): Patterns_Collection;
    /**
     * gets the Locations
     *
     * @return 	Patterns_Collection	The Locations
     */
    public function getLocations(): Patterns_Collection;
}
<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 970 2014-12-16 17:24:49Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Game_Interface
{
	/**
	 * gets the CompetitionSeason
	 *
	 * @return 	Voetbal_PoulePlace_Interface	the CompetitionSeason
	 */
	public function getCompetitionSeason();
	/**
	 * puts the CompetitionSeason
	 *
	 * @param Voetbal_CompetitionSeason_Interface $oCompetitionSeason the CompetitionSeason which will be set
	 * @return 	null
	 */
	public function putCompetitionSeason( $oCompetitionSeason );
	/**
	 * gets the HomePoulePlace
	 *
	 * @return 	Voetbal_PoulePlace_Interface	the HomePoulePlace
	 */
	public function getHomePoulePlace();
	/**
	 * puts the HomePoulePlace
	 *
	 * @param Voetbal_PoulePlace_Interface $oHomePoulePlace the HomePoulePlace which will be set
	 * @return 	null
	 */
	public function putHomePoulePlace( $oHomePoulePlace );
	/**
	 * gets the AwayPoulePlace
	 *
	 * @return 	Voetbal_PoulePlace_Interface	the AwayPoulePlace
	 */
	public function getAwayPoulePlace();
	/**
	 * puts the AwayPoulePlace
	 *
	 * @param 	Voetbal_PoulePlace_Interface $oAwayPoulePlace the AwayPoulePlace which will be set
	 * @return 	null
	 */
	public function putAwayPoulePlace( $oAwayPoulePlace );
	/**
	 * gets the Poule
	 *
	 * @return 	Voetbal_Poule_Interface	the Poule
	 */
	public function getPoule();
	/**
	 * gets the HomeGoals
	 *
	 * @return 	int	the HomeGoals
	 */
	public function getHomeGoals();
	/**
	 * puts the HomeGoals
	 *
	 * @param int $nHomeGoals the HomeGoals which will be set
	 * @return 	null
	 */
	public function putHomeGoals( $nHomeGoals );
	/**
	 * gets the AwayGoals
	 *
	 * @return 	int	the AwayGoals
	 */
	public function getAwayGoals();
	/**
	 * puts the AwayGoals
	 *
	 * @param int $nAwayGoals the AwayGoals which will be set
	 * @return 	null
	 */
	public function putAwayGoals( $nAwayGoals );
	/**
	* gets the HomeGoalsExtraTime
	*
	* @return 	int	the HomeGoalsExtraTime
	*/
	public function getHomeGoalsExtraTime();
	/**
	 * puts the HomeGoalsExtraTime
	 *
	 * @param int $nHomeGoalsExtraTime the HomeGoalsExtraTime which will be set
	 * @return 	null
	 */
	public function putHomeGoalsExtraTime( $nHomeGoalsExtraTime );
	/**
	 * gets the AwayGoalsExtraTime
	 *
	 * @return 	int	the AwayGoalsExtraTime
	 */
	public function getAwayGoalsExtraTime();
	/**
	 * puts the AwayGoalsExtraTime
	 *
	 * @param int $nAwayGoalsExtraTime the AwayGoalsExtraTime which will be set
	 * @return 	null
	 */
	public function putAwayGoalsExtraTime( $nAwayGoalsExtraTime );
	/**
	* gets the HomeGoalsPenalty
	*
	* @return 	int	the HomeGoalsPenalty
	*/
	public function getHomeGoalsPenalty();
	/**
	 * puts the HomeGoalsPenalty
	 *
	 * @param int $nHomeGoalsPenalty the HomeGoalsPenalty which will be set
	 * @return 	null
	 */
	public function putHomeGoalsPenalty( $nHomeGoalsPenalty );
	/**
	 * gets the AwayGoalsPenalty
	 *
	 * @return 	int	the AwayGoalsPenalty
	 */
	public function getAwayGoalsPenalty();
	/**
	 * puts the AwayGoalsPenalty
	 *
	 * @param int $nAwayGoalsPenalty the AwayGoalsPenalty which will be set
	 * @return 	null
	 */
	public function putAwayGoalsPenalty( $nAwayGoalsPenalty );
	/**
	 * gets the HomeNrOfCorners
	 *
	 * @return 	int	the HomeNrOfCorners
	 */
	public function getHomeNrOfCorners();
	/**
	 * puts the HomeNrOfCorners
	 *
	 * @param int $nHomeNrOfCorners the HomeNrOfCorners which will be set
	 * @return 	null
	*/
	public function putHomeNrOfCorners( $nHomeNrOfCorners );
	/**
	 * gets the AwayNrOfCorners
	 *
	 * @return 	int	the AwayNrOfCorners
	*/
	public function getAwayNrOfCorners();
	/**
	 * puts the AwayNrOfCorners
	 *
	 * @param int $nAwayNrOfCorners the AwayNrOfCorners which will be set
	 * @return 	null
	*/
	public function putAwayNrOfCorners( $nAwayNrOfCorners );
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
	* gets the calculated number of homegoals
	*
	* @param 	bool $bCountPenalties if penalties should be used, i.e. for goal diff
	* @return 	null
	*/
	public function getHomeGoalsCalc( $bCountPenalties = false );
	/**
	* gets the calculated number of awaygoals
	*
	* @param 	bool $bCountPenalties if penalties should be used, i.e. for goal diff
	* @return 	null
	*/
	public function getAwayGoalsCalc( $bCountPenalties = false );
	/**
	 * gets the State
	 *
	 * @return 	int			the state
	 */
	public function getState();
	/**
	 * puts the State
	 *
	 * @param int $nState the State which will be set
	 * @return 	null
	 */
	public function putState( $nState );
	/**
	 * gets the Location
	 *
	 * @return 	Voetbal_Location	the Location
	 */
	public function getLocation();
	/**
	 * puts the Location
	 *
	 * @param string $oLocation the location which will be set
	 * @return 	null
	 */
	public function putLocation( $oLocation );
	/**
	 * gets the ViewOrder
	 *
	 * @return 	int	the ViewOrder
	 */
	public function getViewOrder();
	/**
	 * puts the ViewOrder
	 *
	 * @param int $nViewOrder the ViewOrder which will be set
	 * @return 	null
	 */
	public function putViewOrder( $nViewOrder );
	/**
	* gets the participations
	*
	* @param 	Voetbal_Team $oTeam	the home- or away-team, default is null
	* @return 	Patterns_ObservableObject_Collection_Idable	the participations
	*/
	public function getParticipations( Voetbal_Team $oTeam = null ): Patterns_ObservableObject_Collection_Idable;
	/**
	* gets the goals
	*
	* @param 	int $nHomeAway	the home- or away, default is null
	* @return 	Patterns_ObservableObject_Collection	the goals
	*/
	public function getGoals( $nHomeAway = null ): Patterns_ObservableObject_Collection;
}
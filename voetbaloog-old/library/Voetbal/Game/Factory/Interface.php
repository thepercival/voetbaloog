<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 871 2014-06-30 16:07:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Game_Factory_Interface
{
	/**
	 * create game
	 *
	 * @param 	Agenda_DateTime			$oDateTime		the startdatetime
	 * @param 	Voetbal_PoulePlace		$oHomePP		the homepouleplace
	 * @param 	Voetbal_PoulePlace		$oAwayPP		the awaypouleplace
	 * @param 	string					$sExternId		the external id
	 * @param 	int						$nNumber		the gamenumber
	 * @param 	int						$nViewOrder		the vieworder
	 * @return 	Voetbal_Game	the game
	 */
    public static function createObjectExt( Agenda_DateTime $oDateTime, Voetbal_PoulePlace $oHomePP, Voetbal_PoulePlace $oAwayPP, $sExternId = null, $nNumber = 0, $nViewOrder = 0 ): Voetbal_Game;
	/**
	 * get the gamenumberrange from games which have a certai state
	 *
	 * @param 	Voetbal_Poule	$oPoule			the poule
	 * @param 	int				$nState			the state
	 * @param 	DateTime		$oStartDateTime	the startdatetime
	 * @param 	DateTime		$oEndDateTime	the enddatetime
	 * @return 	RAD_Range|null	the range
	 */
	public static function getNumberRange( $oPoule, $nState, $oStartDateTime = null, $oEndDateTime = null ): ?RAD_Range;
    /**
     * gets the state of the gamerounds within a poule
     *
     * @param 	Voetbal_Poule	$oPoule			the poule
     * @return 	array	the states
     */
    public static function getStateGameRounds( Voetbal_Poule $oPoule );
	/**
	 * get the game by number, poule and player
	 *
	 * @param 	Voetbal_Poule	                $oPoule			the poule
	 * @param 	int				                $nGameNumber	the gamenumber
	 * @param 	Voetbal_Team_Membership_Player	$oPlayer	    the player
	 * @param 	int                             $nState	        the state
	 * @return 	Voetbal_Game    the game
	 */
	public static function createObjectFromDatabaseCustom( $oPoule, $nGameNumber, $oPlayer, $nState = null );
}
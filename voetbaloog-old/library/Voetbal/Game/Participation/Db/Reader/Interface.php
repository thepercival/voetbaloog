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
interface Voetbal_Game_Participation_Db_Reader_Interface
{
	/**
	 * creates game details per gamenumber
	 *
	 * @param 	Voetbal_Team_Membership_Player	$oPlayerMembership	the player
	 * @param	Construction_Option_Collection	$oOptions			the options
	 * @return 	Patterns_Collection
	 */
	public function getDetails( Voetbal_Team_Membership_Player $oPlayerMembership, Construction_Option_Collection $oOptions ): Patterns_Collection;
	/**
	 * creates game detailstotals per gamenumber
	 *
	 * @param 	Voetbal_Team_Membership_Player	$oPlayerMembership	the player
	 * @param	Construction_Option_Collection  $oOptions			the options
	 * @return 	Patterns_Collection
	 */
	public function getDetailsTotals( Voetbal_Team_Membership_Player $oPlayerMembership, Construction_Option_Collection $oOptions ): Patterns_Collection;
}
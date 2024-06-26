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
interface Voetbal_Game_Participation_Factory_Interface
{
	/**
	 * creates game details per gamenumber
	 *
	 * @param	Construction_Option_Collection		$oOptions			the options
	 * @param 	Voetbal_Team_Membership_Player	    $oPlayerMembership	the player
	 * @param	bool								$bTotals			the totals
	 * @return 	Patterns_Collection
	 */
	public static function getDetails( Construction_Option_Collection $oOptions, Voetbal_Team_Membership_Player $oPlayerMembership, bool $bTotals ): Patterns_Collection;
}
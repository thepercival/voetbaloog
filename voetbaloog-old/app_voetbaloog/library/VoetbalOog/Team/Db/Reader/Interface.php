<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 670 2014-01-15 18:37:05Z thepercival $
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
interface VoetbalOog_Team_Db_Reader_Interface
{
	/**
	 * get the team which everyone betted on
	 *
	 * @param 	VoetbalOog_Round_BetConfig	$oRoundBetConfig	the roundbetconfig
	 * @return 	Patterns_Collection	the teams
	 */
	public function createSameObjects( $oRoundBetConfig );
}
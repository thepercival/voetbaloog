<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 871 2014-06-30 16:07:51Z thepercival $
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
interface VoetbalOog_Team_Factory_Interface
{
	/**
	 * get the team which everyone betted on
	 *
	 * @param 	VoetbalOog_Round_BetConfig	$oRoundBetConfig	the roundbetconfig
	 * @return 	Patterns_Collection	the teams
	 */
	public static function createSameObjectsFromDatabase( $oRoundBetConfig );

}
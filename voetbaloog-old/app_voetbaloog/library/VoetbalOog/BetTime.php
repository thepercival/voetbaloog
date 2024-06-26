<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: BetTime.php 1100 2016-04-29 10:39:23Z thepercival $
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
abstract class VoetbalOog_BetTime
{
	public static $nBeforeStartGame = 1;			// int
	public static $nBeforeStartRound = 2;			// int
	public static $nBeforeCompetitionSeason = 4; 	// int
	public static $nBeforeStartPreviousRound = 8;	// int

	public static function getDescription ( $nBetTime )
	{
		if ( $nBetTime === VoetbalOog_BetTime::$nBeforeStartGame )
			return 'begin wedstrijd';
		else if ( $nBetTime === VoetbalOog_BetTime::$nBeforeStartRound )
			return 'begin ronde';
		else if ( $nBetTime === VoetbalOog_BetTime::$nBeforeCompetitionSeason )
			return 'begin toernooi';
		else if ( $nBetTime === VoetbalOog_BetTime::$nBeforeStartPreviousRound )
			return 'begin vorige ronde';
		return null;
	}
}
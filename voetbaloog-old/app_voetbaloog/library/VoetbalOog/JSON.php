<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: JSON.php 997 2015-05-05 10:40:04Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_JSON
{
	public static $nPool_CompetitionSeason = 256;
	public static $nPool_BetConfigs = 512;
	public static $nCompetitionSeason_BetConfigs = 1024;
	public static $nPoolUser_Bets = 2048;
	public static $nPool_Users = 4096;
	public static $nPool_Payments = 8192;
	public static $nRoundBetConfig_SameTeams = 16384;
}
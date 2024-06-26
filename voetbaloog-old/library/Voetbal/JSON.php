<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: JSON.php 878 2014-06-30 22:19:47Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_JSON implements JsonSerializable
{
	public static $nCompetitionSeason_Rounds = 1;
	public static $nRound_Poules = 2;
	public static $nPoule_Games = 4;
	public static $nCompetitionSeason_Topscorers = 8;
	public static $nGame_Participations = 16;
	public static $nGame_Goals = 32;
	public static $nCompetitionSeason_TeamsInTheRace = 64;
	public static $nAssociation_Teams = 128;
	// 1 reserve 128

	public static $nState_Created = Voetbal_Factory::STATE_CREATED;
	public static $nState_Scheduled = Voetbal_Factory::STATE_SCHEDULED;
	public static $nState_InProgress = Voetbal_Factory::STATE_INPROGRESS;
	public static $nState_Played = Voetbal_Factory::STATE_PLAYED;

	public function jsonSerialize()
	{
		return get_class_vars(get_class($this));
	}
}

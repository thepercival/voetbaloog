<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 580 2013-11-20 15:28:51Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
final class VoetbalOog_Bet_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "Bets";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["VoetbalOog_Bet::Id"] = $oTable->createColumn( "Id" );
		$this["VoetbalOog_Bet::RoundBetConfig"] = $oTable->createColumn( "RoundBetConfigId" );
		$this["VoetbalOog_Bet::PoolUser"] = $oTable->createColumn( "UsersPerPoolId" );
		$this["VoetbalOog_Bet::Correct"] = $oTable->createColumn( "Correct" );
		$this["VoetbalOog_Bet_Score::Game"] = $oTable->createColumn( "GameId" );
		$this["VoetbalOog_Bet_Score::HomeGoals"] = $oTable->createColumn( "HomeGoals" );
		$this["VoetbalOog_Bet_Score::AwayGoals"] = $oTable->createColumn( "AwayGoals" );
		$this["VoetbalOog_Bet_Result::Game"] = $oTable->createColumn( "GameId" );
		$this["VoetbalOog_Bet_Result::Result"] = $oTable->createColumn( "Result" );
		$this["VoetbalOog_Bet_Qualify::PoulePlace"] = $oTable->createColumn( "PoulePlaceId" );
		$this["VoetbalOog_Bet_Qualify::Team"] = $oTable->createColumn( "TeamId" );
	}
}
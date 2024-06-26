<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 970 2014-12-16 17:24:49Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
final class Voetbal_Game_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "Games";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_Game::Id"] = $oTable->createColumn( "Id" );
		$this["Voetbal_Game::HomePoulePlace"] = $oTable->createColumn( "HomePoulePlaceId" );
		$this["Voetbal_Game::AwayPoulePlace"] = $oTable->createColumn( "AwayPoulePlaceId" );
		$this["Voetbal_Game::HomeGoals"] = $oTable->createColumn( "HomeGoals" );
		$this["Voetbal_Game::AwayGoals"] = $oTable->createColumn( "AwayGoals" );
		$this["Voetbal_Game::HomeGoalsExtraTime"] = $oTable->createColumn( "HomeGoalsExtraTime" );
		$this["Voetbal_Game::AwayGoalsExtraTime"] = $oTable->createColumn( "AwayGoalsExtraTime" );
		$this["Voetbal_Game::HomeGoalsPenalty"] = $oTable->createColumn( "HomeGoalsPenalty" );
		$this["Voetbal_Game::AwayGoalsPenalty"] = $oTable->createColumn( "AwayGoalsPenalty" );
		$this["Voetbal_Game::HomeNrOfCorners"] = $oTable->createColumn( "HomeNrOfCorners" );
		$this["Voetbal_Game::AwayNrOfCorners"] = $oTable->createColumn( "AwayNrOfCorners" );
		$this["Voetbal_Game::Number"] = $oTable->createColumn( "Number" );
		$this["Voetbal_Game::StartDateTime"] = $oTable->createColumn( "StartDateTime" );
		$this["Voetbal_Game::State"] = $oTable->createColumn( "State" );
		$this["Voetbal_Game::Location"] = $oTable->createColumn( "LocationId" );
		$this["Voetbal_Game::ViewOrder"] = $oTable->createColumn( "ViewOrder" );
		$this["Voetbal_Game::ValidatedDateTime"] = $oTable->createColumn( "ValidatedDateTime" );
		$this["Voetbal_Game::ExternId"] = $oTable->createColumn( "ExternId" );
	}
}
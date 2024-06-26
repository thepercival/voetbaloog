<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 921 2014-08-28 18:49:24Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
final class Voetbal_Game_Participation_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "PlayerPeriodsPerGame";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_Game_Participation::Id"] = $oTable->createColumn( "Id" );
		$this["Voetbal_Game_Participation::Game"] = $oTable->createColumn( "GameId" );
		$this["Voetbal_Game_Participation::Team"] = $oTable->createColumn( "TeamId" );
		$this["Voetbal_Game_Participation::TeamMembershipPlayer"] = $oTable->createColumn( "PlayerPeriodId" );
		$this["Voetbal_Game_Participation::YellowCardOne"] = $oTable->createColumn( "YellowCardOne" );
		$this["Voetbal_Game_Participation::YellowCardTwo"] = $oTable->createColumn( "YellowCardTwo" );
		$this["Voetbal_Game_Participation::RedCard"] = $oTable->createColumn( "RedCard" );
		$this["Voetbal_Game_Participation::In"] = $oTable->createColumn( "MinuteIn" );
		$this["Voetbal_Game_Participation::Out"] = $oTable->createColumn( "MinuteOut" );
	}
}
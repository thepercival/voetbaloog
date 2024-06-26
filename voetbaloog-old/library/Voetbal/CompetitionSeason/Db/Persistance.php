<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
final class Voetbal_CompetitionSeason_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "CompetitionsPerSeason";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_CompetitionSeason::Id"] = $oTable->createColumn( "Id" );
		$this["Voetbal_CompetitionSeason::Competition"] = $oTable->createColumn( "CompetitionId" );
		$this["Voetbal_CompetitionSeason::Season"] = $oTable->createColumn( "SeasonId" );
		$this["Voetbal_CompetitionSeason::Public"] = $oTable->createColumn( "Public" );
		$this["Voetbal_CompetitionSeason::Association"] = $oTable->createColumn( "AssociationId" );
		$this["Voetbal_CompetitionSeason::PromotionRule"] = $oTable->createColumn( "PromotionRule" );
		$this["Voetbal_CompetitionSeason::NrOfMinutesGame"] = $oTable->createColumn( "NrOfMinutesGame" );
		$this["Voetbal_CompetitionSeason::NrOfMinutesExtraTime"] = $oTable->createColumn( "NrOfMinutesExtraTime" );
		$this["Voetbal_CompetitionSeason::WinPointsAfterGame"] = $oTable->createColumn( "WinPointsAfterGame" );
		$this["Voetbal_CompetitionSeason::WinPointsAfterExtraTime"] = $oTable->createColumn( "WinPointsAfterExtraTime" );
		$this["Voetbal_CompetitionSeason::ExternId"] = $oTable->createColumn( "ExternId" );
	}
}
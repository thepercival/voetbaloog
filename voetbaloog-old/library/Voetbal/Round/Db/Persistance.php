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
final class Voetbal_Round_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "Rounds";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_Round::Id"] = $oTable->createColumn( "Id" );
		$this["Voetbal_Round::Name"] = $oTable->createColumn( "Name" );
		$this["Voetbal_Round::Number"] = $oTable->createColumn( "Number" );
		$this["Voetbal_Round::SemiCompetition"] = $oTable->createColumn( "SemiCompetition" );
		$this["Voetbal_Round::CompetitionSeason"] = $oTable->createColumn( "CompetitionsPerSeasonId" );
	}
}
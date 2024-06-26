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
final class Voetbal_PoulePlace_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "PoulePlaces";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_PoulePlace::Id"] = $oTable->createColumn( "Id" );
		$this["Voetbal_PoulePlace::Team"] = $oTable->createColumn( "TeamId" );
		$this["Voetbal_PoulePlace::Number"] = $oTable->createColumn( "Number" );
		$this["Voetbal_PoulePlace::Poule"] = $oTable->createColumn( "PouleId" );
		$this["Voetbal_PoulePlace::PenaltyPoints"] = $oTable->createColumn( "PenaltyPoints" );
	}
}
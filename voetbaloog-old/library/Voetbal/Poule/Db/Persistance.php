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
final class Voetbal_Poule_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "Poules";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_Poule::Id"] = $oTable->createColumn( "Id" );
        $this["Voetbal_Poule::Number"] = $oTable->createColumn( "Number" );
		$this["Voetbal_Poule::Name"] = $oTable->createColumn( "Name" );
		$this["Voetbal_Poule::Round"] = $oTable->createColumn( "RoundId" );
	}
}
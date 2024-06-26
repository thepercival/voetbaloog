<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 584 2013-11-24 21:48:56Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
final class Voetbal_Team_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "Teams";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_Team::Id"] = $oTable->createColumn( "Id" );
		$this["Voetbal_Team::Name"] = $oTable->createColumn( "Name" );
		$this["Voetbal_Team::Abbreviation"] = $oTable->createColumn( "Abbreviation" );
		$this["Voetbal_Team::ImageName"] = $oTable->createColumn( "ImageName" );
		$this["Voetbal_Team::Association"] = $oTable->createColumn( "AssociationId" );
		$this["Voetbal_Team::ExternId"] = $oTable->createColumn( "ExternId" );
	}
}
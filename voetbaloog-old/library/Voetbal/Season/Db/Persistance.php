<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 894 2014-08-14 20:07:54Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
final class Voetbal_Season_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "Seasons";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_Season::Id"] = $oTable->createColumn( "Id" );
		$this["Voetbal_Season::Name"] = $oTable->createColumn( "Name" );
		$this["Voetbal_Season::StartDateTime"] = $oTable->createColumn( "StartDateTime" );
		$this["Voetbal_Season::EndDateTime"] = $oTable->createColumn( "EndDateTime" );
		$this["Voetbal_Season::ExternId"] = $oTable->createColumn( "ExternId" );
	}
}
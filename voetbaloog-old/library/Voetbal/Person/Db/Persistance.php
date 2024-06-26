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
final class Voetbal_Person_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "Persons";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_Person::Id"] = $oTable->createColumn( "Id" );
		$this["Voetbal_Person::FirstName"] = $oTable->createColumn( "FirstName" );
		$this["Voetbal_Person::NameInsertions"] = $oTable->createColumn( "Insertion" );
		$this["Voetbal_Person::LastName"] = $oTable->createColumn( "LastName" );
		$this["Voetbal_Person::DateOfBirth"] = $oTable->createColumn( "DateOfBirth" );
		$this["Voetbal_Person::ExternId"] = $oTable->createColumn( "ExternId" );
		$this["Voetbal_Person::ValidatedDateTime"] = $oTable->createColumn( "ValidatedDateTime" );
		$this["Voetbal_Person::Gender"] = $oTable->createColumn( "Gender" );
	}
}
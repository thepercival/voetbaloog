<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Persistance.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    MetaData
 */

/**
 * @package MetaData
 */
final class MetaData_ObjectChange_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "ImportDetails";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["MetaData_ObjectChange::Id"] = $oTable->createColumn( "Id" );
		$this["MetaData_ObjectChange::ImportRun"] = $oTable->createColumn( "ImportRunId" );
		$this["MetaData_ObjectChange::EntityName"] = $oTable->createColumn( "Entity" );
		$this["MetaData_ObjectChange::ActionName"] = $oTable->createColumn( "Action" );
		$this["MetaData_ObjectChange::ObjectProperty"] = $oTable->createColumn( "PropertyName" );
		$this["MetaData_ObjectChange::SystemId"] = $oTable->createColumn( "SystemId" );
		$this["MetaData_ObjectChange::OldValue"] = $oTable->createColumn( "OldValue" );
		$this["MetaData_ObjectChange::NewValue"] = $oTable->createColumn( "NewValue" );
	}
}
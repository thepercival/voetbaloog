<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Persistance.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Message
*/

/**
 * @package Message
 */
final class Message_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "Messages";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Message::Id"] = $oTable->createColumn( "Id" );
		$this["Message::Subject"] = $oTable->createColumn( "Subject" );
		$this["Message::Description"] = $oTable->createColumn( "Description" );
		$this["Message::InputDateTime"] = $oTable->createColumn( "InputDateTime" );
		$this["Message::FromUser"] = $oTable->createColumn( "FromUserId" );
		$this["Message::ToUser"] = $oTable->createColumn( "ToUserId" );
		$this["Message::State"] = $oTable->createColumn( "State" );
		$this["Message::Context"] = $oTable->createColumn( "Context" );
	}
}
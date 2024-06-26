<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 580 2013-11-20 15:28:51Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */


/**
 * @package VoetbalOog
 */
final class VoetbalOog_Message_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "MessagesPerUsersPerPool";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["VoetbalOog_Message::Id"] = $oTable->createColumn( "Id" );
		$this["VoetbalOog_Message::Message"] = $oTable->createColumn( "Message" );
		$this["VoetbalOog_Message::DateTime"] = $oTable->createColumn( "DateTime" );
		$this["VoetbalOog_Message::PoolUser"] = $oTable->createColumn( "UsersPerPoolId" );
	}
}
<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 905 2014-08-20 17:41:52Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
final class VoetbalOog_Pool_User_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "UsersPerPool";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["VoetbalOog_Pool_User::Id"] = $oTable->createColumn( "Id" );
		$this["VoetbalOog_Pool_User::Pool"] = $oTable->createColumn( "PoolId" );
		$this["VoetbalOog_Pool_User::User"] = $oTable->createColumn( "UserId" );
		$this["VoetbalOog_Pool_User::Admin"] = $oTable->createColumn( "Admin" );
		$this["VoetbalOog_Pool_User::Paid"] = $oTable->createColumn( "Paid" );
	}
}
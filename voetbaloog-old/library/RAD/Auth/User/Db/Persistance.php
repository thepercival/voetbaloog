<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Persistance.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
final class RAD_Auth_User_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "UsersExt";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["RAD_Auth_User::Id"] = $oTable->createColumn( "Id" );
		$this["RAD_Auth_User::Name"] = $oTable->createColumn( "LoginName" );
		$this["RAD_Auth_User::Password"] = $oTable->createColumn( "Password" );
		$this["RAD_Auth_User::LatestLoginDateTime"] = $oTable->createColumn( "LatestLoginDateTime" );
		$this["RAD_Auth_User::LatestLoginIpAddress"] = $oTable->createColumn( "LatestLoginIpAddress" );
		$this["RAD_Auth_User::System"] = $oTable->createColumn( "System" );
		$this["RAD_Auth_User::Preferences"] = $oTable->createColumn( "Preferences" );
	}
}
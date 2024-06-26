<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 958 2014-09-16 20:13:45Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package		VoetbalOog
 */
final class VoetbalOog_User_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "UsersExt";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["VoetbalOog_User::Id"] = $oTable->createColumn( "Id" );
		$this["VoetbalOog_User::Name"] = $oTable->createColumn( "LoginName" );
		$this["VoetbalOog_User::Password"] = $oTable->createColumn( "Password" );
		$this["VoetbalOog_User::LatestLoginDateTime"] = $oTable->createColumn( "LatestLoginDateTime" );
		$this["VoetbalOog_User::LatestLoginIpAddress"] = $oTable->createColumn( "LatestLoginIpAddress" );
		$this["VoetbalOog_User::System"] = $oTable->createColumn( "System" );
		$this["VoetbalOog_User::Preferences"] = $oTable->createColumn( "Preferences" );

		$this["VoetbalOog_User::EmailAddress"] = $oTable->createColumn( "EmailAddress" );
		$this["VoetbalOog_User::Picture"] = $oTable->createColumn( "Picture" );
		$this["VoetbalOog_User::Gender"] = $oTable->createColumn( "Gender" );
		$this["VoetbalOog_User::DateOfBirth"] = $oTable->createColumn( "DateOfBirth" );
		$this["VoetbalOog_User::HashType"] = $oTable->createColumn( "HashType" );
		$this["VoetbalOog_User::Salted"] = $oTable->createColumn( "Salted" );
		$this["VoetbalOog_User::ActivationKey"] = $oTable->createColumn( "ActivationKey" );
		$this["VoetbalOog_User::FacebookId"] = $oTable->createColumn( "FacebookId" );
		$this["VoetbalOog_User::GoogleId"] = $oTable->createColumn( "GoogleId" );
		$this["VoetbalOog_User::TwitterId"] = $oTable->createColumn( "TwitterId" );
		$this["VoetbalOog_User::CookieSessionToken"] = $oTable->createColumn( "CookieSessionToken" );
		$this["VoetbalOog_User::PeriodicEmail"] = $oTable->createColumn( "PeriodicEmail" );

	}
}
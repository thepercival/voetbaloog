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
final class Voetbal_Team_Membership_Player_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "PlayerPeriods";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_Team_Membership_Player::Id"] = $oTable->createColumn( "Id" );
		$this["Voetbal_Team_Membership_Player::Client"] = $oTable->createColumn( "PersonId" );
		$this["Voetbal_Team_Membership_Player::Provider"] = $oTable->createColumn( "TeamId" );
		$this["Voetbal_Team_Membership_Player::Line"] = $oTable->createColumn( "Line" );
		$this["Voetbal_Team_Membership_Player::BackNumber"] = $oTable->createColumn( "BackNumber" );
		$this["Voetbal_Team_Membership_Player::Picture"] = $oTable->createColumn( "Picture" );
		$this["Voetbal_Team_Membership_Player::StartDateTime"] = $oTable->createColumn( "StartDateTime" );
		$this["Voetbal_Team_Membership_Player::EndDateTime"] = $oTable->createColumn( "EndDateTime" );
		$this["Voetbal_Team_Membership_Player::ExternId"] = $oTable->createColumn( "ExternId" );
	}
}
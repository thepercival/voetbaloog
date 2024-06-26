<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 617 2013-12-11 10:41:56Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
final class Voetbal_Team_Membership_StaffMember_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "StaffMemberPeriods";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_Team_Membership_StaffMember::Id"] = $oTable->createColumn( "Id" );
		$this["Voetbal_Team_Membership_StaffMember::Client"] = $oTable->createColumn( "PersonId" );
		$this["Voetbal_Team_Membership_StaffMember::Provider"] = $oTable->createColumn( "TeamId" );
		$this["Voetbal_Team_Membership_StaffMember::FunctionX"] = $oTable->createColumn( "FunctionX" );
		$this["Voetbal_Team_Membership_StaffMember::Importance"] = $oTable->createColumn( "Importance" );
		$this["Voetbal_Team_Membership_StaffMember::Picture"] = $oTable->createColumn( "Picture" );
		$this["Voetbal_Team_Membership_StaffMember::StartDateTime"] = $oTable->createColumn( "StartDateTime" );
		$this["Voetbal_Team_Membership_StaffMember::EndDateTime"] = $oTable->createColumn( "EndDateTime" );
	}
}
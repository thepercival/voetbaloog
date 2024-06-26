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
final class VoetbalOog_Pool_Invitations_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "PoolInvitations";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["VoetbalOog_Pool_Invitation::Id"] = $oTable->createColumn( "Id" );
		$this["VoetbalOog_Pool_Invitation::Pool"] = $oTable->createColumn( "PoolId" );
		$this["VoetbalOog_Pool_Invitation::Inviter"] = $oTable->createColumn( "InviterUserId" );
		$this["VoetbalOog_Pool_Invitation::SendDateTime"] = $oTable->createColumn( "SendDateTime" );

		$this["VoetbalOog_Pool_Invitation::Invitee"] = $oTable->createColumn( "InviteeUserId" );
		$this["VoetbalOog_Pool_Invitation::InviteeEmailAddress"] = $oTable->createColumn( "InviteeEmailAddress" );
		$this["VoetbalOog_Pool_Invitation::DeclinedDateTime"] = $oTable->createColumn( "DeclinedDateTime" );
		$this["VoetbalOog_Pool_Invitation::AcceptedDateTime"] = $oTable->createColumn( "AcceptedDateTime" );
	}
}
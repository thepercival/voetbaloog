<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 580 2013-11-20 15:28:51Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
interface VoetbalOog_Pool_Invitation_Interface
{
	/**
	 * gets the Pool
	 *
	 * @return 	VoetbalOog_Pool_Interface	the Pool
	 */
	public function getPool();
	/**
	 * puts the Pool
	 *
	 * @param VoetbalOog_Pool_Interface $oPool the Pool which will be set
	 * @return 	null
	 */
	public function putPool( $oPool );
	/**
	 * gets the Inviter
	 *
	 * @return 	VoetbalOog_User_Interface	the Inviter
	 */
	public function getInviter();
	/**
	 * puts the Inviter
	 *
	 * @param VoetbalOog_User_Interface $oInviter the Inviter which will be set
	 * @return 	null
	 */
	public function putInviter( $oInviter );
	/**
	 * gets the senddatetime
	 *
	 * @return 	DateTime	The senddatetime
	 */
	public function getSendDateTime();
	/**
	 * gets the senddatetime
	 *
	 * @param 	DateTime	$oSendDateTime	The senddatetime
	 * @return 	null
	*/
	public function putSendDateTime( $oSendDateTime );

	/**
	 * gets the Invitee
	 *
	 * @return 	VoetbalOog_User_Interface	the Invitee
	 */
	public function getInvitee();
	/**
	 * puts the Invitee
	 *
	 * @param VoetbalOog_User_Interface $oInvitee the Invitee which will be set
	 * @return 	null
	*/
	public function putInvitee( $oInvitee );
	/**
	 * gets the invitee
	 *
	 * @return string	The InviteeEmailAddress
	 */
	public function getInviteeEmailAddress();
	/**
	 * puts the inviteeemailaddress
	 *
	 * @param  string	$sInviteeEmailAddress	The emailaddress of the invitee
	 * @return null
	*/
	public function putInviteeEmailAddress( $sInviteeEmailAddress );
	/**
	 * gets the accepteddatetime
	 *
	 * @return 	DateTime	The accepteddatetime
	 */
	public function getAcceptedDateTime();
	/**
	 * gets the accepteddatetime
	 *
	 * @param 	DateTime	$oAcceptedDateTime	The accepteddatetime
	 * @return 	null
	*/
	public function putAcceptedDateTime( $oAcceptedDateTime );
	/**
	 * gets the Rejected datetime
	 *
	 * @return 	DateTime	The Rejected datetime
	 */
	public function getRejectedDateTime();
	/**
	 * gets the Rejected datetime
	 *
	 * @param 	DateTime	$oRejectedDateTime	The Rejected datetime
	 * @return 	null
	*/
	public function putRejectedDateTime( $oRejectedDateTime );
}
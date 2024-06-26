<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 1199 2019-08-13 11:22:19Z thepercival $
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
interface VoetbalOog_Message_Interface
{
	/**
	 * gets the Message
	 *
	 * @return 	string	the Message
	 */
	public function getMessage();
	/**
	 * puts the Message
	 *
	 * @param string $sMessage the Message which will be set
	 * @return 	null
	 */
	public function putMessage( $sMessage );
	/**
	 * gets the PoolUser
	 *
	 * @return 	VoetbalOog_Pool_User_Interface	the PoolUser
	 */
	public function getPoolUser();
	/**
	 * puts the PoolUser
	 *
	 * @param VoetbalOog_Pool_User_Interface $oPoolUser the PoolUser which will be set
	 * @return 	null
	 */
	public function putPoolUser( $oPoolUser );
	/**
	 * gets the DateTime
	 *
	 * @return 	Agenda_DateTime	the DateTime
	 */
	public function getDateTime();
	/**
	 * puts the DateTime
	 *
	 * @param Agenda_DateTime $oDateTime the DateTime which will be set
	 * @return 	null
	 */
	public function putDateTime( $oDateTime );


}
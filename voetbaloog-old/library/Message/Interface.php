<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Message
 */

/**
 *
 * @package Message
 */
interface Message_Interface
{
	/**
	 * gets the subject
	 *
	 * @return 	string	the subject
	 */
	public function getSubject();
	/**
	 * puts the Subject
	 *
	 * @param string $szSubject the Subject which will be set
	 * @return 	null
	 */
	public function putSubject( $szSubject );
	/**
	 * gets the Description
	 *
	 * @return 	string	the Description
	 */
	public function getDescription();
	/**
	 * puts the Description
	 *
	 * @param string $szDescription the Description which will be set
	 * @return 	null
	 */
	public function putDescription( $szDescription );
	/**
	 * gets the FromUser
	 *
	 * @return 	RAD_Auth_User_Interface	the FromUser
	 */
	public function getFromUser();
	/**
	 * puts the FromUser
	 *
	 * @param RAD_Auth_User_Interface $objFromUser the FromUser which will be set
	 * @return 	null
	 */
	public function putFromUser( $objFromUser );
	/**
	 * gets the ToUser
	 *
	 * @return 	RAD_Auth_User_Interface	the ToUser
	 */
	public function getToUser();
	/**
	 * puts the ToUser
	 *
	 * @param RAD_Auth_User_Interface $objToUser the ToUser which will be set
	 * @return 	null
	 */
	public function putToUser( $objToUser );
	/**
	 * gets the InputDateTime
	 *
	 * @return 	Agenda_DateTime	the InputDateTime
	 */
	public function getInputDateTime();
	/**
	 * puts the InputDateTime
	 *
	 * @param Agenda_DateTime $objInputDateTime the InputDateTime which will be set
	 * @return 	null
	 */
	public function putInputDateTime( $objInputDateTime );
	/**
	 * gets the State
	 *
	 * @return 	int	the State
	 */
	public function getState();
	/**
	 * puts the State
	 *
	 * @param int $nState the State which will be set
	 * @return 	null
	 */
	public function putState( $nState );
	/**
	 * gets the Context
	 *
	 * @return 	int	the Context
	 */
	public function getContext();
	/**
	 * puts the Context
	 *
	 * @param int $nContext the Context which will be set
	 * @return 	null
	 */
	public function putContext( $nContext );
}
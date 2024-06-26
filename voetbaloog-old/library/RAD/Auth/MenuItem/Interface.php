<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
interface RAD_Auth_MenuItem_Interface
{
	/**
	 * gets the description
	 *
	 * @return string The description
	 */
	public function getDescription();
	/**
	 * puts the description
	 *
	 * @param  string	$szDescription	The description of the menuitem
	 * @return null
	 */
	public function putDescription( $szDescription );
	/**
	 * gets the Action
	 *
	 * @return RAD_Auth_Action The Action
	 */
	public function getAction();
	/**
	 * puts the Action
	 *
	 * @param  string	$szAction	The Action of the menuitem
	 * @return null
	 */
	public function putAction( $szAction );
	/**
	 * gets the visibility
	 *
	 * @return bool The visibility
	 */
	public function getShow();
	/**
	 * puts the visibility
	 *
	 * @param  bool	$bShow	The visibility of the menuitem
	 * @return null
	 */
	public function putShow( $bShow );
}
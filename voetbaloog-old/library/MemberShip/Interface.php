<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    MemberShip
 */

/**
 * @package MemberShip
 */
interface MemberShip_Interface
{
	/**
	 * gets the Client
	 *
	 * @return 	Patterns_Idable_Interface	the Client
	 */
	public function getClient();
	/**
	 * puts the Client
	 *
	 * @param Patterns_Idable_Interface $objClient the Client which will be set
	 * @return 	null
	 */
	public function putClient( $objClient );
	/**
	 * gets the Provider
	 *
	 * @return 	Patterns_Idable_Interface	the Provider
	 */
	public function getProvider();
	/**
	 * puts the Provider
	 *
	 * @param Patterns_Idable_Interface $objProvider the Provider which will be set
	 * @return 	null
	 */
	public function putProvider( $objProvider );
}
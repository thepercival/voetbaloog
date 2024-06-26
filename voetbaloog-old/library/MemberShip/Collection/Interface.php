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
 * @package    MemberShip
 */

/**
 *
 *
 * @package    MemberShip
 */
interface MemberShip_Collection_Interface
{
	/**
	 * gets the items by date
	 *
	 * @param Agenda_DateTime		$objDateTime	The Date
	 * @return MemberShip_Collection_Interface		the found items
	 */
	public function getItemsByDate( $objDateTime );
	/**
	 * gets the items by Client
	 *
	 * @param Patterns_Idable_Interface				$objClient		The Client
	 * @return MemberShip_Collection_Interface		the found items
	 */
	public function getItemsByClient( $objClient );
	/**
	 * gets the items by provider
	 *
	 * @param MemberShip_Provider_Interface	$objProvider	The Provider
	 * @return MemberShip_Collection_Interface		the found items
	 */
	public function getItemsByProvider( $objProvider );
	/**
	 * gets the item by provider, client and STARTDateTime
	 *
	 * @param MemberShip_Provider_Interface	$objProvider	The Provider
	 * @param Patterns_Idable_Interface				$objClient		The Client
	 * @param string | DateTime			$vtStartDateTime				TheStartDateTime
	 * @return MemberShip_Interface		the found item
	 */
	public function getItem( $objProvider, $objClient, $vtStartDateTime );
}
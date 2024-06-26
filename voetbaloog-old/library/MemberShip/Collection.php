<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Collection.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    MemberShip
 */

/**
 * @package    MemberShip
 */
class MemberShip_Collection extends Agenda_TimeSlot_Collection implements MemberShip_Collection_Interface
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * * Defined by MemberShip_Collection_Interface; gets the items by date
	 *
	 * @see MemberShip_Collection_Interface::getItemsByDate()
	 */
	public function getItemsByDate( $objDateTime )
	{
		return null;
	}

	/**
	 * * Defined by MemberShip_Collection_Interface; gets the items by client
	 *
	 * @see MemberShip_Collection_Interface::getItemsByClient()
	 */
	public function getItemsByClient( $objClient )
	{
		$objMemberShips = MemberShip_Factory::createObjects();

		foreach ( $this as $objMemberShip )
		{
			if ( $objMemberShip->getClient()->getId() === $objClient->getId() )
				$objMemberShips->add( $objMemberShip );
		}
		return $objMemberShips;
	}

	/**
	 * * Defined by MemberShip_Collection_Interface; gets the items by provider
	 *
	 * @see MemberShip_Collection_Interface::getItemsByProvider()
	 */
	public function getItemsByProvider( $objProvider )
	{
		return null;
	}

	/**
	 *
	 * @see MemberShip_Collection_Interface::getItemsByClientProvider()
	 */
	public function getItemsByClientProvider( $objClient, $objProvider )
	{
		$objMemberShips = MemberShip_Factory::createObjects();

		foreach ( $this as $objMemberShip )
		{
			if ( $objMemberShip->getClient()->getId() === $objClient->getId()
				and $objMemberShip->getProvider()->getId() === $objProvider->getId()
			)
				$objMemberShips->add( $objMemberShip );
		}
		return $objMemberShips;
	}



	/**
	 * * Defined by MemberShip_Collection_Interface; gets the item by provider and client
	 *
	 * @see MemberShip_Collection_Interface::getItem()
	 */
	public function getItem( $objProvider, $objClient, $vtDateTime )
	{
		if ( $objProvider === null or $objClient === null or $vtDateTime === null )
			return null;

		if ( is_string( $vtDateTime ) )
			$vtDateTime = Agenda_Factory::createDateTime( $vtDateTime );

		foreach ( $this as $szId => $objMemberShip )
		{
			/*
			if (
				$objMemberShip->getProvider() === $objProvider
				and $objMemberShip->getClient() === $objClient
				and $objMemberShip->getStartDateTime() === $vtDateTime
			)
			{
				return $objMemberShip;
			}
			*/
			if (
				$objMemberShip->getProvider()->getId() === $objProvider->getId()
			and
				$objMemberShip->getClient()->getId() === $objClient->getId()
			and
				(
					(
						$objMemberShip->getStartDateTime()->equals( $vtDateTime )
					or
						$objMemberShip->getStartDateTime()->isEarlier( $vtDateTime )
					)
				and
					(
						$objMemberShip->getEndDateTime() === null
					or
						$objMemberShip->getEndDateTime()->equals( $vtDateTime )
					or
						$objMemberShip->getEndDateTime()->isLater( $vtDateTime )
					)
				)
			)
			{
				return $objMemberShip;
			}
		}

		return null;
	}
}
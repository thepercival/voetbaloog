<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Factory.php 4263 2015-12-23 21:22:00Z thepercival $
 *
 * @package    MemberShip
 */

/**
 * @package MemberShip
 */
class MemberShip_Factory implements MemberShip_Factory_Interface, Patterns_Singleton_Interface
{
	private static $m_objSingleton;

	/**
	 * @see Patterns_Singleton_Interface::__clone()
	 */
	public function __clone()
	{
		trigger_error("Cloning is not allowed.", E_USER_ERROR);
	}

	/**
	 * @see Patterns_Singleton_Interface::getInstance()
	 */
	public static function getInstance()
	{
		if ( self::$m_objSingleton === null )
		{
			$MySelf = __CLASS__;
			self::$m_objSingleton = new $MySelf();
		}
		return self::$m_objSingleton;
	}

	/**
	 * @see MemberShip_Factory_Interface::createObject()
	 */
	public static function createObject( $oProvider, $oClient )
	{
		$oMembership = new MemberShip();
		$oMembership->putProvider( $oProvider );
		$oMembership->putClient( $oClient );
		$oMembership->putId( $oProvider->getId()."-".$oClient->getId() );
		return $oMembership;
	}

	/**
	 * @see MemberShip_Factory_Interface::createObjects()
	 */
	public static function createObjects()
	{
		return new MemberShip_Collection();
	}

	/**
	 * @see MemberShip_Factory_Interface::getMembershipFilters()
	 */
	public static function getMembershipFilters( $szClassName, $objProvider, $objClient, $objDateTimeSlot )
	{
		$oOptions = Construction_Factory::createOptions();

		if ( $objProvider !== null )
			$oOptions->addFilter( $szClassName."::Provider", "EqualTo", $objProvider );

		if ( $objClient !== null )
			$oOptions->addFilter( $szClassName."::Client", "EqualTo", $objClient );

		if ( $objDateTimeSlot !== null )
		{
			$oPeriodFilters = Construction_Factory::createFiltersForTimeSlots( $szClassName, $objDateTimeSlot, Agenda_TimeSlot::EXCLUDE_NONE, true );
			$oOptions->addCollection( $oPeriodFilters );
		}

		return $oOptions;
	}

	/**
	 * @see MemberShip_Factory_Interface::getOverlaps()
	 */
	public static function getOverlaps( $oClient, $oProvider, $oOverlapParam )
	{
		$oOverlaps = Agenda_Factory::createTimeSlots();
		{
			$oMemberships = $oClient->getMemberships()->getItemsByClientProvider( $oClient, $oProvider );

			foreach( $oMemberships as $oMembership )
			{
				$oOverlap = Agenda_Factory::createOverlap( $oOverlapParam, $oMembership );

				$oOverlaps->add( $oOverlap );
			}
		}
		return $oOverlaps;
	}
}
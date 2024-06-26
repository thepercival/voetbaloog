<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Collection.php 4554 2019-08-12 14:37:34Z thepercival $
 * @package    Agenda
 */


/**
 * @package Agenda
 */
class Agenda_TimeSlot_Collection extends Patterns_ObservableObject_Collection implements Agenda_TimeSlot_Collection_Interface, Patterns_Idable_Interface
{
	use Patterns_Idable_Trait;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @see Agenda_TimeSlot_Collection_Interface::has()
	 */
	public function has( $oTimeSlotToLookFor, $nRange = Agenda_TimeSlot::EXCLUDE_NONE, $bInvertCompare = false )
	{
		if ( $bInvertCompare === false )
		{
			foreach ( $this as $oTimeSlot )
			{
				if ( $oTimeSlotToLookFor->overlapses( $oTimeSlot, $nRange ) )
					return true;
			}
		}
		else
		{
			foreach ( $this as $oTimeSlot )
			{
				if ( $oTimeSlot->overlapses( $oTimeSlotToLookFor, $nRange ) )
					return true;
			}
		}
		return false;
	}

	/**
	 * @see Agenda_TimeSlot_Collection_Interface::getOverlappingTimeSlots()
	 */
	public function getOverlappingTimeSlots( Agenda_TimeSlot_Interface $oTimeSlotToBeIn, $nRange = Agenda_TimeSlot::EXCLUDE_NONE )
	{
		$oTimeSlots = Agenda_Factory::createTimeSlots();

		foreach ( $this as $oTimeSlot )
		{
			if ( $oTimeSlotToBeIn->overlapses( $oTimeSlot, $nRange ) === true )
			{
				$oTimeSlots->add( $oTimeSlot );
			}
		}
		return $oTimeSlots;
	}

	/**
	 * @see Agenda_TimeSlot_Collection_Interface::getByDateTime()
	 */
	public function getByDateTime( DateTime $oDateTime = null )
	{
		if ( $oDateTime === null )
			$oDateTime = Agenda_Factory::createDateTime();

		foreach ( $this as $oTimeSlot )
		{
			if ( $oTimeSlot->isIn( $oDateTime ) )
				return $oTimeSlot;
		}
		return null;
	}

	/**
	 * @see Agenda_TimeSlot_Collection_Interface::hasDateTime()
	 */
	public function hasDateTime( DateTime $oDateTime = null )
	{
		return ( $this->getByDateTime( $oDateTime ) !== null );
	}

	/**
	 * @see Agenda_TimeSlot_Collection_Interface::getDuration()
	 */
	public function getDuration( $nZendDateTimePart )
	{
		$nDuration = 0;
		foreach ( $this as $oTimeSlot )
			$nDuration += $oTimeSlot->getDuration( $nZendDateTimePart );
		return $nDuration;
	}

	/**
	 * @see Agenda_TimeSlot_Collection_Interface::equals()
	 */
	public function equals( $oTimeSlots )
	{
		if ( $oTimeSlots->count() !== $this->count() )
			return false;

		foreach( $oTimeSlots as $oTimeSlot )
		{
			foreach( $this as $oMyTimeSlot )
			{
				if ( $oMyTimeSlot->equals( $oTimeSlot ) === false )
					return false;
			}
		}
		return true;
	}
}
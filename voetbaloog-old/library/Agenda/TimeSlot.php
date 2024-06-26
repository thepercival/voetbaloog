<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: TimeSlot.php 4567 2019-09-10 13:09:16Z thepercival $
 * @package    Agenda
 */

/**
 * @package Agenda
 */
class Agenda_TimeSlot implements Agenda_TimeSlot_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
	// Agenda_TimeSlot_Interface
	protected $m_objStartDateTime;
	protected $m_objEndDateTime;

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

	CONST EXCLUDE_NONE = 0;
	CONST EXCLUDE_BEFORESTART = 1;
	CONST EXCLUDE_AFTEREND = 2;
	CONST EXCLUDE_BOTH = 3;

	public function __construct(){}

	public function __clone()
	{
		$this->m_objStartDateTime = clone $this->m_objStartDateTime;
		$this->m_objEndDateTime = clone $this->m_objEndDateTime;
		$this->m_vtId = "cloned at ".microtime();
	}

	/**
	 * @see Agenda_TimeSlot_Interface::getStartDateTime()
	 */
	public function getStartDateTime(): Agenda_DateTime
	{
		return $this->m_objStartDateTime;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::putStartDateTime()
	 */
	public function putStartDateTime( $objStartDateTime )
	{
		if ( $objStartDateTime !== null and is_string( $objStartDateTime ) )
			$objStartDateTime = Agenda_Factory::createDateTime( $objStartDateTime );

		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::StartDateTime", $this->m_objStartDateTime, $objStartDateTime );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_objStartDateTime = $objStartDateTime;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::getEndDateTime()
	 */
	public function getEndDateTime()
	{
		return $this->m_objEndDateTime;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::putEndDateTime()
	 */
	public function putEndDateTime( $objEndDateTime )
	{
		if ( $objEndDateTime !== null and is_string( $objEndDateTime ) )
			$objEndDateTime = Agenda_Factory::createDateTime( $objEndDateTime );

		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::EndDateTime", $this->m_objEndDateTime, $objEndDateTime );
  			$this->notifyObservers( $objObjectChange );
		}
		$this->m_objEndDateTime = $objEndDateTime;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::isIn()
	 */
	public function isIn( Agenda_DateTime $oDateTime )
	{
		return ( $oDateTime >= $this->getStartDateTime() and ( $this->getEndDateTime() === null or $oDateTime <= $this->getEndDateTime() ) );
	}

	/**
	 * @see Agenda_TimeSlot_Interface::overlapses()
	 */

	/*
	 * 1	2		3		4
	 * |	| this  |		|
	 * ______________________
	 * |	|		|		|
	 * |  _ |		|		|
	 * | |A||		|		|
	 * |  - |		|		|
	 * |   -|---	|		|
	 * |  |	| B |	|		|
	 * |   -|---	|		|
	 * |	|	 _  |		|
	 * |	|	|C| |		|
	 * |	|	 -  |		|
	 * |	|	 ___|_		|
	 * |	|	| D	| |		|
	 * |	|	 ---|-		|
	 * |	|	    |  _	|
	 * |	|		| |E|	|
	 * |	|	    |  -	|
	 * |   _|_______|_      |
	 * |  | |   F   |  |    |
	 * |   -|-------|-      |
	 *
	 * The timeslot($this) is from 2 to 3
	 * When the second paramter is "ExcludeNone" B, C, D and F will be found.
	 * When the second paramter is "ExcludeBeforeStart"   only C and D will be found.
	 * When the second paramter is "ExcludeAfterEnd" only B and C will be found.
	 * When the second paramter is "ExcludeBoth" only C will be found.
	*/
	public function overlapses( $oTimeSlot, $nRange = Agenda_TimeSlot::EXCLUDE_NONE )
	{
		if ( $oTimeSlot === null )
			return false;

		$bRet = false;

		$oTimeSlotEndDateTime = $oTimeSlot->getEndDateTime();
		$bTimeSlotEndsAfterBegin = ( $oTimeSlotEndDateTime === null or $this->getStartDateTime() < $oTimeSlotEndDateTime );

		$oThisEndDateTime = $this->getEndDateTime();
		$bTimeSlotBeginsBeforeEnd = ( $oThisEndDateTime === null or $oThisEndDateTime > $oTimeSlot->getStartDateTime() );

		if ( $bTimeSlotEndsAfterBegin === true and $bTimeSlotBeginsBeforeEnd === true )
			$bRet = true;

		if ( ( Agenda_TimeSlot::EXCLUDE_BEFORESTART & $nRange ) === Agenda_TimeSlot::EXCLUDE_BEFORESTART )
		{
			if ( $oTimeSlot->getStartDateTime() < $this->getStartDateTime() )
				$bRet = false;
		}

		if ( ( Agenda_TimeSlot::EXCLUDE_AFTEREND & $nRange ) === Agenda_TimeSlot::EXCLUDE_AFTEREND )
		{
			if ( $oThisEndDateTime !== null and ( $oTimeSlotEndDateTime === null or $oTimeSlotEndDateTime > $oThisEndDateTime ) )
				$bRet = false;
		}
		return $bRet;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::getDuration()
	 */
	public function getDuration( $nZendDateTimePart = null, $bFloored = false )
	{
		return Agenda_Factory::getDuration( $this->getStartDateTime(), $this->getEndDateTime(), $nZendDateTimePart, $bFloored );
	}

	public function getHalfway(): DateTime
    {
        $oHalfwayDateTime = Agenda_Factory::createDateTime( (string) $this->getStartDateTime() );
        $nHalfWaySeconds = (int) ($this->getDuration() / 2);
        $oHalfwayDateTime->modify("+" . $nHalfWaySeconds ." seconds");
        return $oHalfwayDateTime;
    }

	/**
	 * @see Agenda_TimeSlot_Interface::equals()
	 */
	public function equals( $oTimeSlot )
	{
		return ( $oTimeSlot->getStartDateTime() == $this->getStartDateTime()
			and $oTimeSlot->getEndDateTime() == $this->getEndDateTime()
		);
	}
}
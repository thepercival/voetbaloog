<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Week.php 4568 2019-09-10 16:16:22Z thepercival $
 * @package    Agenda
 */

/**
 * @package Agenda
 */
class Agenda_Week extends Agenda_TimeSlot implements Agenda_Week_Interface
{
	protected $m_szName;  				// string
	protected $m_objDays;  				// Patterns_Collection
	protected $m_objDaysWithOutWeekend; // Patterns_Collection

	public function __construct()
  	{
  		parent::__construct();
  	}

  	/**
	 * @see Agenda_Week_Interface::getName()
	 */
	public function getName()
	{
		return $this->m_szName;
	}

  	/**
	 * @see Agenda_Week_Interface::putName()
	 */
	public function putName( $szName )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Agenda_Week::Name", $this->m_szName, $szName );
  			$this->notifyObservers( $objObjectChange );
		}
		$this->m_szName = $szName;
	}

  	/**
	 * @see Agenda_TimeSlot_Interface::getStartDateTime()
	 */
  	public function getStartDateTime(): Agenda_DateTime
	{
		if ( $this->m_objStartDateTime === null )
		{
			$this->m_objStartDateTime = $this->getISOFirstDayOfWeekDateTime( substr( $this->m_vtId, 0, 4 ), substr( $this->m_vtId, 4, 2 ) );
		}
		return $this->m_objStartDateTime;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::putStartDateTime()
	 */
	public function putStartDateTime( $objStartDateTime )
	{
		throw new Exception( "should be got from weekid", E_ERROR );
	}

	/**
	 * @see Agenda_TimeSlot_Interface::getEndDateTime()
	 */
	public function getEndDateTime()
	{
		if ( $this->m_objEndDateTime === null )
		{
			$this->m_objEndDateTime = Agenda_Factory::createDate( $this->getStartDateTime() );
			$this->m_objEndDateTime->modify( "+7 days" );
		}
		return $this->m_objEndDateTime;
	}

	/**
	 * @see Agenda_TimeSlot_Interface::putEndDateTime()
	 */
	public function putEndDateTime( $objEndDateTime )
	{
		throw new Exception( "should be got from weekid", E_ERROR );
	}

	/**
	 * @see Agenda_Week_Interface::getDays()
	 */
	public function getDays()
	{
		if ( $this->m_objDays === null )
		{
			$this->m_objDays = Agenda_Factory::createTimeslots();

			$objDateTmp = Agenda_Factory::createDate( $this->getStartDateTime() );
			while ( $objDateTmp->isEarlier( $this->getEndDateTime() ) )
			{
				$this->m_objDays->add( Agenda_Factory::createDay( $objDateTmp ) );
				$objDateTmp->modify("+1 days");
			}
		}
		return $this->m_objDays;
	}

	/**
	 * @see Agenda_Week_Interface::getDaysWithOutWeekend()
	 */
	public function getDaysWithOutWeekend()
	{
		if ( $this->m_objDaysWithOutWeekend === null )
		{
			$this->m_objDaysWithOutWeekend = Agenda_Factory::createTimeslots();

			$objDays = $this->getDays();
			foreach( $objDays as $objDay )
			{
				if ( $objDay->getStartDateTime()->toValue( Zend_Date::WEEKDAY_8601 ) !== 6
					and $objDay->getStartDateTime()->toValue( Zend_Date::WEEKDAY_8601 ) !== 7
				)
					$this->m_objDaysWithOutWeekend->add( $objDay );
			}
		}
		return $this->m_objDaysWithOutWeekend;
	}

	protected function getISOFirstDayOfWeekDateTime( $year, $week )
	{
        $date = date( Agenda_Factory::$m_szDateTimeFormat, strtotime( $year."W".$week ) );

        return Agenda_Factory::createDateTime( $date );
	}
}
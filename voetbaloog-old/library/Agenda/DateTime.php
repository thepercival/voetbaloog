<?php

/**
 * @copyright  	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: DateTime.php 4380 2016-03-28 13:28:12Z thepercival $
 * @package		Agenda
 */

/**
 * @package Agenda
 */
class Agenda_DateTime extends DateTime implements Patterns_Idable_Interface
{
	// Patterns_Idable_Interface
	protected $m_vtId;		// string

	private $m_szSerDateTime;	// string for serialization
	private $m_szSerTimeZone;	// string for serialization

	CONST STR_SQLDATE = "Y-m-d";
	CONST STR_SQLTIME = "H:i:s";
	CONST STR_SQLDATETIME = "Y-m-d H:i:s"; // default
	CONST STR_NICEDATE = "D j M Y";
	CONST STR_NICEDATETIME = "D j M Y H:i";

	public function __construct( $date = null )
	{
		parent::__construct( $date, new DateTimeZone( date_default_timezone_get() ) );

		$this->m_vtId = $this->getTimestamp();
	}

	/**
	 * @see Patterns_Idable_Interface::getId()
	 */
	public function getId()
	{
		return $this->m_vtId;
	}

	/**
	 *
	 * @see Patterns_Idable_Interface::putId()
	 */
	public function putId( $vtId )
	{
		throw new Exception("Cannnot put the id of the datetime",E_ERROR);
	}

	public function getTimestamp()
	{
		return strtotime( $this );
	}

	public function isEarlier( $objDateTime )
	{
		return ( $this->getTimestamp() < $objDateTime->getTimestamp() );
	}

	public function isLater( $objDateTime )
	{
		return ( $this->getTimestamp() > $objDateTime->getTimestamp() );
	}

	public function equals( $objDateTime )
	{
		return ( $this->getTimestamp() == $objDateTime->getTimestamp() );
	}

	public function toValue( $nZendConst )
	{
		return (int) $this->format( self::getPHPValue( $nZendConst ) );
	}

	private static function getPHPValue( $nZendConst )
	{
		switch( $nZendConst )
		{
			case Zend_Date::YEAR:
				return "Y";
			case Zend_Date::YEAR_SHORT:
				return "y";
			case Zend_Date::YEAR_8601:
				return "o";
			case Zend_Date::MONTH_SHORT:
				return "m";
			case Zend_Date::DAY_SHORT:
				return "d";
			case Zend_Date::WEEKDAY_8601:
				return "N";
			case Zend_Date::WEEK:
				return "W";
			case Zend_Date::TIME_SHORT:
				return "H:i:s";
			case Zend_Date::HOUR_SHORT:
				return "H";
			case Zend_Date::MINUTE_SHORT:
				return "i";
			case Zend_Date::SECOND_SHORT:
				return "s";
		}
		return null;
	}

	public function set( $nValue, $nZendConst )
	{
		$nValueSub = $this->toValue( $nZendConst );

		$szPHPConst = null;
		switch( $nZendConst )
		{
			case Zend_Date::YEAR_SHORT:
				$szPHPConst = "year";
				break;
			case Zend_Date::MONTH_SHORT:
				$szPHPConst = "month";
				break;
			case Zend_Date::DAY_SHORT:
				$szPHPConst = "day";
				break;
			case Zend_Date::HOUR_SHORT:
				$szPHPConst = "hour";
				break;
			case Zend_Date::MINUTE_SHORT:
				$szPHPConst = "minute";
				break;
			case Zend_Date::SECOND_SHORT:
				$szPHPConst = "second";
				break;
		}

		$this->modify( "-".$nValueSub." ".$szPHPConst );
		$this->modify( "+".$nValue." ".$szPHPConst );
	}

	public function toString( $szFormat )
	{
		return Agenda_Factory::translate( strtolower( $this->format( $szFormat ) ) );
	}

	public function __toString()
	{
		return $this->toString( static::STR_SQLDATETIME );
	}

	public function __call($method, $args)
	{
		throw new Exception("ZENDEXT__CALL(method:".$method."(".implode(",",$args).") )");
	}

	public function __sleep()
	{
		$this->m_szSerDateTime = "".$this;
		$this->m_szSerTimeZone = $this->getTimeZone()->getName();
		return array( "m_vtId", "m_szSerDateTime", "m_szSerTimeZone");
	}

	public function __wakeup()
	{
		if ( $this->m_szSerTimeZone === null )
			parent::__construct( $this->m_szSerDateTime );
		else
			parent::__construct( $this->m_szSerDateTime, new DateTimeZone( $this->m_szSerTimeZone ) );
		$this->m_szSerDateTime = null;
		$this->m_szSerTimeZone = null;
	}
}

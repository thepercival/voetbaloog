<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Factory.php 3951 2013-09-19 09:40:09Z thepercival $
 * @since	  File available since Release 4.0
 * @package	Agenda
 */

/**
 * @package Agenda
 */
class Agenda_Week_Factory extends Object_Factory_JSON implements Agenda_Week_Factory_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see Object_Factory_Interface::createObjects()
	 */
	public static function createObjects()
	{
		return Agenda_Factory::createTimeSlots();
	}

	/**
	 * @see Agenda_Week_Factory_Interface::createObjectFromDate()
	 */
	public static function createObjectFromDate( $objDate )
	{
		$nYear = $objDate->toValue( Zend_Date::YEAR_8601 );
		$nWeek = $objDate->toValue( Zend_Date::WEEK );

		if ( $nWeek < 10 )
			$nWeek = "0".$nWeek;
		return self::createObjectExt( $nYear.$nWeek, $nWeek );
	}

	/**
	 * @see Agenda_Week_Factory_Interface::createObjectExt()
	 */
	public static function createObjectExt( $vtWeekId = null, $vtWeekName = null )
	{
		if ( $vtWeekId === null )
			return self::createObjectFromDate( Agenda_Factory::createDate() );

		$objWeeks = self::getPool();
		$nWeekId = (int) $vtWeekId;

		$objWeek = $objWeeks[ $nWeekId ];
		if ( $objWeek === null )
		{
			$objWeek = self::createObject();
			$objWeek->putId( $nWeekId );
			if ( $vtWeekName === null )
				$objWeek->putName( substr( (string)$nWeekId, 4, 2 ) );
			else
				$objWeek->putName( $vtWeekName );

			$objWeeks[ $nWeekId ] = $objWeek;
		}

		return $objWeek;
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON()
	 */
	public static function convertObjectToJSON( $objObject, $nDataFlag = null )
	{
		if ( $objObject === null )
			return "null";

		if ( static::isInPoolJSON( $objObject ) )
			return $objObject->getId();
		static::addToPoolJSON( $objObject );

		$nJSStartTimeStamp = $objObject->getStartDateTime()->getTimeStamp() * 1000;
		$nJSEndTimeStamp = $objObject->getEndDateTime()->getTimeStamp() * 1000;

		return
		"{
			\"Id\": ".$objObject->getId().",
			\"Name\": '".$objObject->getName()."',
			\"StartDateTime\": ".$nJSStartTimeStamp.",
			\"EndDateTime\": ".$nJSEndTimeStamp."
		}";
	}
}
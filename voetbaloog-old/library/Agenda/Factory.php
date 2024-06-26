<?php

/**
 * @copyright  	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 4533 2018-08-30 13:32:43Z thepercival $
 * @package	Agenda
 */

/**
 * @package Agenda
 */
class Agenda_Factory extends JSON_Factory implements Agenda_Factory_Interface, XML_Factory_Interface
{
	protected static $m_objSingleton;
	public static $m_szDateTimeFormat = "Y-m-d H:i:s";

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see Agenda_Factory_Interface::createTimeSlot()
	 */
	public static function createTimeSlot( $vtStartDateTime = null, $vtEndDateTime = null )
	{
		if( $vtStartDateTime === null )
			$vtStartDateTime = self::createDateTime();
		elseif ( is_string( $vtStartDateTime ) )
			$vtStartDateTime = self::createDateTime( $vtStartDateTime );

		if( $vtEndDateTime === null )
			$vtEndDateTime = self::createDateTime();
		elseif ( is_string( $vtEndDateTime ) )
			$vtEndDateTime = self::createDateTime( $vtEndDateTime );

		if( $vtStartDateTime === null or $vtEndDateTime === null )
			return null;

		// Maybe we should throw an exception here
		if( $vtStartDateTime > $vtEndDateTime )
			return null;

		$objTimeSlot = new Agenda_TimeSlot();
		$objTimeSlot->putId( microtime()."->".$vtStartDateTime->getTimestamp()."<->".$vtEndDateTime->getTimestamp() );
		$objTimeSlot->putStartDateTime( $vtStartDateTime );
		$objTimeSlot->putEndDateTime( $vtEndDateTime );

		return $objTimeSlot;
	}

	/**
	 * @see Agenda_Factory_Interface::createTimeSlot()
	 */
	public static function createTimeSlotNew( $vtStartDateTime = null, $vtEndDateTime = null )
	{
		if( $vtStartDateTime === null )
			$vtStartDateTime = self::createDateTime();
		elseif ( is_string( $vtStartDateTime ) )
			$vtStartDateTime = self::createDateTime( $vtStartDateTime );

		if ( is_string( $vtEndDateTime ) )
			$vtEndDateTime = self::createDateTime( $vtEndDateTime );

		if( $vtStartDateTime === null )
			return null;

		if( $vtEndDateTime !== null and $vtStartDateTime > $vtEndDateTime )
			return null;

		$oTimeSlot = new Agenda_TimeSlot();
		$oTimeSlot->putId( $vtStartDateTime->getTimestamp()."->". ( $vtEndDateTime !== null ? $vtEndDateTime->getTimestamp() : null ) );
		$oTimeSlot->putStartDateTime( $vtStartDateTime );
		$oTimeSlot->putEndDateTime( $vtEndDateTime );

		return $oTimeSlot;
	}

	/**
	 * @see Agenda_Factory_Interface::createTimeSlots()
	 */
	public static function createTimeSlots()
	{
		return new Agenda_TimeSlot_Collection();
	}

	/**
	 *
	 * @see Agenda_Factory_Interface::createAgenda()
	 */
	public static function createAgenda( $szId )
	{
		$objAgenda = new Agenda();
		$objAgenda->putId( $szId );
		return $objAgenda;
	}

	/**
	 * @see Agenda_Factory_Interface::createDate()
	 */
	public static function createDate( $vtDate = null )
	{
		if ( $vtDate !== null )
		{
			// if $vtDate is instanceof DateTime $vtDate is implicitly converted, CDK
			$nStartTimePos = strpos( $vtDate, " " );
			if ( $nStartTimePos !== false )
				$vtDate = substr( $vtDate, 0, $nStartTimePos );
		}
        $oDateTime = self::createDateTime( $vtDate );
        $oDateTime->setTime ( 0, 0 );
        return $oDateTime;
	}

	/**
	 * @see Agenda_Factory_Interface::createDateTime()
	 */
	public static function createDateTime( $szDateTime = null )
	{
		return new Agenda_DateTime( $szDateTime );
	}

	/**
	 * @see Agenda_Factory_Interface::createDay()
	 */
	public static function createDay( $objDate = null )
	{
		$objStartDate = Agenda_Factory::createDate( $objDate );
		$objEndDate = Agenda_Factory::createDate( $objDate );
		$objEndDate->modify("+1 days");
		return Agenda_Factory::createTimeSlot( $objStartDate, $objEndDate );
	}

	/**
	 * @see Agenda_Factory_Interface::getDuration()
	 */
	public static function getDuration( $objStartDateTime, $objEndDateTime, $nZendDateTimePart = null, $bFloored = false )
	{
		if ( ! ( $objStartDateTime instanceof DateTime ) ) return -1;
		if ( ! ( $objEndDateTime instanceof DateTime ) ) return -1;

		$nStartTimeStamp = $objStartDateTime->getTimeStamp();
		$nEndTimeStamp = $objEndDateTime->getTimeStamp();

		$vtDuration = abs( $nStartTimeStamp - $nEndTimeStamp );

		// default is seconds
		if ( $nZendDateTimePart === Zend_Date::MINUTE_SHORT )
		{
			$vtDuration /= 60;
		}
		elseif ( $nZendDateTimePart === Zend_Date::HOUR_SHORT )
		{
			$vtDuration /= 3600;
		}
		elseif ( $nZendDateTimePart === Zend_Date::DAY_SHORT )
		{
			$vtDuration /= 86400;
		}
		elseif ( $nZendDateTimePart === Zend_Date::WEEK )
		{
			$vtDuration /= ( 86400 * 7 );
		}
		elseif ( $nZendDateTimePart === Zend_Date::YEAR_SHORT )
		{
			$vtDuration /= 31556926;
		}
		elseif ( $nZendDateTimePart !== null )
		{
			throw new Exception("This datepart is not yet implemented", E_ERROR );
		}

		if ( $bFloored !== false )
			return ( (int) floor( $vtDuration ) );

		return ( (int) round( $vtDuration ) );
	}

	/**
	* @see Agenda_Factory_Interface::createOverlap()
	*/
	public static function createOverlap( $oTimeSlotA, $oTimeSlotB )
	{
		$oStartDateTime = $oTimeSlotB->getStartDateTime();
		if ( $oTimeSlotA->getStartDateTime() > $oStartDateTime )
			$oStartDateTime = $oTimeSlotA->getStartDateTime();

		$oEndDateTime = $oTimeSlotA->getEndDateTime();
		if ( $oEndDateTime === null or ( $oTimeSlotB->getEndDateTime() !== null and $oTimeSlotB->getEndDateTime() < $oEndDateTime ) )
		{
			$oEndDateTime = $oTimeSlotB->getEndDateTime();
		}
		return static::createTimeSlot( (string) $oStartDateTime, (string) $oEndDateTime );
	}

	public static function translate( $sDate, $bEnglishToDutch = true )
	{
		$sRet = strtolower( $sDate );

		$arrEnglish = array(
				"january","february","march","may","june","july","august",
				"monday","tuesday","wednesday","thursday","friday","saturday","sunday",
				"mar","oct",
				"mon","tue","wed","thu","fri","sat","sun",
		);
		$arrDutch = array(
				"januari","februari","maart","mei","juni","juli","augustus",
				"maandag","dinsdag","woensdag","donderdag","vrijdag","zaterdag","zondag",
				"mrt","okt",
				"maa","din","woe","don","vrij","zat","zon",
		);

		if ( $bEnglishToDutch === true )
			$sRet = str_replace( $arrEnglish, $arrDutch, $sRet );
		else
			$sRet = str_replace( $arrDutch, $arrEnglish, $sRet );
		return $sRet;
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

		$sJSEndTimeStamp = "null";
		$oEndDateTime = $objObject->getEndDateTime();
		if ( $oEndDateTime !== null )
			$sJSEndTimeStamp = $oEndDateTime->getTimeStamp() * 1000;

		return
		"{
			\"Id\": \"".$objObject->getId()."\",
			\"StartDateTime\": ".$nJSStartTimeStamp.",
			\"EndDateTime\": ".$sJSEndTimeStamp."
		}";
	}

	/**
	 * @see XML_Factory_Interface::convertObjectToXML()
	 */
	public static function convertObjectToXML( $oObject )
	{
		$sXml = "";
		if( $oObject instanceof Agenda_TimeSlot )
		{
			$sXml .= "<Agenda_TimeSlot>";
			$sXml .= "<StartDateTime>" . $oObject->getStartDateTime() . "</StartDateTime>";
			$sXml .= "<EndDateTime>" . $oObject->getEndDateTime() . "</EndDateTime>";
			$sXml .= "</Agenda_TimeSlot>";
		}
		return $sXml;
	}

	/**
	 * @see XML_Factory_Interface::convertObjectsToXML()
	 */
	public static function convertObjectsToXML( $oObjects )
	{
		$sXml = "";
		if( $oObjects instanceof Agenda_TimeSlot_Collection )
		{
			$sXml .= "<Agenda_TimeSlot_Collection>";
			$sXml .= "<Id>" . $oObjects->getId() . "</Id>";
			foreach( $oObjects as $oObject )
				$sXml .= static::convertObjectToXML( $oObject );
			$sXml .= "</Agenda_TimeSlot_Collection>";
		}
		elseif( $oObjects instanceof Patterns_Collection )
		{
			$sXml .= "<Collection>";
			foreach( $oObjects as $oObject )
			{
				if ( $oObject instanceof Patterns_Collection )
					$sXml .= static::convertObjectsToXML( $oObject );
			}
			$sXml .= "</Collection>";
		}
		return $sXml;
	}
}
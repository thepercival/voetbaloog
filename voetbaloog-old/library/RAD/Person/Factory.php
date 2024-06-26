<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		RAD
 */

/**
 * @package RAD
 */
class RAD_Person_Factory implements Patterns_Singleton_Interface
{
	protected static $m_oSingleton;

	/**
	 * A protected constructor; prevents direct creation of object
	 */
	protected function __construct(){}

	/**
	 * @see Patterns_Singleton_Interface::__clone()
	 */
	public function __clone()
	{
		throw new Exception("Cloning is not allowed.", E_ERROR );
	}

	/**
	 * @see Patterns_Singleton_Interface::getInstance()
	 */
	public static function getInstance()
	{
		if ( static::$m_oSingleton === null )
		{
			$sCalledClassName = get_called_class();
			static::$m_oSingleton = new $sCalledClassName();
		}
		return static::$m_oSingleton;
	}

	/**
	 * @see RAD_Person_Interface::getFullName()
	 */
	public static function getFullName( $sFirstName, $sLastNameCalled, $nCallType = null )
	{
		if ( $nCallType === RAD_Person::CALLTYPE_FULLNAME_FIRSTNAMELETTER )
			return strtoupper( substr( $sFirstName, 0, 1 ) ) . ". " . $sLastNameCalled;
		else if ( $nCallType === RAD_Person::CALLTYPE_FULLNAME_ORDER )
			throw new Exception("wrong call type");
		return $sFirstName . " " . $sLastNameCalled;
	}

	/**
	 * @see RAD_Person_Interface::getLastNameCalled()
	 */
	public static function getLastNameCalled( $sNameInsertions, $sLastName, $sNameInsertionsPartner = null, $sLastNamePartner = null, $nCallType = RAD_Person::CALLTYPE_LASTNAME, $bForOrdering = false )
	{
		if ( strlen( $sNameInsertions ) > 0 and strlen( $sLastName ) > 0 )
		{
			if ( $bForOrdering === false )
				$sLastName = $sNameInsertions . ' ' . $sLastName;
			else
				$sLastName = $sLastName . ' ' . $sNameInsertions;
		}

		if ( strlen( $sNameInsertionsPartner ) > 0 and strlen( $sLastNamePartner ) > 0 )
		{
			if ( $bForOrdering === false )
				$sLastNamePartner = $sNameInsertionsPartner . ' ' . $sLastNamePartner;
			else
				$sLastNamePartner = $sLastNamePartner . ' ' . $sNameInsertionsPartner;
		}

		$sLastNameCalled = null;
		if ( $nCallType === RAD_Person::CALLTYPE_LASTNAME )
			$sLastNameCalled = $sLastName;
		elseif ( $nCallType === RAD_Person::CALLTYPE_LASTNAME_LASTNAMEPARTNER )
			$sLastNameCalled = $sLastName . "-" . $sLastNamePartner;
		elseif ( $nCallType === RAD_Person::CALLTYPE_LASTNAMEPARTNER_LASTNAME )
			$sLastNameCalled = $sLastNamePartner . "-" . $sLastName;
		elseif ( $nCallType === RAD_Person::CALLTYPE_LASTNAMEPARTNER )
			$sLastNameCalled = $sLastNamePartner;
		return $sLastNameCalled;
	}
}
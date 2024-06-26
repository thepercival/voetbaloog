<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Factory.php 4558 2019-08-13 08:54:29Z thepercival $
 * @since	  File available since Release 4.0
 * @package	Construction
 */

/**
 * @package	Construction
 */
class Construction_Factory implements Construction_Factory_Interface, Patterns_Singleton_Interface, XML_Factory_Interface
{
	// Hold an instance of the class
	private static $m_objSingleton;
	protected static $m_objSearchOperators;
	protected static $m_nIdCounter;

	/**
	 * A protected constructor; prevents direct creation of object
	 */
	protected function __construct() {}

	/**
	 * @see Patterns_Singleton_Interface::__clone()
	 */
	public function __clone()
	{
		trigger_error( "Cloning is not allowed.", E_USER_ERROR);
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
	 * @see Construction_Factory_Interface::createReadProperty()
	 */
	public static function createReadProperty( $sObjectProperty )
	{
		$oConstructionOption = new Construction_Option_ReadProperty();
		$oConstructionOption->putId( "READ:" . $sObjectProperty );
		$oConstructionOption->putObjectProperty( $sObjectProperty );
		return $oConstructionOption;
	}

	/**
	 * @see Construction_Factory_Interface::createFilter()
	 */
	public static function createFilter( $szId )
	{
		$objConstructionOptionFilter = new Construction_Option_Filter();
		$objConstructionOptionFilter->putId( "FILTER:" . $szId );
		return $objConstructionOptionFilter;
	}

	/**
	 * @see Construction_Factory_Interface::createFilterExt()
	 */
	public static function createFilterExt( $szObjectProperty, $szSearchOperator, $vtValue )
	{
		$objSearchOperator = self::getSearchOperator( $szSearchOperator );
		if ( $objSearchOperator === null )
			throw new Exception( "Unknown filter ".$szSearchOperator, E_ERROR );
		$objConstructionOptionFilter = self::createFilter( $szObjectProperty . "-" . $objSearchOperator->getId() . "-" . MetaData_Factory::toString( $vtValue ) );
		$objConstructionOptionFilter->putObjectProperty( $szObjectProperty );
		$objConstructionOptionFilter->putSearchOperator( $objSearchOperator );
		$objConstructionOptionFilter->putValue( $vtValue );
		return $objConstructionOptionFilter;
	}

	/**
	 * @see Construction_Factory_Interface::createOptions()
	 */
	public static function createOptions()
	{
		return new Construction_Option_Collection();
	}

	/**
	 * @see Construction_Factory_Interface::createOptionsExt()
	 */
	public static function createOptionsExt( $vtId )
	{
		$oOptions = new Construction_Option_Collection();
		$oOptions->putId( $vtId );
		return $oOptions;
	}

	/**
	 * @see Construction_Factory_Interface::createFiltersForTimeSlots()
	 */
	public static function createFiltersForTimeSlots( $szClassName, $objTimeSlots, $nRange = 0 /*Agenda_TimeSlot::EXCLUDE_NONE*/, $bEndDateCanBeNull = false )
	{
		if ( $objTimeSlots instanceof Agenda_TimeSlot_Collection )
		{
			$objFilters = Construction_Factory::createOptions();

			if ( $objTimeSlots->count() > 0 )
			{
				$objOrFilters = Construction_Factory::createOptions();
				$objOrFilters->putId( $szClassName . "_OR_FILTERS");

				foreach ( $objTimeSlots as $szTimeSlotId => $objTimeSlot )
				{
					$objOrFiltersIt = self::createFiltersForTimeSlot( $szClassName, $objTimeSlot, $nRange, $bEndDateCanBeNull );
					$objOrFilters->add( $objOrFiltersIt );
				}

				$objFilters->add( $objOrFilters );
			}
			return $objFilters;
		}
		else // TimeSlot or DateTime
		{
			return self::createFiltersForTimeSlot( $szClassName, $objTimeSlots, $nRange, $bEndDateCanBeNull );
		}

		return null;
	}

	/**
	 * helper for createFiltersForTimeSlots
	 */
	protected static function createFiltersForTimeSlot( $szClassName, $objTimeSlot, $nRange, $bEndDateCanBeNull )
	{
		$objFilters = Construction_Factory::createOptions();
		if ( $objTimeSlot === null )
  			return $objFilters;

		$objStartDateTime = null;
		$objEndDateTime = null;
		if ( $objTimeSlot instanceof Agenda_TimeSlot_Interface )
		{
			$objStartDateTime = $objTimeSlot->getStartDateTime();
			$objEndDateTime = $objTimeSlot->getEndDateTime();
		}
		elseif ( $objTimeSlot instanceof DateTime )
		{
			$objStartDateTime = $objTimeSlot;
			$objEndDateTime = $objTimeSlot;
		}

		if ( $objEndDateTime !== null )
			$objFilters->addFilter( $szClassName."::StartDateTime", "SmallerThan", $objEndDateTime );

		if ( $objStartDateTime !== null )
		{
			if ( $bEndDateCanBeNull === true )
			{
				$objOrFilters = Construction_Factory::createOptions();
				$objOrFilters->putId( $szClassName . "_EndDateNull".$objTimeSlot->getId() );

				$objOrFilters->addFilter( $szClassName."::EndDateTime", "GreaterThan", $objStartDateTime );
				$objOrFilters->addFilter( $szClassName."::EndDateTime", "EqualTo", null );

				$objFilters->add( $objOrFilters );
			}
			else
			{
				$objFilters->addFilter( $szClassName."::EndDateTime", "GreaterThan", $objStartDateTime );
			}
		}

		if ( ( Agenda_TimeSlot::EXCLUDE_BEFORESTART & $nRange ) === Agenda_TimeSlot::EXCLUDE_BEFORESTART )
  			$objFilters->addFilter( $szClassName."::StartDateTime", "SmallerThan", $objStartDateTime );

		if ( ( Agenda_TimeSlot::EXCLUDE_AFTEREND & $nRange ) === Agenda_TimeSlot::EXCLUDE_AFTEREND )
  			$objFilters->addFilter( $szClassName."::EndDateTime", "GreaterThan", $objEndDateTime );

  		$objFilters->putId( $objTimeSlot->getId() );
		return $objFilters;
	}

	/**
	 * @see Construction_Factory_Interface::createOrder()
	 */
	public static function createOrder( $szObjectProperty, $bDescending )
	{
		$objConstructionOptionOrder = new Construction_Option_Order();
		$objConstructionOptionOrder->putId( "ORDER:" . $szObjectProperty );
		$objConstructionOptionOrder->putObjectProperty( $szObjectProperty );
		$objConstructionOptionOrder->putDescending( $bDescending );
		return $objConstructionOptionOrder;
	}

	/**
	 * @see Construction_Factory_Interface::createLimit()
	 */
	public static function createLimit( $nCount, $nOffSet = null )
	{
		$objConstructionOptionLimit = new Construction_Option_Limit();
		$objConstructionOptionLimit->putId( "LIMIT:" . $nCount );
		$objConstructionOptionLimit->putCount( $nCount );
		$objConstructionOptionLimit->putOffSet( $nOffSet );
		return $objConstructionOptionLimit;
	}

	/**
	 * @see Construction_Factory_Interface::createOrders()
	 */
	public static function createOrders()
	{
		return new Construction_Option_Collection();
	}



	/**
	 * @see Construction_Factory_Interface::createSearchOperator()
	 */
	public static function createSearchOperator()
	{
		return new Construction_SearchOperator();
	}

	/**
	 * @see Construction_Factory_Interface::getSearchOperator()
	 */
	public static function getSearchOperator( $szId )
	{
		$objSearchOperators = self::getSearchOperators();
		return $objSearchOperators[ $szId ];
	}

	/**
	 * @see Construction_Factory_Interface::getSearchOperators()
	 */
	public static function getSearchOperators()
	{
		if ( self::$m_objSearchOperators === null )
		{
			self::$m_objSearchOperators = Patterns_Factory::createCollection();
			{
				$objSearchOperator= self::createSearchOperator();
				$objSearchOperator->putId("EqualTo");
				$objSearchOperator->putDescription("Gelijk aan");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}

			{
				$objSearchOperator= self::createSearchOperator();
				$objSearchOperator->putId("NotEqualTo");
				$objSearchOperator->putDescription("Ongelijk aan");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}

			{
				$objSearchOperator = self::createSearchOperator();
				$objSearchOperator->putId("StartsWith");
				$objSearchOperator->putDescription("Begint met");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}

			{
				$objSearchOperator = self::createSearchOperator();
				$objSearchOperator->putId("EndsWith");
				$objSearchOperator->putDescription("Eindigt met");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}

			{
				$objSearchOperator = self::createSearchOperator();
				$objSearchOperator->putId("GreaterThan");
				$objSearchOperator->putDescription("Groter dan");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}

			{
				$objSearchOperator = self::createSearchOperator();
				$objSearchOperator->putId("GreaterThanOrEqualTo");
				$objSearchOperator->putDescription("Groter dan of gelijk aan");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}

			{
				$objSearchOperator = self::createSearchOperator();
				$objSearchOperator->putId("SmallerThan");
				$objSearchOperator->putDescription("Kleiner dan");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}

			{
				$objSearchOperator = self::createSearchOperator();
				$objSearchOperator->putId("SmallerThanOrEqualTo");
				$objSearchOperator->putDescription("Kleiner dan of gelijk aan");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}

			{
				$objSearchOperator = self::createSearchOperator();
				$objSearchOperator->putId("Like");
				$objSearchOperator->putDescription("Ongeveer gelijk aan");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}

			{
				$objSearchOperator = self::createSearchOperator();
				$objSearchOperator->putId("NotLike");
				$objSearchOperator->putDescription("Niet ongeveer gelijk aan");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}

			{
				$objSearchOperator = self::createSearchOperator();
				$objSearchOperator->putId("In");
				$objSearchOperator->putDescription("Zit in");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}

			{
				$objSearchOperator = self::createSearchOperator();
				$objSearchOperator->putId("BinaryIn");
				$objSearchOperator->putDescription("Binair in");
				$objSearchOperator->putNumberOfParameters( 1 );
				self::$m_objSearchOperators->add( $objSearchOperator );
			}
		}
		return self::$m_objSearchOperators;
	}

	/**
	 * @see XML_Factory_Interface::convertObjectsToXML()
	 */
	public static function convertObjectsToXML( $oObjects )
	{
		$sXml = "<Construction_Collection>";
		foreach($oObjects as $oObject)
		{
			if( $oObject instanceof Construction_Option_Collection )
				$sXml .= static::convertObjectsToXML( $oObject );
			else
				$sXml .= static::convertObjectToXML($oObject);
		}
		$sXml .= "</Construction_Collection>";
		return $sXml;
	}

	/**
	 * @see XML_Factory_Interface::convertObjectToXML()
	 */
	public static function convertObjectToXML( $oObject )
	{
		$sXml = "<Construction_Option>";
		if( $oObject instanceof Construction_Option_Filter )
		{
			$sXml .= "<Filter>";
			$sXml .= "<SearchOperator>" . $oObject->getSearchOperator() . "</SearchOperator>";
			$sXml .= "<ObjectProperty>" . $oObject->getObjectProperty() . "</ObjectProperty>";
			$sXml .= "<Value>" . $oObject->getValue() . "</Value>";
			$sXml .= "</Filter>";
		}
		elseif( $oObject instanceof Construction_Option_Order )
		{
			$sXml .= "<Order>";
			$sXml .= "<Id>" . $oObject->getId() . "</Id>";
			$sXml .= "<ObjectProperty>" . $oObject->getObjectProperty() . "</ObjectProperty>";
			$sDescending = "false";
			if( $oObject->getDescending() )
				$sDescending = "true";
			$sXml .= "<Descending>" . $sDescending . "</Descending>";
			$sXml .= "</Order>";
		}
		elseif( $oObject instanceof Construction_Option_ReadProperty )
		{
			$sXml .= "<ReadProperty>";
			$sXml .= "<ObjectProperty>" . $oObject->getObjectProperty() . "</ObjectProperty>";
			$sXml .= "</ReadProperty>";
		}
		elseif( $oObject instanceof Construction_Option_Limit )
		{
			$sXml .= "<Limit>";
			$sXml .= "<Count>" . $oObject->getCount() . "</Count>";
			$sXml .= "<OffSet>" . $oObject->getOffSet() . "</OffSet>";
			$sXml .= "</Limit>";
		}
		$sXml .= "</Construction_Option>";
		return $sXml;
	}

	public static function createConstructionFromXmlString( $sXml )
	{
		$oXml = new SimpleXMLElement( $sXml );
		if( strcasecmp( $oXml->getName(), "Construction_Collection" ) === 0 )
		{
			// Create Construction filter collection
			$oOptions = static::createOptions();
			foreach( $oXml->children() as $oChild )
			{
				if( strcasecmp( $oChild->getName(), "Construction_Collection" ) === 0 )
				{
					$oOptionCollection = self::createConstructionFromXmlString( $oChild->saveXML() );
					$aAttributes = $oChild->attributes();
					if( count( $aAttributes ) > 0 )
					{
						if( $aAttributes['Id'] !== null )
							$oOptionCollection->putId( html_entity_decode( (string)$aAttributes['Id'] ) );
						else
							$oOptionCollection->putId( static::$m_nIdCounter++ );
					}
					$oOptions->add( $oOptionCollection );
				}
				else
				{
					$oItem = $oChild[0];
					$oOption = self::createOptionFromXml( $oItem );
					$aAttributes = $oChild->attributes();
					if( count( $aAttributes ) > 0 )
					{
                        if( $aAttributes['Id'] !== null )
                            $oOption->putId( html_entity_decode( (string)$aAttributes['Id'] ) );
                        else
                            $oOption->putId( static::$m_nIdCounter++ );
					}
					$oOptions->add( $oOption );
				}
			}
			return $oOptions;
		}
		else
			return self::createOptionFromXml( $oXml );
	}

	public static function createOptionFromXml( SimpleXMLElement $oXml )
	{
		if( isset( $oXml->Filter ) )
		{
			// create filter
			$oFilter = Construction_Factory::createFilterExt(
				( string )$oXml->Filter->ObjectProperty,
				( string )$oXml->Filter->SearchOperator,
				( string )$oXml->Filter->Value
			);
			return $oFilter;
		}
		elseif( isset( $oXml->Order ) )
		{
			// create order
			$bDescending = false;
			if( strcasecmp( $oXml->Order->Descending, "true" ) === 0 )
				$bDescending = true;
			elseif( strcasecmp( $oXml->Order->Descending, "1" ) === 0)
				$bDescending = true;
			$oOrder = self::createOrder( ( string )$oXml->Order->ObjectProperty, $bDescending );
			return $oOrder;
		}
		elseif( isset( $oXml->ReadProperty ) )
		{
			return static::createReadProperty( (string) $oXml->ReadProperty->ObjectProperty );
		}
		elseif( isset($oXml->Limit ) )
		{
			return static::createLimit( $oXml->Limit->Count, $oXml->Limit->OffSet );
		}
	}
}
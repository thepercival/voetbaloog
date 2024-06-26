<?php

/**
 * XMLReader.php
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Reader.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 * @since	  File available since Release 4.0
 * @package	Source
 */


/**
 * Inherits from Reader and implements the interface XMLReader_Interface.
 *
 * @package Source
 */
class Source_XML_Reader extends Source_Reader implements Source_XML_Reader_Interface
{
	protected $m_vtXML;

	public function __construct()
	{
		parent::__construct();
	}

	 /**
	 * Defined by XMLReader_Interface; gets the source
	 *
	 * @see XMLReader_Interface::getSource()
	 */
	public function getSource()
	{
		return $this->m_vtXML;
	}

	 /**
	 * Defined by XMLReader_Interface; puts the source
	 *
	 * @see XMLReader_Interface::putSource()
	 */
	public function putSource( $vtXML )
	{
		$this->m_vtXML = $vtXML;
	}

	protected function getFromArray( $propsparam, $szName )
	{
		foreach ( $propsparam as $id => $objprop )
		{
			if ( $objprop->name === $szName )
				return $objprop;
		}
		return null;
	}

	/**
	 * NONINTERFACE
	 **/
	public static function createObjectFromSimpleXMLElement( $objSimpleXMLElement )
	{
		$arrnameSpaces = $objSimpleXMLElement->getNamespaces();
		$szNameSpace = $arrnameSpaces[""];
		if ( strlen ( $szNameSpace ) > 0 )
			$szNameSpace = substr( $szNameSpace, strrpos( $szNameSpace, "/" ) + 1 );

		if ( strlen ( $szNameSpace ) === 0 )
			throw new Exception("No Namespace is set", E_ERROR );

		$szEntityName = $objSimpleXMLElement->getName();
		// temperarely until php 5.3
		$szFactory = $szNameSpace."_".$szEntityName."_Factory";
        $objObject = eval($szFactory . '::createObject();');

		foreach ( $objSimpleXMLElement->children() as $ProperyElement )
		{
			//$szValue = Source_XML_Factory::replaceSpecialChars( (string)$ProperyElement, false );
			$szValue = (string)$ProperyElement;
			MetaData_Factory::putValue( $objObject, "::".$ProperyElement->getName(), $szValue );
		}

		return $objObject;
	}

	/**
	 *
	 * NonInterfacemethod
	 *
	 */
	public static function createDOMDocument( $szEntity )
	{
		// Creates a DOMDocument instance
		$implDOM = new DOMImplementation();
		$objDOMDTD = $implDOM->createDocumentType( $szEntity, "-//W3C//DTD HTML 4.01//EN", "");
		$objDom = $implDOM->createDocument( "", "", $objDOMDTD);
		$objDom->formatOutput = true;
		$objDom->encoding = "UTF-8";
		$objDom->standalone = true;
		return $objDom;
	}

	/**
	 *
	 * NonInterfacemethod
	 *
	 */
	public static function stringToSimpleXML( $szXML )
	{
		if ( strlen( $szXML) === 0 )
			return null;
		$vtEnc = mb_detect_encoding( $szXML );
		$szXML = mb_convert_encoding( $szXML, "UTF-8", $vtEnc );
		return new SimpleXMLElement( $szXML );
	}

	public static function fileToSimpleXML( $szLocation )
	{
		if ( ! is_file ( $szLocation ) )
			return "404 File not found!";

		$handle = fopen( $szLocation, "r");
		$szXML = fread( $handle, filesize( $szLocation ) );
		fclose( $handle );

		if ( strlen( $szXML) === 0 )
			return null;
		$vtEnc = mb_detect_encoding( $szXML );
		$szXML = mb_convert_encoding( $szXML, "UTF-8", $vtEnc );
		return new SimpleXMLElement( $szXML );
	}

	/**
	 * @see Source_XML_Reader_Interface::createObjectFromXMLString()
	 */
	public static function createObjectFromXMLString( $sXml )
	{
		if( $sXml === "" or $sXml === null )
			return null;
		$oIterator = new SimpleXMLIterator( $sXml );
		return static::createObjectFromSimpleXMLIterator( $oIterator );
	}

	public static function createObjectFromClassName( $sClassName )
	{
		switch( strtolower( $sClassName ) )
		{
			case 'collection':
				return Patterns_Factory::createCollection();
				break;

			case 'agenda_timeslot_collection':
				return Agenda_Factory::createTimeSlots();
				break;

			case 'agenda_timeslot':
				return Agenda_Factory::createTimeSlot();
				break;

			default:
				$sFactory = $sClassName . "_Factory";
				if( class_exists( $sFactory ) )
					return $sFactory::createObject();
				else
					return null;
		}
	}

	/**
	 * @static
	 * @param SimpleXMLIterator $oIterator
	 * @return mixed
	 */
	public static function createObjectFromSimpleXMLIterator( SimpleXMLIterator $oIterator )
	{
		$sClass = $oIterator->getName();
		$oObject = static::createObjectFromClassName( $sClass );

		for( $oIterator->rewind(); $oIterator->valid(); $oIterator->next() )
		{
			$sProperty = $oIterator->key();
			if( strcasecmp( $sProperty, "Collection" ) === 0 )
			{
				$oChildObject = static::createObjectFromClassName( $sProperty );
				foreach( $oIterator->getChildren() as $xmlChild )
				{
					$oChildChildObject = static::createObjectFromSimpleXMLIterator( $xmlChild );
					$oChildObject->add( $oChildChildObject );
				}
				if( $oObject instanceof Agenda_TimeSlot_Collection )
					$oObject->addCollection( $oChildObject );
				else
					$oObject = $oChildObject;
			}
			elseif( $oIterator->hasChildren() )
			{
				$oChildObject = static::createObjectFromSimpleXMLIterator( $oIterator->getChildren() );

				if( $oObject === null )
					$oObject = $oChildObject;
				elseif( $oObject instanceof Patterns_Collection )
				{
					$oObject->add( $oChildObject );
				}
				else
				{
					MetaData_Factory::putValue( $oObject, "::" . $sProperty, $oChildObject );
				}
			}
			else
			{
				$sValue = html_entity_decode( (string) $oIterator->current() );

				if( $sValue === "" )
					$sValue = null;

				MetaData_Factory::putValue( $oObject, "::" . $sProperty, $sValue );
			}
		}
		return $oObject;
	}
}
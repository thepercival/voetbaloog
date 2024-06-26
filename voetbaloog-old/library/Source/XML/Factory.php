<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Factory.php 4557 2019-08-12 18:50:59Z thepercival $
 * @since	  File available since Release 4.0
 * @package	   Source
 */

/**
 * @package Source
 */
class Source_XML_Factory implements Source_XML_Factory_Interface, Patterns_Singleton_Interface
{
	private static $m_objSingleton;
	private static $m_szXMLNameSpace;

  	protected function __construct(){}

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
	 * @see Source_XML_Factory_Interface::getXMLNameSpace()
	 */
	public static function getXMLNameSpace( $szPostFix )
	{
		if ( self::$m_szXMLNameSpace === null )
		{
			$objCfgXML = new Zend_Config_Ini( APPLICATION_PATH . "/configs/config.ini", "xml");
			self::$m_szXMLNameSpace = $objCfgXML->get("namespace").$szPostFix;
		}
		return self::$m_szXMLNameSpace;
	}

	/**
	 * @see Source_XML_Factory_Interface::replaceSpecialChars()
	 */
	public static function replaceSpecialChars( $szValue, $bToXML = true )
	{
		if ( $bToXML === true )
		{
			$szValue = str_replace ("&", "&amp;", $szValue );
			$szValue = str_replace ("<", "&lt;", $szValue );
			$szValue = str_replace (">", "&gt;", $szValue );
			$szValue = str_replace ("\"", "&quot;", $szValue );
			$szValue = str_replace ("'", "&#39;", $szValue );
		}
		elseif ( $bToXML === false )
		{
			$szValue = str_replace ("&amp;", "&", $szValue );
			$szValue = str_replace ("&lt;", "<", $szValue );
			$szValue = str_replace ("&gt;", ">", $szValue );
			$szValue = str_replace ("&quot;", "\"", $szValue );
			$szValue = str_replace ("&#39;", "'", $szValue );
		}
		return $szValue;
	}

	public static function createDOMDocumentFromObject( $objObjects, $objXMLProperties )
	{
		$objSimpleXML = self::realCreateSimpleXMLFromObject( $objObjects, $objXMLProperties );

		if ( $objSimpleXML === null )
			return null;

		$objDOMXML = dom_import_simplexml( $objSimpleXML );

		$domDOC = new DOMDocument("1.0");
		$domDOC->formatOutput = true;

		$domDOC->encoding = "UTF-8";
		$domDOC->standalone = true;

		$objDOMXML = $domDOC->importNode( $objDOMXML , true);
		$objDOMXML = $domDOC->appendChild( $objDOMXML );

		return $domDOC;
	}

	public static function createSimpleXMLFromObject( $objObjects, $objXMLProperties )
	{
		return self::realCreateSimpleXMLFromObject( $objObjects, $objXMLProperties );
	}

	protected static function realCreateSimpleXMLFromObject( $objObjects, $objXMLProperties )
	{
		$szRootEntity = null;

		foreach ( $objXMLProperties as $objXMLProperty )
		{
			$szArgument = $objXMLProperty->getId();
			$nPos = strpos( $szArgument, "::" );
			$szRootEntityTmp = substr( $szArgument, 0, $nPos );
			if ( $szRootEntity === null )
				$szRootEntity = $szRootEntityTmp;

			if ( $szRootEntity != $szRootEntityTmp )
				throw new Exception("Rootproperty is not equal to other rootproperty", E_ERROR );
		}

		$objSimpleXML = new SimpleXMLElement( "<root></root>" );
		self::createSimpleXMLFromObjectHelper( $objObjects, "", $objSimpleXML, $objXMLProperties, $szRootEntity );

		// Return first entity
		foreach ( $objSimpleXML->children() as $objSimpleXMLChild )
			return $objSimpleXMLChild;
	}

	private static function createSimpleXMLFromObjectHelper( $objObject, $szSearchString, $objSimpleXML, $objXMLProperties, $szClassName )
	{
		if ( $objObject === null )
			return;

		if ( $objObject instanceof Patterns_Collection_Interface or $objObject instanceof Patterns_Idable_Interface )
		{
			if ( strlen( $szSearchString ) > 0 )
				$szSearchString .= "::";
			$szSearchString .= $szClassName;

			$szXMLName = $szClassName;
			$nStrPos = strpos( $szClassName, "_" );
			$szPostFix = "";
			if ( $nStrPos !== false )
			{
				// if $szClassName = X_Y than $szPostFix = X and $szXMLName = Y
				$szXMLName = substr( $szClassName, $nStrPos + 1 );
				$szPostFix = substr( $szClassName, 0, $nStrPos );
			}

			$objSimpleXMLChild = $objSimpleXML->addChild( $szXMLName, null, self::getXMLNameSpace( $szPostFix ) );

			if ( $objObject instanceof Patterns_Collection )
			{
				foreach ( $objObject as $objObjectTmp )
				{
					$szClassName = get_class( $objObjectTmp );
					self::createSimpleXMLFromObjectHelper( $objObjectTmp, $szSearchString, $objSimpleXMLChild, $objXMLProperties, $szClassName );
				}
			}
			elseif ( $objObject instanceof Patterns_Idable_Interface )
			{
				$objXMLPropertiesTmp = Patterns_Factory::createCollection();
				foreach ( $objXMLProperties as $objXMLProperty )
				{
					$objXMLPropertyTmp = Patterns_Factory::createValuable( $objXMLProperty->getId(), $objXMLProperty->getValue() );
					$objXMLPropertiesTmp->add( $objXMLPropertyTmp );
				}

				$objXMLPropertiesWalkedThrough = Patterns_Factory::createCollection();

				foreach ( $objXMLProperties as $szXMLProperty => $objXMLProperty )
				{
					$szBasePart = substr( $szXMLProperty, 0, strlen( $szSearchString ) );
					if ( $szBasePart === $szSearchString )
					{
						$szPostSearchString = substr( $szXMLProperty, strlen( $szSearchString ) + 2 );

						$nPos = strpos( $szPostSearchString, "::" );

						if ( $nPos > 0  )
						{
							$szPostSearchString = substr( $szPostSearchString, 0, $nPos );
						}

						$vtValue = MetaData_Factory::getValue( $objObject, "::".$szPostSearchString );

						if ( $nPos > 0 )
						{
							$objXMLPropertyWalkedThrough = $objXMLPropertiesWalkedThrough[$szSearchString."::".$szPostSearchString];

							if ( $objXMLPropertyWalkedThrough === null )
							{
								self::createSimpleXMLFromObjectHelper( $vtValue, $szSearchString, $objSimpleXMLChild, $objXMLPropertiesTmp, $szPostSearchString );
								$objXMLPropertyWalkedThrough = Patterns_Factory::createValuable( $szSearchString."::".$szPostSearchString, $objXMLProperty->getValue() );
								$objXMLPropertiesWalkedThrough->add( $objXMLPropertyWalkedThrough );
							}
						}
						else
						{
							if ( $vtValue !== null )
							{
								if ( $vtValue instanceof DateTime )
								{
									$objSimpleXMLChild->addChild( $objXMLProperty->getValue()."Date", $vtValue->format( "Y-m-d") );
									$objSimpleXMLChild->addChild( $objXMLProperty->getValue()."Time", $vtValue->format( "H:i:s") );
								}
								else
								{
									$objSimpleXMLChild->addChild( $objXMLProperty->getValue(), $vtValue );
								}
							}
						}
					}
				}
			}
		}
	}
}
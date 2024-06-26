<?php

/**
 *
 *
 * Defines and implements the interface IXMLReader
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 *
 * @package    Source
 */

/**
 *
 *
 * @package Source
 */
interface Source_XML_Factory_Interface
{
	/**
	 * gets the namespace
	 *
	 * @param string $szPostFix	The PostFix
	 * @return string	the namespace
	 */
	public static function getXMLNameSpace( $szPostFix );
	/**
	 * replaces specials character in the string as return value
	 *
	 * @param string	$szValue	value to convert
	 * @param bool		$bToXML		from or to xml
	 * @return string	the newly created string
	 */
	public static function replaceSpecialChars( $szValue, $bToXML = true );
	/**
	 * creates a domdocument from a collection
	 *
	 * @param Patterns_Collection_Interface | Patterns_Idable_Interface $objObjects The Objects
	 * @param Patterns_Collection_Interface | Patterns_Idable_Interface $objXMLProperties The XMLProperties
	 * @return DOMDocument|null	the newly created string
	 */
	public static function createDOMDocumentFromObject( $objObjects, $objXMLProperties );
	/**
	 * creates a simplexmlelement from a collection
	 *
	 * @param Patterns_Collection_Interface | Patterns_Idable_Interface $objObjects The Objects
	 * @param Patterns_Collection_Interface | Patterns_Idable_Interface $objXMLProperties The XMLProperties
	 * @return string	the newly created string
	 */
	public static function createSimpleXMLFromObject( $objObjects, $objXMLProperties );
}
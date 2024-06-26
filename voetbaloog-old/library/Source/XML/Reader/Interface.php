<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
interface Source_XML_Reader_Interface
{
	/**
	 * gets the source
	 *
	 * @return 	string 	The xml
	 */
	public function getSource();
	/**
	 * puts the source
	 *
	 * @param 	string 	$szXML	The xml
	 */
	public function putSource( $szXML );
    /**
     * converts xml string to object
     * 
     * @param string $sXml
     * @return object the object or collection of objects
     */
    public static function createObjectFromXMLString( $sXml );
}
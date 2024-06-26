<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 * @package    XML
 */

/**
 * @package XML
 */
interface XML_Factory_Interface
{
    /**
     * convert objects from php to XML
     *
     * @param 	mixed $oObject the object to convert to xml
     * @return 	string
     */
    public static function convertObjectToXML( $oObject );

    /**
     * convert collection of objects from php to XML
     *
     * @param Patterns_Collection_Interface $oObjects the collection of objects to convert to XML
     * @return string the result xml. This may contain a property to describe the type (like <collection type = 'abc'>) but is ignored
     */
    public static function convertObjectsToXML( $oObjects );
}
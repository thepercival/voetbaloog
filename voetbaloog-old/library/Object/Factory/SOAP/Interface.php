<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 3735 2012-07-17 14:12:42Z thepercival $
 *
 * @package	   Object
 */

/**
 * @package Object
 */
interface Object_Factory_SOAP_Interface
{	
	/**
	 * Creates an object from the soapclient
	 *
	 * @param Construction_Option_Collection	$oOptions	The Options
	 * @return Patterns_Idable_Interface		An instance of an object of class $szClassName
	 */
	public static function createObjectFromSOAP( Construction_Option_Collection $oOptions = null );
	/**
	 * Creates a collection of objects from the soapclient
	 *
	 * @param Construction_Option_Collection	$oOptions	The Options
	 * @return Patterns_Collection A collection of objects of class $szClassName
	 */
	public static function createObjectsFromSOAP( Construction_Option_Collection $oOptions = null ): Patterns_Collection;
}
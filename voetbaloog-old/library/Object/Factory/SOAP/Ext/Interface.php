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
interface Object_Factory_SOAP_Ext_Interface
{	
	/**
	 * Creates a collection of objects from the database from a many-to-many relation
	 *
	 * @param Patterns_Idable_Interface | Patterns_Collection	$oObject		The Object(s) which server as filter and describes which relation should be used
	 * @param Construction_Option_Collection			        $oOptions		The Construction Options
	 * @param string									        $sClassName		If the first param is null this param describes which relation should be used
	 * @return Patterns_Collection A collection of objects
	 */
	public static function createObjectsFromSOAPExt( $oObject, $oOptions = null, $sClassName = null ): Patterns_Collection;
}
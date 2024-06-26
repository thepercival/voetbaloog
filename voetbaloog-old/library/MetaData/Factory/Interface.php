<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 *
 * @package	   MetaData
 */

/**
 *
 * @package MetaData
 */
interface MetaData_Factory_Interface
{
	/**
	 * Creates a collection of properties, param: A variable length parameter list, the first item represent the class,
	 * all following items represent objectproperties
     *
	 * @return Patterns_Collection_Interface the objectproperties
	 */
	public static function createObjectPropertiesExt( /* variable param list */ );
	/**
	 * Creates a new instance of ObjectChange
	 *
	 * @param Patterns_Idable_Interface	$oObject			The object
	 * @param string			        $sObjectProperty	The objectproperty
	 * @param array				        $arrParams			The params
	 * @return mixed			The value of the objectproperty
	 */
	public static function getValue( $oObject, $sObjectProperty, $arrParams = array() );
	/**
	 * Creates a new instance of ObjectChange
	 *
	 * @param Patterns_Idable_Interface	$objObject			The object
	 * @param string            		$szObjectProperty	The objectproperty
	 * @param mixed				        $vtValue			The value to be set
	 * @return null
	 */
	public static function putValue( $objObject, $szObjectProperty, $vtValue );
	/**
	 * converts a variant to a string
	 *
	 * @param mixed			$vtVariant			The variant value
	 * @return string		the convert value of the variant
	 */
	public static function toString( $vtVariant );
	/**
	 * gets the class name
	 *
	 * @param  string				$szObjectProperty	The objectproperty
	 * @return string				The classname
	 */
	public static function getClassName( $szObjectProperty );
	/**
	 * gets the objectproperty
	 *
	 * @param  string				$szObjectProperty	The objectproperty
	 * @return string				the objectproperty
	 */
	public static function getPropertyName( $szObjectProperty );
}
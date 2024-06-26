<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4554 2019-08-12 14:37:34Z thepercival $
 * @package    JSON
 */

/**
 * @package JSON
 */
interface JSON_Factory_Interface
{
	/**
	 * convert objects from php to JSON
	 *
	 * @param 	Patterns_Collection	$objObjects		the objects to convert to json
	 * @return 	string
	 */
	public static function convertObjectsToJSON( $objObjects, $nDataFlag = null );
	/**
	 * convert objects from php to JSON
	 *
	 * @param 	Patterns_Idable_Interface	$objObject		the object to convert to json
	 * @return 	string
	 */
	public static function convertObjectToJSON( $objObject, $nDataFlag = null );
	/**
	 * set json get from cache off
	 *
	 * @return 	null
	 */
	public static function disableJSONPool();
}
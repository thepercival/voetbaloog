<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4108 2014-07-03 11:05:20Z johanneskonst $
 * @since      File available since Release 4.0
 * @package	   Object
 */

/**
 * @package Object
 */
interface Object_Factory_Db_Ext_Nr_Interface
{	
	/**
	 * gets the nr of objects from the database from a many-to-many relation
	 *
	 * @param Patterns_Idable_Interface | Patterns_Collection	$oObject		The Object(s) which server as filter and describes which relation should be used
	 * @param Construction_Option_Collection	                $oOptions		The Construction Options
	 * @param string							                $sClassName		If the first param is null this param describes which relation should be used
	 * @return int
	 */
	public static function getNrOfObjectsFromDatabaseExt( $oObject, Construction_Option_Collection $oOptions = null, string $sClassName = null ): int;
}
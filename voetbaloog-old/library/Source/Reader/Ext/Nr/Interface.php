<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 3735 2012-07-17 14:12:42Z thepercival $
 * @since      File available since Release 4.0
 * @package	   Source
 */

/**
 * @package Source
 */
interface Source_Reader_Ext_Nr_Interface
{
    /**
     * gets the number of objects from the database from a many-to-many relation
     *
     * @param Patterns_Idable_Interface | Patterns_Collection	$oObject		The Object(s) which server as filter and describes which relation should be used
     * @param Construction_Option_Collection	                $oOptions		The Construction Options
     * @param string							                $sClassName		If the first param is null this param describes which relation should be used
     * @return int
     */
    public function getNrOfObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): int;
}
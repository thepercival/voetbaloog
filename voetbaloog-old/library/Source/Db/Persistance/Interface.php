<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
interface Source_Db_Persistance_Interface
{
	/**
	 * gets the objectproperties
	 *
	 * @return Patterns_Collection	the object properties
	 */
	public function getObjectProperties();
	/**
	 * gets the columnname for a property
	 *
	 * @param  string               $sObjectProperty The objectproperty which will be queried
	 * @param  bool					$bPlusTable			Default is false
	 * @return string	columnname
	 */
	public function getColumnName( string $sObjectProperty, bool $bPlusTable = false );
	/**
	 * gets the columns
	 *
	 * @param  string					    $vtObjectProperty  		The objectproperty to look for
	 * @param  bool							$bPlusTable			    Default is false
	 * @return 	Patterns_Collection	    The columns related with the objectproperties
	 */
	public function getColumnNames( string $vtObjectProperty, bool $bPlusTable = false );
}
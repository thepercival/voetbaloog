<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 3886 2013-04-23 12:01:47Z cdunnink $
 *
 * @package    Source
 */

/**
 * This interface is implementend when the reader should be called from
 * another reader. This happens when more than one objecttype is instantiated
 * from one row(which is composed of more than one table usually)
 *
 * @package Source
 */
interface Source_Db_Reader_Foreign_Interface
{
	/**
	 * gets the Objects from an array
	 *
	 * @param  array	$arrRows    The data
	 * @return Patterns_Collection	The objects
	 */
	public function createObjectsFromRowsForeign( $arrRows );
	/**
	 * gets the Object from an array
	 *
	 * @param  array	$arrRow     The data
	 * @return Patterns_Idable_Interface	The object
	 */
	public function createObjectFromRowForeign( $arrRow );
	/**
	 * gets an associative array of foreign columns
	 *
	 * @return array	the foreign columns
	 */
	public function getForeignColumns();
}
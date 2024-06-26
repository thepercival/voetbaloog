<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 *
 * @package	   Source
 */

/**
 *
 *
 * @package Source
 */
interface Source_CSV_Reader_Interface
{
	/**
	 * gets the csvdata as an array
	 *
	 * @param string $szLocation The filelocation
	 * @param string $szSeperator The seperator between the columns, default is ";"
	 * @return array	the csvdata
	 */
	public static function getCSVData( $szLocation, $szSeperator = null );
}
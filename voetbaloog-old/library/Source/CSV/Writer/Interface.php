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
 * @package	   Source
 */

/**
 *
 *
 * @package Source
 */
interface Source_CSV_Writer_Interface
{
	/**
	 * puts the csvdata in a file
	 *
	 * @param string 	$szLocation 	The filelocation	 
	 * @param array		$arrCSVData 	The data to be written
	 * @param array		$arrCSVHeaders 	The csvheaders to be written
	 * @param string 	$szSeperator 	The seperator between the columns, default is ";"
	 * @param string 	$szCharacterSet The default charcterset is utf-8
	 * @return bool	the csvdata
	 */
	public static function putCSVData( $szLocation, $arrCSVData, $arrCSVHeaders = null, $szSeperator = null, $szCharacterSet = null );
	
}
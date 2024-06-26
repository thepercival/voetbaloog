<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Reader.php 4603 2021-05-17 13:48:14Z thepercival $
 *
 * @package	   Source
 */

/**
 * @package Source
 */
class Source_CSV_Reader implements Source_CSV_Reader_Interface
{
	public static $m_szDefaultSeparator = ";";
	public static $m_szDefaultCharacterSet = "UTF-8";

	public function __construct()
	{
	}

	/**
	 * @see Source_CSV_Reader_Interface::getCSVData()
	 */
	public static function getCSVData( $sLocation, $sSeparator = null )
	{
		$sData = static::getDataAsString( $sLocation );

		return static::getCSVDataFromString( $sData, $sSeparator );
	}

	/**
	 * @see Source_CSV_Reader_Interface::getCSVData()
	 */
	public static function getDataAsString( $sLocation )
	{
		if ( ! is_file ( $sLocation ) )
			throw new Exception( "404 file not found!", E_ERROR );

		$handle = fopen( $sLocation, "r");
		$nLength = filesize( $sLocation );
		if ( $nLength === 0 )
			return "";

		$sFileContents = fread( $handle, $nLength );
		fclose( $handle );

		return $sFileContents;
	}

	/**
	 * @see Source_CSV_Reader_Interface::getCSVData()
	*/
	public static function getCSVDataFromString( $sContent, $sSeparator = null )
	{
		$arrContents = array();
		if ( strlen( $sContent ) === 0 )
			return;

		// Let op \r\n is soms niet het einde van de regel
		$szExplode = "\r\n";
		if ( strstr( $sContent, "\r\n" ) === false )
			$szExplode = "\n";

		if ( $sSeparator === null )
			$sSeparator = self::$m_szDefaultSeparator;

		$arrTmpContents = explode( $szExplode, $sContent );

		foreach( $arrTmpContents as $arrTmpContent )
		{
			$arrContent = explode( $sSeparator, $arrTmpContent["value"] );
			if ( count( $arrContent ) === 1 and strlen( $arrContent[0] ) === 0 )
				continue;
			$arrContents[] = $arrContent;
		}

		return $arrContents;
	}
}
<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Writer.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 *
 * @package	   Source
 */

/**
 *
 *
 * @package Source
 */
class Source_CSV_Writer implements Source_CSV_Writer_Interface
{
  	public function __construct()
  	{
  	}

  	public static function putCSVData( $szLocation, $arrCSVData, $arrCSVHeaders = null, $szSeperator = null, $szCharacterSet = null )
	{
		if ( $szSeperator === null )
            $szSeperator = Source_CSV_Reader::$m_szDefaultSeparator;
			
		if ( file_exists( $szLocation ) === true )
		{
			if ( ! is_file ( $szLocation ) )
				throw new Exception( $szLocation." is not a file", E_ERROR );
			
			if ( unlink( $szLocation ) === false )
				throw new Exception( "Cannot remove file ".$szLocation, E_ERROR );		
		}
		$fp = fopen( $szLocation, "w");
		
		$szData = "";
		$nHeaderCount = null;
		if ( $arrCSVHeaders !== null )
		{
			$szDataLine = null;
			$nHeaderCount = count( $arrCSVHeaders );
			foreach( $arrCSVHeaders as $szCSVHeader )
			{
				if ( $szDataLine === null )
					$szDataLine = $szCSVHeader;
				else
					$szDataLine .= $szSeperator.$szCSVHeader;
			}			
			$szData .= $szDataLine.PHP_EOL;
		}
		
		foreach( $arrCSVData as $arrValue )
		{
			if ( $nHeaderCount !== null and count( $arrValue ) !== $nHeaderCount )
				throw new Exception("Het aantal waarden komt niet overeen met het aantal headers", E_ERROR);

			$szDataLine = null;
			foreach( $arrValue as $szValue )
			{
				if ( $szDataLine === null )
					$szDataLine = $szValue;
				else
					$szDataLine .= $szSeperator.$szValue;
			}
			$szData .= $szDataLine.PHP_EOL;
		}	 
		
		if ( $szCharacterSet !== null )
			$szData = mb_convert_encoding( $szData, $szCharacterSet, Source_CSV_Reader::$m_szDefaultCharacterSet );
			 
		fwrite( $fp, $szData);		
		fclose( $fp );

		return true;
	}
}
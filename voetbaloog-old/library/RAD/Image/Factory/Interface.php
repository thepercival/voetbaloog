<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Image
 */

/**
 * @package    Image
 */
interface RAD_Image_Factory_Interface
{
	/**
	 * saves the image
	 *
	 * @param 	string $szPathPrefix		The perfix
	 * @param 	string $objImageStream		The imagestream
	 * @param 	string $szPathPostfix		The postfix
	 * @return  null
	 */
	public static function saveImage( $szPathPrefix, $objImageStream, $szPathPostfix = ".jpg" );
	/**
	 * resizes the image and return the data
	 *
	 * @param 	string 	$vtData				The data
	 * @param 	int 	$p_nNewWidth		The new width
	 * @param 	int 	$p_nNewHeight		The new height
	 * @return  false|string
	 */
	public static function resize( $vtData, $p_nNewWidth = null, $p_nNewHeight = null );
	/**
	* resizes the image and return the data
	*
	* @param 	int 	$nNrOfColors		The number of colors
	* @param 	int 	$nType				The type of color, html or rgb
	* @return  array 	array of colors
	*/
	public static function getColors( $nNrOfColors, $nType = 1 /* static::$m_nRGB */ );
	/**
	* resizes the image and return the data
	*
	* @param 	string 	$vtData				The data
	* @param 	string 	$sDirection			Which side to cut off
	* @return  false|string
	*/
	public static function cutToSquare( $vtData, $sDirection );
}
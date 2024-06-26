<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Factory.php 4558 2019-08-13 08:54:29Z thepercival $
 * @since	  File available since Release 4.0
 * @package	Image
 */

/**
 * @package	Image
 */
class RAD_Image_Factory implements RAD_Image_Factory_Interface, Patterns_Singleton_Interface
{
	private static $m_objSingleton;
	public static $m_nRGB = 1;
	public static $m_nHTML = 2;

	/**
	 * A protected constructor; prevents direct creation of object
	 */
	protected function __construct(){}

	/**
	 * @see Patterns_Singleton_Interface::__clone()
	 */
	public function __clone()
	{
		trigger_error("Cloning is not allowed.", E_USER_ERROR);
	}

	/**
	 * @see Patterns_Singleton_Interface::getInstance()
	 */
	public static function getInstance()
	{
		if ( self::$m_objSingleton === null )
		{
			$MySelf = __CLASS__;
			self::$m_objSingleton = new $MySelf();
		}
		return self::$m_objSingleton;
	}

	/**
	 * @see RAD_Image_Factory_Interface::saveImage()
	 */
	public static function saveImage( $szPathPrefix, $objImageStream, $szPathPostfix = ".jpg" )
	{
		//$db_img = base64_decode($row["Picture"]);
		$objImage = imagecreatefromstring( $objImageStream );
		imagejpeg( $objImage, $szPathPrefix.$szPathPostfix );
	}

	public static function resize( $vtData, $p_nNewWidth = null, $p_nNewHeight = null )
	{
		$src = imagecreatefromstring( $vtData );
		if ( $src === false )
			return false;

		$nWidth = imagesx( $src );
		$nHeight = imagesy( $src );
		$nNewWidth = null;
		$nNewHeight = null;
		if ( $p_nNewWidth !== null )
		{
			$nAspectRatio = $nHeight / $nWidth;
			$nNewWidth = $p_nNewWidth;
			$nNewHeight = $p_nNewWidth * $nAspectRatio;
		}
		else if ( $p_nNewHeight !== null )
		{
			$nAspectRatio = $nWidth / $nHeight;
			$nNewHeight = $p_nNewHeight;
			$nNewWidth = $p_nNewHeight * $nAspectRatio;
		}
		else
		{
			return $vtData;
		}
		$img = imagecreatetruecolor( $nNewWidth, $nNewHeight );
		imagecopyresized( $img, $src, 0, 0, 0, 0, $nNewWidth, $nNewHeight, $nWidth, $nHeight);

		ob_start();
		imagejpeg( $img );
		return ob_get_clean();
	}
	
	public static function cutToSquare( $vtData, $sDirection )
	{
		$rImage = imagecreatefromstring ( $vtData );
	
		$nWidth = imagesx($rImage);
		$nHeight = imagesy($rImage);
	
		$nOffsetX = 0;
		$nOffsetY = 0;

        $nNewHeight = $nWidth;
        $nNewWidth = $nWidth;
		if ( $sDirection === "bottom" )
		{
			$nNewHeight = $nWidth;
			$nNewWidth = $nWidth;
		}
		
		$rNewImage = imagecreatetruecolor( $nNewWidth, $nNewHeight);
		imagecopy( $rNewImage, $rImage, 0, 0, $nOffsetX, $nOffsetY, $nWidth, $nHeight);
	
		ob_start();
		imagejpeg( $rNewImage );
		return ob_get_clean();
	}

	public static function getAspectRatio( $vtData )
	{
		$src = imagecreatefromstring( $vtData );
		return imagesy( $src ) / imagesx( $src );
	}

	public static function getColors( $nNrOfColors, $nType = 1 /* static::$m_nRGB */ )
	{
		$arrColors = array(
			array( 0, 0, 255 ),
			array( 0, 255, 0 ),
			array( 255, 0, 0 ),
			array( 0, 255, 255 ),
			array( 255, 0, 255 ),
			array( 255, 255, 0 ),
			array( 0, 0, 128 ),
			array( 0, 128, 0 ),
			array( 128, 0, 0 ),
			array( 0, 128, 128 ),
			array( 128, 0, 128 ),
			array( 128, 128, 0 ),
			array( 0, 0, 64 ),
			array( 0, 64, 0 ),
			array( 64, 0, 0 ),
			array( 0, 64, 64 ),
			array( 64, 0, 64 ),
			array( 64, 64, 0 ),
			array( 0, 0, 192 ),
			array( 0, 192, 0 ),
			array( 192, 0, 0 ),
			array( 0, 192, 192 ),
			array( 64, 0, 192 ),
			array( 192, 192, 0 )
		);

		while ( $nNrOfColors > count( $arrColors ) )
			$arrColors[] = array( 0, 0, 0 );

		if ( $nType === static::$m_nHTML )
		{
			for( $nI = 0 ; $nI < count( $arrColors ) ; $nI++ )
				$arrColors[ $nI ] = static::rgb2html( $arrColors[ $nI ] );
		}

		return $arrColors;
	}

	protected static function rgb2html( $arrRGB )
	{
		if ( !( is_array( $arrRGB ) and sizeof( $arrRGB ) === 3 ) )
			throw new Exception( "array is not excepted for rgb2html!", E_ERROR );

		list($r, $g, $b) = $arrRGB;

		$r = (int) $r;
		$g = (int) $g;
		$b = (int) $b;

		$r = dechex( $r < 0 ? 0 : ( $r > 255 ? 255 : $r ) );
		$g = dechex( $g < 0 ? 0 : ( $g > 255 ? 255 : $g ) );
		$b = dechex( $b < 0 ? 0 : ( $b > 255 ? 255 : $b ) );

		$sColor = ( strlen( $r ) < 2 ? '0' : '' ) . $r;
		$sColor .= ( strlen( $g ) < 2 ? '0' : '' ) . $g;
		$sColor .= ( strlen( $b ) < 2 ? '0' : '' ) .$b;

		return $sColor;
	}
}
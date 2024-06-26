<?php

/**
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Apps_Helper_GetImage extends Zend_Controller_Action_Helper_Abstract
{
	public function direct( $szLabel, $arrOptions = array() )
	{
		if ( $_FILES[ $szLabel ] ['error'] > 0 )
		{
			$sMessage = "onbekende fout";
			switch ( $_FILES[ $szLabel ] ['error'] )
			{
				case UPLOAD_ERR_INI_SIZE:
					$sMessage = "het bestand is groter dan de PHP-installatie toestaat.";
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$sMessage = "het bestand is groter dan de formulie toestaat.";
					break;
				case UPLOAD_ERR_PARTIAL:
					$sMessage = "een gedeelte van het bestand is geupload.";
					break;
				case UPLOAD_ERR_NO_FILE:
					return false;
			}
			return array( false, $sMessage );
		}
		if ( $_SERVER['REQUEST_METHOD'] !== "POST" )
			return array( false, "er is geen post-aanvraag gedaan" );

		$sImageFile = $_FILES[ $szLabel ] ['tmp_name'];
		$sImageFileName = $_FILES[ $szLabel ] ['name'];
		$arrImageSize = GetImageSize( $sImageFile );
		$nImageWidth = $arrImageSize[0];
		$nImageHeight = $arrImageSize[1];

		$sExtension = $this->getFileExtension( $sImageFileName );

		if ( $sExtension !== "jpg" and $sExtension !== "jpeg" and $sExtension !== "gif" and $sExtension !== "png" )
		{
			$sMessage = $sExtension." is een onbekend formaat(jpeg,jpg,gif,png)<br>";
			$sMessage .= "u heeft een afbeelding geprobeerd te uploaden met de extensie: ".$sExtension;
			return array( false, $sMessage );
		}

		$sMessage = null;
		if ( array_key_exists( "min_aspect_ratio", $arrOptions ) and ( $nImageWidth / $nImageHeight ) < $arrOptions["min_aspect_ratio"] )
		{
			$sMessage = "minimale breedte/hoogte-verhouding is ".$arrOptions["min_aspect_ratio"]." (portret-afbeelding)";
		}
		else if ( array_key_exists( "max_aspect_ratio", $arrOptions ) and ( $nImageWidth / $nImageHeight ) > $arrOptions["max_aspect_ratio"] )
		{
			$sMessage = "maximale breedte/hoogte-verhouding is ".$arrOptions["max_aspect_ratio"]." (portret-afbeelding)";
		}
		else if ( array_key_exists( "min_image_width", $arrOptions ) and $nImageWidth < $arrOptions["min_image_width"] )
		{
			$sMessage = "minimale breedte moet ".$arrOptions["min_image_width"]." pixels zijn";
		}
		else if ( array_key_exists( "min_image_height", $arrOptions ) and $nImageHeight < $arrOptions["min_image_height"] )
		{
			$sMessage = "minimale hoogte moet ".$arrOptions["min_image_height"]." pixels zijn";
		}
		if ( $sMessage !== null )
			return array( false, $sMessage );

		$vtStream = null;
		try
		{
			$vtResource = fopen( $sImageFile,"r");
			$vtStream = fread( $vtResource, filesize( $sImageFile ) );
			fclose( $vtResource );
			unlink( $sImageFile );

			if ( array_key_exists( "max_image_width", $arrOptions ) and $nImageWidth > $arrOptions["max_image_width"] )
			{
				$vtStream = RAD_Image_Factory::resize( $vtStream, $arrOptions["max_image_width"] );
			}

			if ( array_key_exists( "max_image_height", $arrOptions ) and $nImageHeight > $arrOptions["max_image_height"] )
			{
				$vtStream = RAD_Image_Factory::resize( $vtStream, null, $arrOptions["max_image_height"] );
			}
			return array( true, $vtStream );
		}
		catch( Exception $e )
		{
			return array( false, "er is iets misgegaan : " . $e->getMessage() );
		}
		return array( false, "er is een onbekende fout opgetreden" );
	}

	private function getFileExtension( $sFileName )
	{
		$nDotPosition = strrpos( $sFileName, "." );
		if ( $nDotPosition === false )
		{
			$nDotPosition = strrpos( $sFileName, " " );
			if ( $nDotPosition === false )
				return null;
		}

		$nExtensionLength = strlen( $sFileName ) - $nDotPosition;
		return strtolower( substr( $sFileName, $nDotPosition + 1, $nExtensionLength ) );
	}
}
?>



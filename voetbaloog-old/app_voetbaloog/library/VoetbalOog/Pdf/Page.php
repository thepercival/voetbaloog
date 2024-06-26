<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Page.php 997 2015-05-05 10:40:04Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pdf_Page extends ZendExt_Pdf_Page
{

	public function __construct( $param1, $param2 = null, $param3 = null )
	{
		parent::__construct( $param1, $param2, $param3 );
	}

	public function getPageMargin(){ return 20; }
	public function getHeaderHeight(){ return 50; }

	/**
	 * @param Voetbal_CompetitionSeason $oCompetitionSeason
	 */
	protected function drawHeader( $oCompetitionSeason, $oObject )
	{
		// begin : set predfined variables //////////////////
		$nLineWidth = 2;
		$this->setLineWidth( $nLineWidth );
		$oFont = $this->getFont();
		$nFontSize = $this->getFontSize();

		// picture has 600x100 width
		$nTextWidth = 300;
		$this->setFont( $oFont, 18 );
		$nNrOfTextLines = 2;
		if ( $oObject instanceof VoetbalOog_Pool_User ) {
			$this->setFont( $oFont, 14 );
			$nNrOfTextLines = 3;
			$nTextWidth = $nTextWidth * 14 / 18;
		}
		$nLogoWidth = $this->getHeaderHeight();
		$nTextMargin = 20;

		$nDefImageWidth = 250; $nDefImageHeight = 100;
		// end : set predfined variables //////////////////

		$nPageMargin = $this->getPageMargin();

		$nLeftX = $nPageMargin;
		$nRightX = $this->getWidth() - $nPageMargin;
		$nTopY = $this->getHeight() - $nPageMargin;
		$nBottomY = (int) ( $nTopY - $this->getHeaderHeight() );

		$nWidth = $nRightX - $nLeftX;
		$nHeight = $nTopY - $nBottomY;

		// draw cell
		$vtLineColor = "#135113";
		$this->drawCell( null, $nLeftX, $nTopY, $nWidth, $nHeight, ZendExt_Pdf_Page::ALIGNLEFT, $vtLineColor );

		$nWidth -= ( 2 * $nLineWidth );
		$nHeight -= ( 2 * $nLineWidth );
		$nLeftX += $nLineWidth;
		$nRightX -= $nLineWidth;
		$nTopY -= $nLineWidth;
		$nBottomY += $nLineWidth;

		$nVOLogoWidth = $nWidth - ( $nTextWidth + ( 2 * $nTextMargin ) + $nLogoWidth );
		if ( $nVOLogoWidth > $nDefImageWidth )
			$nVOLogoWidth = $nDefImageWidth;
		$nResizeRate = ceil( $nVOLogoWidth / $nDefImageWidth * 100 );
		$nImageHeight = ( $nDefImageHeight / 100 ) * $nResizeRate;
		if ( $nImageHeight > $nHeight )
			$nImageHeight = $nHeight;

		// draw left image
		$nAlignToMiddle = ( ( $nTopY - $nBottomY ) - $nImageHeight ) / 2;
		$oLeftImage = Zend_Pdf_Image::imageWithPath( WEBSITE_LOCAL_PATH . "public/images/pdf/header.jpg" );
		$this->drawImage( $oLeftImage, $nLeftX, $nBottomY + $nAlignToMiddle, $nLeftX + $nVOLogoWidth, $nBottomY + $nImageHeight + $nAlignToMiddle );

		$nTextLeft = $nLeftX + $nVOLogoWidth + $nTextMargin;
		$nTextHeight = (int) floor( ( $nTopY - $nBottomY ) / $nNrOfTextLines );

		// draw text compeittionseason
		$this->drawString( $oCompetitionSeason->getName(), $nTextLeft, ( $nTopY - $nTextHeight ) + ( $nTextHeight * 0.3 ), $nTextWidth );

		$oPool = null;
		$oPoolUser = null;
		if ( $oObject instanceof VoetbalOog_Pool )
		{
			$oPool = $oObject;
		}
		else if ( $oObject instanceof VoetbalOog_Pool_User )
		{
			$oPoolUser = $oObject;
			$oPool = $oPoolUser->getPool();
		}

		$this->drawString( "pool : " . $oPool->getName(), $nTextLeft, $nTopY - ( $nTextHeight * 2 ) + ( $nTextHeight * 0.3 ), $nTextWidth );
		$nTopY -= $nTextHeight;

		if ( $oPoolUser !== null )
		{
			$this->drawString( "naam : " . $oPoolUser->getUser()->getName(), $nTextLeft, $nBottomY + ( $nTextHeight * 0.3 ), $nTextWidth );
			$nTopY -= $nTextHeight;
		}

		if ( $oCompetitionSeason !== null )
		{
			// draw right image
			$nImageHeight = ( $nDefImageHeight / 100 ) * $nResizeRate;
			if ( $nImageHeight > $nHeight )
				$nImageHeight = $nHeight;

			$nLeftX = $nRightX - $nLogoWidth;

			$oImage = $this->getImage ( $oCompetitionSeason );

			if ( $oImage !== null )
				$this->drawImage( $oImage, $nLeftX, $nBottomY, $nRightX, $nBottomY + $nImageHeight );
		}

		// put back original
		$this->setFont( $oFont, $nFontSize );
		$this->setLineWidth( 1 );

		return $nBottomY - $nLineWidth;
	}

	protected function getImage ( $oObject )
	{
		if ( $oObject === null )
			return null;

		$sImagePath = null;
		{
			if ( $oObject instanceof Voetbal_Team )
			{
				$sImagePath = WEBSITE_LOCAL_PATH . "public/images/pdf/teams/".$oObject->getImageName()."_16.png";
			}
			else if ( $oObject instanceof Voetbal_CompetitionSeason )
			{
				$sImagePath = WEBSITE_LOCAL_PATH . "public/images/competitionseasons/".$oObject->getImageName()."_pdf.jpg";
			}
		}
		if ( $sImagePath === null or file_exists( $sImagePath ) === false )
			return null;

		if ( array_key_exists( $sImagePath, $this->m_arrImages ) )
			return $this->m_arrImages[ $sImagePath ];

		$this->m_arrImages[ $sImagePath ] = Zend_Pdf_Image::imageWithPath( $sImagePath );

		return $this->m_arrImages[ $sImagePath ];
	}

	protected function px2mm( $nMM )
	{
		return $nMM / 3.7795275593333;
	}
}
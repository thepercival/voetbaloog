<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: CompetitionSeason.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pdf_Page_CompetitionSeason extends VoetbalOog_Pdf_Page
{
	public function __construct( $param1, $param2 = null, $param3 = null )
	{
		parent::__construct( $param1, $param2, $param3 );
	}

	public function draw()
	{
		// $this->drawBackground();

		$nYPos = $this->drawCustomHeader();

		$nPageMargin = $this->getPageMargin();

		$nXPos = $this->drawGames( $nYPos - $nPageMargin, $nPageMargin );

		$nYPos = $this->drawPouleStandings( $nYPos - $nPageMargin, $nXPos + $nPageMargin );

		$this->drawQualificationOverview( $nYPos - $nPageMargin, $nXPos + $nPageMargin );
	}

	protected function drawBackground()
	{
		/*
		$this->setAlpha( 0.3 );
		$vtFillColor = new Zend_Pdf_Color_Html( "#135113" );
		$this->setFillColor( $vtFillColor );
		$this->drawRectangle( 0, $this->getHeight(), $this->getWidth(), 0 );
		// WEBSITE_LOCAL_PATH . "public/images/competitionseasons/logo_".$sImagePart.".jpg"
		$oImage = Zend_Pdf_Image::imageWithPath( VoetbalOog_Pdf_Factory::getImagePath() . "voetbal.png" );
		$this->drawImage( $oImage, 0, $this->getHeight(), $this->getWidth(), 0 );
		$this->setAlpha( 1 );
		*/
	}

	protected function drawCustomHeader()
	{
		$this->setLineWidth( 2 );
		$oFont = $this->getFont();
		$nFontSize = $this->getFontSize();

		$this->setFont( $oFont, 20 );

		// set variables
		$nPageMargin = $this->getPageMargin();

		$nLeftX = $nPageMargin;
		$nRightX = $this->getWidth() - $nPageMargin;
		$nTopY = $this->getHeight() - $nPageMargin;
		$nBottomY = $nTopY - $this->getHeaderHeight();

		$oCompetitionSeason = $this->m_oParent->getCompetitionSeason();

		$nImageWidth = 600 /100 * 80;
		$nImageHeight = 100 / 100 * 80;
		$nImageHeightDelta = ( 100 / 100 * 20 ) / 2;

		// draw cell
		$vtLineColor = "#135113";

		$this->drawCell( null, $nLeftX, $nTopY, $nRightX - $nLeftX, $nTopY - $nBottomY, $vtLineColor );

		// draw text
		$nLeftMargin = 50;
		$nTextLeft = $nLeftX + $nImageWidth + $nLeftMargin;
		$nFontSize = 20;
		$this->drawString( $oCompetitionSeason->getName(), $nTextLeft, $nBottomY + ( $this->getHeaderHeight() / 2 ) );

		// draw left image
		$oLeftImage = Zend_Pdf_Image::imageWithPath( WEBSITE_LOCAL_PATH . "public/images/pdf/header.jpg" );
		$this->drawImage( $oLeftImage, $nLeftX + 1, $nBottomY + $nImageHeightDelta, $nLeftX + $nImageWidth + 1, $nBottomY + $nImageHeightDelta + $nImageHeight );

		// draw right image
		$nImageWidth = 100 /100 * 98;
		$nImageHeight = 100 / 100 * 98;
		$nImageHeightDelta = ( 100 / 100 * 2 ) / 2;
		$nLeftX = $nRightX - ( 100 * 1.5 );
		$oRightImage = Zend_Pdf_Image::imageWithPath( WEBSITE_LOCAL_PATH . "public/images/competitionseasons/".$oCompetitionSeason->getImageName()."_pdf.jpg" );
		$this->drawImage( $oRightImage, $nLeftX + 1, $nBottomY + $nImageHeightDelta, $nLeftX + $nImageWidth + 1, $nBottomY + $nImageHeightDelta + $nImageHeight );

		// put back original
		$this->setFont( $oFont, $nFontSize );
		$this->setLineWidth( 1 );

		return $nBottomY;
	}

	protected function drawGames( $nYPos, $nXPos )
	{
		$nWidth = 300;
		$nPageMargin = $this->getPageMargin();
		$nHeight = $nYPos - $nPageMargin;

		$this->setLineWidth( 2 );

		$oFont = $this->getFont();
		$nFontSize = $this->getFontSize();

		// $this->setFont( $oFont, 20 );

		// set variables
		$nLeftX = $nXPos;
		$nRightX = $nLeftX + $nWidth;
		$nTopY = $nYPos;
		$nBottomY = $nYPos - $nHeight;

		// $oCompetitionSeason = $this->m_oParent->getCompetitionSeason();

		$vtLineColor = new Zend_Pdf_Color_Html( "#135113" );

        $sText = "";
		$this->drawCell( $sText, $nLeftX, $nTopY, $nRightX - $nLeftX, $nTopY - $nBottomY, $vtLineColor );

		$nHeaderHeight = 22;
		$this->setFillColor( $vtLineColor );
		$this->drawString( $sText, $nLeftX, $nTopY, $nRightX - $nLeftX, $nHeaderHeight, ZendExt_Pdf_Page::ALIGNCENTER );

		$this->setFillColor( new Zend_Pdf_Color_Html( "white" ) );
		$this->drawString( "Wedstrijden", $nLeftX, $nTopY + 3 + $nHeaderHeight, $nRightX - $nLeftX, ZendExt_Pdf_Page::ALIGNCENTER );

		$this->setFont( $oFont, $nFontSize );
		$this->setLineWidth( 1 );

		return $nXPos + $nWidth;
	}

	protected function drawPouleStandings( $nYPos, $nXPos )
	{
		$nPageMargin = $this->getPageMargin();
		$nWidth = $this->getWidth() - ( $nPageMargin + $nXPos );

		// set variables
		$nLeftX = $nXPos;
		$nTopY = $nYPos;

		$oPouleStandings = new VoetbalOog_Pdf_Page_Part_PouleStandings( $nLeftX, $nTopY );
		$oPouleStandings->putPage( $this );
		$oCompetitionSeason = $this->m_oParent->getCompetitionSeason();
		$oPouleStandings->putRound( $oCompetitionSeason->getRounds()->first() );
		$oPouleStandings->putWidth( $nWidth );

		return $oPouleStandings->draw();
	}

	protected function drawQualificationOverview( $nYPos, $nXPos )
	{
		$nPageMargin = $this->getPageMargin();
		$nWidth = $this->getWidth() - ( $nPageMargin + $nXPos );
		$nHeight = $nYPos - $nPageMargin;

		$this->setLineWidth( 2 );

		$oFont = $this->getFont();
		$nFontSize = $this->getFontSize();

		// $this->setFont( $oFont, 20 );

		// set variables
		$nLeftX = $nXPos;
		$nRightX = $nLeftX + $nWidth;
		$nTopY = $nYPos;
		$nBottomY = $nYPos - $nHeight;

		// $oCompetitionSeason = $this->m_oParent->getCompetitionSeason();

		$vtLineColor = new Zend_Pdf_Color_Html( "#135113" );

		$this->drawCell( null, $nLeftX, $nTopY, $nRightX - $nLeftX, $nTopY - $nBottomY, ZendExt_Pdf_Page::ALIGNCENTER, $vtLineColor );

		$nHeaderHeight = 22;
		$this->setFillColor( $vtLineColor );
		$this->drawCell( null, $nLeftX, $nTopY, $nRightX - $nLeftX, $nHeaderHeight, ZendExt_Pdf_Page::ALIGNCENTER, $vtLineColor );

		$this->setFillColor( new Zend_Pdf_Color_Html( "white" ) );
		$this->drawString( "Kwalificatie Overzicht", $nLeftX, $nTopY + 3 + $nHeaderHeight, $nRightX - $nLeftX );

		$this->setFont( $oFont, $nFontSize );
		$this->setLineWidth( 1 );

		return $nBottomY;
	}

	public function getHeaderHeight()
	{
		return 100;
	}
}
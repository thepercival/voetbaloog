<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: PouleStandings.php 580 2013-11-20 15:28:51Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pdf_Page_Part_PouleStandings
{
	protected $m_oPage;
	protected $m_oRound;
	protected $m_nLeftX;
	protected $m_nTopY;
	protected $m_nWidth;
	protected $m_nHeight;

	public function __construct( $nLeftX, $nTopY )
	{
		$this->m_nLeftX = $nLeftX;
		$this->m_nTopY = $nTopY;
	}

	public function getPage()
	{
		return $this->m_oPage;
	}

	public function putPage( $oPage )
	{
		$this->m_oPage = $oPage;
	}

	public function getRound()
	{
		return $this->m_oRound;
	}

	public function putRound( $oRound )
	{
		$this->m_oRound = $oRound;
	}

	public function getLeftX()
	{
		return $this->m_nLeftX;
	}

	public function getTopY()
	{
		return $this->m_nTopY;
	}

	public function getWidth()
	{
		return $this->m_nWidth;
	}

	public function putWidth( $nWidth )
	{
		$this->m_nWidth = $nWidth;
	}

	/*
	 * bepaal de height door te kijken naar de poules ( aantal en aantal ploegen per poule ) en de width
	 */
	public function getHeight()
	{
		if ( $this->m_nHeight === null )
		{
			$oPoules = $this->getRound()->getPoules();
			$oPoulePlaces = $oPoules->first()->getPlaces();

			$nWidth = $this->getWidth();

			$this->getPouleStandingWidth();



			// poulewidth is default so
			// get number of horizontal poules
			// get how many lines of poules
			// pouleheight is header + nr of pouleplaces time lineheight

			// $nHeight = 300;
			// return $nTopY - $nHeight;
			// $oPouleStandings->putHeight( $nHeight );
		}
		return $this->m_nHeight;
	}

	/**
	 *
	 * Tel kolommen bij elkaar op plus 1 tussen elke kolom
	 */
	protected function getPouleStandingWidth()
	{
		// tel
	}

	public function draw()
	{
		$nYPos = $this->drawHeader();

		return $this->drawPouleStandings( $nYPos );
	}

	protected function drawHeader()
	{
		$oPage = $this->getPage();

		$oPage->setLineWidth( 2 );

		$oFont = $oPage->getFont();
		$nFontSize = $oPage->getFontSize();

		$vtLineColor = new Zend_Pdf_Color_Html( "#135113" );
		
		$nLeftX = $this->getLeftX();
		$nTopY = $this->getTopY();
		$nWidth = $this->getWidth();
		$nHeight = $this->getHeight();
		$nHeaderHeight = $this->getHeaderHeight();

		// draw cell
		$oPage->setFillColor( $vtLineColor );
		$oPage->drawCell( null, $nLeftX, $nTopY, $nWidth, $nHeight, ZendExt_Pdf_Page::ALIGNLEFT, $vtLineColor );

		$oPage->drawCell( null, $nLeftX, $nTopY, $nWidth, $nHeaderHeight, ZendExt_Pdf_Page::ALIGNCENTER, $vtLineColor );

		$oPage->setFillColor( new Zend_Pdf_Color_Html( "white" ) );
		$oPage->drawString( "Groepen", $nLeftX, $nTopY + 3 + $nHeaderHeight, $nWidth, ZendExt_Pdf_Page::ALIGNCENTER );

		$oPage->setFont( $oFont, $nFontSize );
		$oPage->setLineWidth( 1 );

		return $nTopY - $nHeight;
	}

	protected function drawPouleStandings( $nYPos )
	{
 		// height - headerheight = poulestandingsheight
 		// width = width

		$oPoules = $this->getRound()->getPoules();

		// $oPoules->count();

	}

	protected function drawPouleStanding()
	{


	}

	protected function getHeaderHeight()
	{
		return 22;
	}
}
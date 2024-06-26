<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: PoolForm.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pdf_Document_PoolForm extends Zend_Pdf
{
	protected $m_oPoolUser;	// VoetbalOog_Pool_User
	protected $m_arrColumns;	// array
	protected $m_nPageMargin;	// int
	protected $m_nHeaderHeight;	// int

	public function __construct( $oPoolUser )
	{
		parent::__construct();
		$this->m_oPoolUser = $oPoolUser;
	}

	public function render( $newSegmentOnly = false, $outputStream = null )
	{
		$this->fillContent();
		return parent::render( $newSegmentOnly, $outputStream );
	}

	public function getPoolUser()
	{
		return $this->m_oPoolUser;
	}

	public function getPageMargin()
	{
		if ( $this->m_nPageMargin === null )
			$this->initPageVariables();
		return $this->m_nPageMargin;
	}

	public function getHeaderHeight()
	{
		if ( $this->m_nHeaderHeight === null )
			$this->initPageVariables();
		return $this->m_nHeaderHeight;
	}

	private function initPageVariables()
	{
		if ( $this->m_nHeaderHeight === null or $this->m_nPageMargin === null )
		{
			$oPdfPage = new VoetbalOog_Pdf_Page_PoolForm( Zend_Pdf_Page::SIZE_A4 );
			$this->m_nHeaderHeight = $oPdfPage->getHeaderHeight();
			$this->m_nPageMargin = $oPdfPage->getPageMargin();
		}
	}

	public function getColumns()
	{
		if ( $this->m_arrColumns === null )
		{
			$this->m_arrColumns = array();
			$this->m_arrColumns["Datum"] = 50;
			$this->m_arrColumns["Tijd"] = 35;
			$this->m_arrColumns["Plaats"] = 80;
			$this->m_arrColumns["P"] = 20;
			$this->m_arrColumns["Thuisploeg"] = 158;
			$this->m_arrColumns["Uitploeg"] = 158;
			$this->m_arrColumns["Uitsl"] = 34;
		}
		return $this->m_arrColumns;
	}

	public function getUserColumnWidth()
	{
		return 40;
	}

	public function getFontHeight()
	{
		return 12;
	}

	protected function fillContent()
	{
		$oPool = $this->m_oPoolUser->getPool();
		$oPoolUser = $this->m_oPoolUser;
		$oCompetitionSeason = $oPool->getCompetitionSeason();

		$nMargin = $this->getPageMargin();
		$nHeaderHeight = $this->getHeaderHeight();
		$nFontHeight = $this->getFontHeight();
		$nRowHeight = $nFontHeight + 1;

		$bStart = true;
		$nYPos = null;
		$oPdfPage = null;
		$oRounds = $oCompetitionSeason->getRounds();
		foreach ( $oRounds as $oRound )
		{
			$oGames = $oRound->getGames( true );

			if ( $nYPos === null or ( $nYPos - ( ( $oGames->count() + 2 ) * $nRowHeight  ) < $nMargin ) )
			{
				$oPdfPage = new VoetbalOog_Pdf_Page_PoolForm( Zend_Pdf_Page::SIZE_A4 );

				$oFont = VoetbalOog_Pdf_Factory::getFont();
				$oPdfPage->setFont( $oFont, $nFontHeight );
				$oPdfPage->putParent( $this );
				$this->pages[] = $oPdfPage;

				$nYPos = $oPdfPage->getHeight() - ( $nHeaderHeight + ( 2 * $nMargin ) );

				$bStart = true;
			}

			$nYPos = $oPdfPage->draw( $oRound, $nYPos, $oPoolUser, $bStart );
			$bStart = false;
		}

		$nYPos -= ( $nRowHeight * 2 );
		// $nYPos -= ( $nRowHeight * 11 ); // to test less space
		if ( $nYPos - $oPdfPage->getMinimalHeightAllTimes() >= $nMargin )
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "VoetbalOog_Pool::Name", "EqualTo", $oPool->getName() );
			$oOptions->addFilter( "VoetbalOog_Pool::Id", "NotEqualTo", $oPool );
			if ( VoetbalOog_Pool_Factory::getNrOfObjectsFromDatabase( $oOptions ) > 0 )
				$oPdfPage->drawAllTimes( $nYPos, $oPoolUser );
		}
		// die();
	}
}
<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: PoolTotal.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pdf_Document_PoolTotal extends Zend_Pdf
{
	protected $m_oPoolUser;						// VoetbalOog_Pool_User
	protected $m_oPool;							// VoetbalOog_Pool
	protected $m_arrColumns;					// array
	protected $m_nPageHeight;					// int
	protected $m_nPageWidth;					// int
	protected $m_nRankWidth; 					// int
	protected $m_nHeaderHeight;					// int
	protected $m_nPageMargin;					// int
	protected $m_nWidthLeftColumns;				// int
	protected $m_nUserBetColumnWidth;			// int
	protected $m_nUserBetColumnWidthMinimal;	// int
	protected $m_nUserPointColumnWidth;			// int
	protected $m_bShowImageForQualify;			// bool
	protected $m_oNow;                          // DateTime

	public function __construct( $oPool, $oPoolUser )
	{
		parent::__construct();
		$this->m_oPool = $oPool;
		$this->m_oPoolUser = $oPoolUser;
		$this->m_nUserBetColumnWidthMinimal = 40;
		$this->m_nUserBetColumnWidth = $this->m_nUserBetColumnWidthMinimal; // 74%
		$this->m_nUserPointColumnWidth = 14; // 26%
		$this->m_oNow = Agenda_Factory::createDateTime();
	}

	public function getNow(){ return $this->m_oNow; }

	public function getPageMargin()
	{
		if ( $this->m_nPageMargin === null )
			$this->putPageProperties();
		return $this->m_nPageMargin;
	}

	public function getHeaderHeight()
	{
		if ( $this->m_nHeaderHeight === null )
			$this->putPageProperties();
		return $this->m_nHeaderHeight;
	}

	private function putPageProperties()
	{
		if ( $this->m_nPageMargin === null and $this->m_nHeaderHeight === null ) {
			$oPage = new VoetbalOog_Pdf_Page( Zend_Pdf_Page::SIZE_A4 );
			$this->m_nPageMargin = $oPage->getPageMargin();
			$this->m_nHeaderHeight = $oPage->getHeaderHeight();
		}
	}

	public function getCustomWidth()
	{
		return $this->m_nPageWidth;
	}

	public function putCustomWidth( $nCustomWidth )
	{
		$this->m_nPageWidth = $nCustomWidth;
	}

	public function getUserColumnWidth()
	{
		return $this->getUserBetColumnWidth() + $this->getUserPointColumnWidth();
	}
	protected function putUserColumnWidth( $nUserColumnWidth )
	{
		$nOldUserColumnWidth = $this->getUserColumnWidth();
		$this->m_nUserBetColumnWidth = $nUserColumnWidth * $this->getUserBetColumnWidth() / $nOldUserColumnWidth;
		$this->m_nUserPointColumnWidth = $nUserColumnWidth * $this->getUserPointColumnWidth() / $nOldUserColumnWidth;
	}
	public function getUserColumnWidthMax()
	{
		return $this->getUserBetColumnWidthMax() + $this->getUserPointColumnWidthMax();
	}
	public function getUserBetColumnWidthMinimal(){ return $this->m_nUserBetColumnWidthMinimal; }
	public function getUserBetColumnWidth(){ return $this->m_nUserBetColumnWidth; }
	public function getUserBetColumnWidthMax(){ return 2 * $this->getUserBetColumnWidth(); }
	public function getUserPointColumnWidth(){ return $this->m_nUserPointColumnWidth; }
	public function getUserPointColumnWidthMax(){ return 2 * $this->getUserPointColumnWidth(); }
	public function canShowImageForQualify(){ return $this->m_bShowImageForQualify; }
	public function putShowImageForQualify( $bShowImageForQualify ){ $this->m_bShowImageForQualify = $bShowImageForQualify; }

	public function getColumns()
	{
		if ( $this->m_arrColumns === null )
			$this->putColumns();
		return $this->m_arrColumns;
	}

	public function getWidthLeftColumns()
	{
		if ( $this->m_nWidthLeftColumns === null )
			$this->putColumns();
		return $this->m_nWidthLeftColumns;
	}

	public function getWidthRightColumns( $nPageWidth )
	{
		return $nPageWidth - ( ( 2 * $this->getPageMargin() ) + $this->getWidthLeftColumns() );
	}

	private function putColumns()
	{
		$this->m_arrColumns = array();
		$this->m_arrColumns["datum"] = 65;
		$this->m_arrColumns["tijd"] = 27;
		$this->m_arrColumns["plaats"] = 60;
		$this->m_arrColumns["p"] = 15;
		$this->m_arrColumns["thuisploeg"] = 80;
		$this->m_arrColumns["uitploeg"] = 80;
		$this->m_arrColumns["uitslag"] = 32;

		$this->m_nWidthLeftColumns = 359; // 65 + 27 + 60 + 15 + 80 + 80 + 32;
	}

	public function getFontHeight()
	{
		return 9;
	}

	public function getRowHeight()
	{
		return $this->getFontHeight() + 2; // 11
	}

	public function getGameRowHeight()
	{
		return $this->getRowHeight() - 1; // 10
	}

	public function getNrOfPoulesPerLine()
	{
		return 4;
	}

	public function getRankWidth()
	{
		if ( $this->m_nRankWidth === null )
		{
			$nRankWidth = $this->getCustomWidth();
			$nRankWidth -= ( 2 * $this->getPageMargin() );
			$nRankWidth -= ( $this->getNrOfPoulesPerLine() - 1 ) * $this->getPageMargin();
			$nRankWidth = floor( $nRankWidth / $this->getNrOfPoulesPerLine() );
			$this->m_nRankWidth = (int) $nRankWidth;
		}
		return $this->m_nRankWidth;
	}

	public function getCustomHeight( $oPool )
	{
		if ( $this->m_nPageHeight === null )
		{
			$nMargin = $this->getPageMargin();
			$nRowHeight = $this->getRowHeight();
			$nGameRowHeight = $this->getGameRowHeight();
			$nHeaderHeight = $this->getHeaderHeight();

			$this->m_nPageHeight = $nMargin + $nHeaderHeight;
			// var_dump( $this->m_nPageHeight );
			$nNumberOfPoulesPerLine = $this->getNrOfPoulesPerLine();

			$oCompetitionSeason = $oPool->getCompetitionSeason();

			$nNrOfRoundBetConfigs = 0;
			$bSHowHeaders = true;

			$oRounds = $oCompetitionSeason->getRounds();
			foreach ( $oRounds as $oRound )
			{
				$oRoundGames = $oRound->getGames();
				$oRoundBetConfigs = $oPool->getBetConfigs( $oRound );

				$oRoundBetConfigResult = null;
				foreach( $oRoundBetConfigs as $oRoundBetConfig )
				{
					if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Result::$nId )
						$oRoundBetConfigResult = $oRoundBetConfig;
				}

				foreach( $oRoundBetConfigs as $oRoundBetConfig )
				{
					if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId
						and $oRoundBetConfigResult !== null
					)
						continue;

					$nNrOfRoundBetConfigs++;

					$this->m_nPageHeight += $nRowHeight; // empty line
					// var_dump( $this->m_nPageHeight );
					$this->m_nPageHeight += $nRowHeight; // points description
					// var_dump( $this->m_nPageHeight );

					if ( $bSHowHeaders === true )
					{
						$this->m_nPageHeight += $nRowHeight; // headers
						if ( $oRoundGames->count() <= 16 )
							$bSHowHeaders = false;
					}
					// var_dump( $this->m_nPageHeight );

					$this->m_nPageHeight += ( $oRoundGames->count() * $nGameRowHeight );
					// var_dump( $this->m_nPageHeight );
					$this->m_nPageHeight += $nRowHeight; // punten totaal
					// var_dump( $this->m_nPageHeight );
				}

				if ( $oRound->needsRanking() )
				{
					// var_dump( $this->m_nPageHeight );
					$oRoundPoules =	$oRound->getPoules();
					$nNrOfPoules = $oRoundPoules->count();
					$nNrOfPoulePlaces = $oRoundPoules->first()->getPlaces()->count();

					$nNrOfRankLines = (int) ceil( $nNrOfPoules / $this->getNrOfPoulesPerLine() );
					// var_dump( $this->m_nPageHeight );
					$this->m_nPageHeight += ( $nNrOfRankLines * $nRowHeight ); // empty lines
					$this->m_nPageHeight += ( $nNrOfRankLines * ( 2 * $nRowHeight ) ); // 2 headerrows
					$this->m_nPageHeight += ( $nNrOfRankLines * $nNrOfPoulePlaces * $nRowHeight ); // rows
					// var_dump( $this->m_nPageHeight );
				}
				// var_dump( $this->m_nPageHeight );
			}

			$this->m_nPageHeight += $nRowHeight; // empty row
			// var_dump( $this->m_nPageHeight );
			$this->m_nPageHeight += ( $nNrOfRoundBetConfigs * $nRowHeight );
			// var_dump( $this->m_nPageHeight );
			$this->m_nPageHeight += $nRowHeight; // points total
			// var_dump( $this->m_nPageHeight );
			$this->m_nPageHeight += $nRowHeight; // pooluser place
			// var_dump( $this->m_nPageHeight );

			$this->m_nPageHeight += $nMargin;
		}
		// var_dump( $this->m_nPageHeight ); die();
		return $this->m_nPageHeight;
	}

	protected function getNrOfAvailablePoolUsersPerPage( $nPageWidth )
	{
		$nPoolUserWidth = ( $nPageWidth - ( 2 * $this->getPageMargin() ) ) - $this->getWidthLeftColumns();
		return (int) floor( $nPoolUserWidth / $this->getUserColumnWidth() );
	}

	public function render( $newSegmentOnly = false, $outputStream = null )
	{
		$this->fillContent();
		return parent::render( $newSegmentOnly, $outputStream );
	}

	protected function createNewPage( $nPageWidth, $nPageHeight )
	{
		$oPage = new VoetbalOog_Pdf_Page_PoolTotal( $nPageWidth, $nPageHeight );
		$oFont = VoetbalOog_Pdf_Factory::getFont();
		$oPage->setFont( $oFont, $this->getFontHeight() );
		$oPage->putParent( $this );
		$this->pages[] = $oPage;
		return $oPage;
	}

	protected function fillContent()
	{
		$oPoolUsers = $this->m_oPool->getUsers();

		$nPageHeight = $this->getCustomHeight( $this->m_oPool );

		$nPageWidth = $nPageHeight * 210/297; // portret

		$nNrOfAvailablePoolUsersPerPage = $this->getNrOfAvailablePoolUsersPerPage( $nPageWidth );
		if ( $oPoolUsers->count() > $nNrOfAvailablePoolUsersPerPage )
			$nPageWidth = $nPageHeight * 297/210; // landscape

		$this->putCustomWidth( $nPageWidth );

		$nNrOfAvailablePoolUsersPerPage = $this->getNrOfAvailablePoolUsersPerPage( $nPageWidth );
		$nNrOfPages = (int) ceil( $oPoolUsers->count() / $nNrOfAvailablePoolUsersPerPage );

		$nNrOfPoolUsersPerPage = (int) ceil( $oPoolUsers->count() / $nNrOfPages );

		$nWidthRightColumns = $this->getWidthRightColumns( $nPageWidth );

		$nWidthPerUser = $nWidthRightColumns / $nNrOfPoolUsersPerPage;

		$nCurrentUserColumnWidth = $this->getUserColumnWidth();
		if ( $nWidthPerUser > $nCurrentUserColumnWidth )
			$nCurrentUserColumnWidth = $nWidthPerUser;
		if ( $nCurrentUserColumnWidth > $this->getUserColumnWidthMax() )
			$nCurrentUserColumnWidth = $this->getUserColumnWidthMax();
		$this->putUserColumnWidth( $nCurrentUserColumnWidth );

		$nRowHeight = $this->getRowHeight();
		$bShowImageForQualify = ( $this->getUserBetColumnWidth() >= ( $nRowHeight + $this->m_nUserBetColumnWidthMinimal ) );
		$this->putShowImageForQualify( $bShowImageForQualify );

		$nCounter = 0;
		$oDrawPoolUsers = VoetbalOog_Pool_User_Factory::createObjects();
		foreach( $oPoolUsers as $oPoolUser )
		{
			$oDrawPoolUsers->add( $oPoolUser );

			if ( ( ++$nCounter % $nNrOfPoolUsersPerPage ) === 0 )
			{
				$oPage = $this->createNewPage( $nPageWidth, $nPageHeight );
				$oPage->draw( $this->m_oPoolUser, $this->m_oPool, $oDrawPoolUsers );
				$oDrawPoolUsers->flush();
			}
		}
		if ( $oDrawPoolUsers->count() > 0 )
		{
			$oPage = $this->createNewPage( $nPageWidth, $nPageHeight );
			$oPage->draw( $this->m_oPoolUser, $this->m_oPool, $oDrawPoolUsers );
		}
	}
}
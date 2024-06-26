<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: PoolForm.php 1202 2020-05-02 09:37:15Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pdf_Page_PoolForm extends VoetbalOog_Pdf_Page
{
	protected $m_sBetBorder = "black";
	protected $m_sNonBetBorder = "#A8A8A8";
	protected $m_sOdd = "#F0F0F0";
	protected $m_sEven = "white";
	// alltimes
	protected $m_nNrOfAvailablePoolUserRows;

	public function __construct( $param1, $param2 = null, $param3 = null )
	{
		parent::__construct( $param1, $param2, $param3 );
	}

	public function getPageMargin()
	{
		return 30;
	}

	public function draw( $oRound, $nYPos, $oPoolUser, $bStart )
	{
		$this->setFillColor( new Zend_Pdf_Color_Html( "white" ) );

		$nMargin = $this->getPageMargin();
		$nFontHeight = $this->m_oParent->getFontHeight();
		$nRowHeight = $nFontHeight + 1;

		$nXStart = $nMargin;
		$nXPos = $nXStart;

		$nBaseWidth = 0;
		$arrColumns = $this->m_oParent->getColumns();
		foreach ( $arrColumns as $sHeader => $nWidth )
			$nBaseWidth += $nWidth;

		if ( $bStart === true )
		{
			$nYPos = $this->drawHeader( $oRound->getCompetitionSeason(), $oPoolUser );
			$nYPos -= $nRowHeight;
		}

		$sName = strip_tags( $oRound->getDisplayName() );
		$sName = html_entity_decode( $sName, ENT_NOQUOTES | ENT_HTML5, 'UTF-8' );
		$this->drawCell( $sName, $nXPos, $nYPos, $nBaseWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $this->m_sNonBetBorder );
		$nYPos -= $nRowHeight; $nXPos = $nXStart;

		$nAllBetTypes = 0; $sPointsDescription = "";
		{
			$oRoundBetConfigs = $oPoolUser->getPool()->getBetConfigs( $oRound );
			foreach ( $oRoundBetConfigs as $oRoundBetConfig )
			{
				$nBetType      = $oRoundBetConfig->getBetType();
				$nResultPoints = $oRoundBetConfig->getPoints();

				switch ( $nBetType ) {
					case VoetbalOog_Bet_Qualify::$nId:
						$sPointsDescription .= "correct gekwalificeerd team " . $nResultPoints . " punt(en).";
						break;
					case VoetbalOog_Bet_Result::$nId:
						$sPointsDescription .= "correct resultaat(gelijk) " . $nResultPoints . " punt(en).";
						break;
					case VoetbalOog_Bet_Score::$nId:
						$sPointsDescription .= "correcte score(1-2) " . $nResultPoints . " punt(en).";
						break;
				}
				$nAllBetTypes += $nBetType;
			}
		}
		$oFont = $this->getFont();
		$nFontSize = $this->getFontSize();
		$this->setFont( $oFont, $nFontSize -2 );

		$nXPos = $this->drawCell( $sPointsDescription, $nXPos, $nYPos, $nBaseWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $this->m_sNonBetBorder );
		$nYPos -= $nRowHeight; $nXPos = $nXStart;

		$this->setFont( $oFont, $nFontSize );

		$oGames = $oRound->getGames();
		if ( $oGames->count() > 0 )
		{
			$bHasScoreOrResultBetType = ( ( $nAllBetTypes & VoetbalOog_Bet_Result::$nId ) === VoetbalOog_Bet_Result::$nId
						or ( $nAllBetTypes & VoetbalOog_Bet_Score::$nId ) === VoetbalOog_Bet_Score::$nId );

			// Set Headers
			{
				foreach ( $arrColumns as $sHeader => $nWidth )
				{
					$sBorder = $this->m_sNonBetBorder;

					if ( !$bHasScoreOrResultBetType )
					{
						if ( $sHeader === "Uitsl" )
							continue;
						else if ( $sHeader === "Thuisploeg" or $sHeader === "Uitploeg" )
							$nWidth += $arrColumns["Uitsl"] / 2;
					}

					if ( $bHasScoreOrResultBetType and $sHeader === "Uitsl" )
						$sBorder = $this->m_sBetBorder;

					if ( $nAllBetTypes & VoetbalOog_Bet_Result::$nId === VoetbalOog_Bet_Result::$nId
						and ( $sHeader === "Thuisploeg" or $sHeader === "Uitploeg" )
					)
						$sBorder = $this->m_sBetBorder;

					$nXPos = $this->drawCell( $sHeader, $nXPos, $nYPos, $nWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $sBorder );
				}
				$nBaseWidth = $nXPos - $nXStart;

				// Set X and Y to beginning of new line
				$nYPos -= $nRowHeight; $nXPos = $nXStart;
			}

			$nCounter = 0;
			foreach ( $oGames as $oGame )
			{
				$sFillColor = ( ( ++$nCounter % 2 ) === 0 ) ? $this->m_sEven : $this->m_sOdd;

				$this->setFillColor( new Zend_Pdf_Color_Html( $sFillColor ) );

				$arrNonBetLineColors = array( "l" => $this->m_sNonBetBorder, "t" => $this->m_sNonBetBorder
						, "r" => $this->m_sNonBetBorder, "b" => $sFillColor
				);
				if ( $nCounter === $oGames->count() )
					$arrNonBetLineColors["b"] = $this->m_sNonBetBorder;
				$arrBetLineColors = array( "l" => $this->m_sBetBorder, "t" => $this->m_sNonBetBorder
						, "r" => $this->m_sBetBorder, "b" => $sFillColor
				);

				$nXPos = $this->drawCell( $oGame->getStartDateTime()->toString("d M"), $nXPos, $nYPos, $arrColumns["Datum"], $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrNonBetLineColors );

				$nXPos = $this->drawCell( $oGame->getStartDateTime()->toString("G:i"), $nXPos, $nYPos, $arrColumns["Tijd"], $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrNonBetLineColors );

				$sLocation = $oGame->getLocation() !== null ? $oGame->getLocation()->getName() : null;
				$nXPos = $this->drawCell( $sLocation, $nXPos, $nYPos, $arrColumns["Plaats"], $nRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $arrNonBetLineColors );

				$sName = $oGame->getHomePoulePlace()->getPoule()->getDisplayName( false );
				$nXPos = $this->drawCell( $sName, $nXPos, $nYPos, $arrColumns["P"], $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrNonBetLineColors );

				$nTeamWidth = $arrColumns["Thuisploeg"];
				if ( !$bHasScoreOrResultBetType )
					$nTeamWidth += $arrColumns["Uitsl"] / 2;

				$arrLineColors = $arrNonBetLineColors;
				if ( $nAllBetTypes & VoetbalOog_Bet_Result::$nId === VoetbalOog_Bet_Result::$nId )
				{
					$arrLineColors = $arrBetLineColors;
					if ( $nCounter === $oGames->count() )
						$arrLineColors["b"] = $this->m_sBetBorder;
				}
				else if ( $nCounter === $oGames->count() )
					$arrLineColors["b"] = $this->m_sNonBetBorder;

				$oHomePoulePlace = $oGame->getHomePoulePlace();
				$arrHomePoulePlace = $this->getPoulePlace( $oHomePoulePlace, $oPoolUser, $nAllBetTypes );

				$nXPosImg = $nXPos;
				if ( $arrHomePoulePlace["image"] !== null )
				{
					$this->setPadding( $nRowHeight );
				}
				$nXPos = $this->drawCell( $arrHomePoulePlace["teamname"], $nXPos, $nYPos, $nTeamWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $arrLineColors );
				if ( $arrHomePoulePlace["image"] !== null )
				{
					$this->setPadding( 1 );
					$this->drawImage( $arrHomePoulePlace["image"], $nXPosImg + 1, $nYPos - ( $nRowHeight - 1 ), $nXPosImg + ( $nRowHeight - 1 ), $nYPos - 1 );
				}
				$this->setFont( $oFont, $nFontSize );

				$oAwayPoulePlace = $oGame->getAwayPoulePlace();
				$arrAwayPoulePlace = $this->getPoulePlace( $oAwayPoulePlace, $oPoolUser, $nAllBetTypes );

				$nXPosImg = $nXPos;
				if ( $arrAwayPoulePlace["image"] !== null )
				{
					$this->setPadding( $nRowHeight );
				}
				$nXPos = $this->drawCell( $arrAwayPoulePlace["teamname"], $nXPos, $nYPos, $nTeamWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $arrLineColors );
				if ( $arrAwayPoulePlace["image"] !== null )
				{
					$this->setPadding( 1 );
					$this->drawImage( $arrAwayPoulePlace["image"], $nXPosImg + 1, $nYPos - ( $nRowHeight - 1 ), $nXPosImg + ( $nRowHeight - 1 ), $nYPos - 1 );
				}

				$this->setFont( $oFont, $nFontSize );

				$arrLineColors = $arrNonBetLineColors;
				if ( $bHasScoreOrResultBetType )
					$arrLineColors = $arrBetLineColors;
				if ( $nCounter === $oGames->count() )
					$arrLineColors["b"] = $this->m_sBetBorder;

				if ( $bHasScoreOrResultBetType )
				{
					$nXPos = $this->drawCell( $this->getResultScore( $oGame, $oPoolUser, $nAllBetTypes ), $nXPos, $nYPos, $arrColumns["Uitsl"], $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );
				}

				$nYPos -= $nRowHeight; $nXPos = $nXStart;
			}
		}
		else
		{
			$oPoules = $oRound->getPoules();
			$oPoule = $oPoules->first();
			$oPoulePlaces = $oPoule->getPlaces();
			$oPoulePlace = $oPoulePlaces->first();

			$arrPoulePlace = $this->getPoulePlace( $oPoulePlace, $oPoolUser, $nAllBetTypes );

			$nXPosImg = $nXPos;
			$nAlign = ZendExt_Pdf_Page::ALIGNLEFT;
			if ( $arrPoulePlace["image"] !== null )
			{
				$this->setPadding( $nRowHeight );
				$nAlign = ZendExt_Pdf_Page::ALIGNCENTER;
				$nXPosImg = ( $nBaseWidth / 2 ) - ( ( $this->getTextWidth( $arrPoulePlace["teamname"] ) / 2 ) - ( $nRowHeight - 1 ) );
			}
			$nXPos = $this->drawCell( $arrPoulePlace["teamname"], $nXPos, $nYPos, $nBaseWidth, $nRowHeight, $nAlign, $this->m_sBetBorder );
			if ( $arrPoulePlace["image"] !== null )
			{
				$this->setPadding( 1 );
				$this->drawImage( $arrPoulePlace["image"], $nXPosImg + 1, $nYPos - ( $nRowHeight - 1 ), $nXPosImg + ( $nRowHeight - 1 ), $nYPos - 1 );
			}
		}

		$nYPos -= $nRowHeight; $nXPos = $nXStart;

		return $nYPos;
	}

	protected function getPoulePlace( $oPoulePlace, $oPoolUser, $nAllBetTypes )
	{
		$arrTeam = array( "image" => null, "teamname" => null );

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_Pool_User::Id", "EqualTo", $oPoolUser );
		$oOptions->addFilter( "VoetbalOog_Bet_Qualify::PoulePlace", "EqualTo", $oPoulePlace );
		$oBet = VoetbalOog_Bet_Factory::createObjectFromDatabase( $oOptions );

		$sText = null;
		if ( $oBet === null )
		{
			if ( !( ( $nAllBetTypes & VoetbalOog_Bet_Qualify::$nId ) === VoetbalOog_Bet_Qualify::$nId ) )
				$arrTeam["image"] = $this->getImage ( $oPoulePlace->getTeam() );
			$arrTeam["teamname"] = $oPoulePlace->getDisplayName();
		}
		else
		{
			$arrTeam["image"] = $this->getImage ( $oBet->getTeam() );
			$arrTeam["teamname"] = $oBet->getTeam()->getName();
		}
		return $arrTeam;
	}

	protected function getResultScore( $oGame, $oPoolUser, $nAllBetTypes )
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_Pool_User::Id", "EqualTo", $oPoolUser );
		$oOptions->addFilter( "VoetbalOog_Bet_Result::Game", "EqualTo", $oGame->getId() );
		$oBets = VoetbalOog_Bet_Factory::createObjectsFromDatabase( $oOptions );

		$sText = null;
		if ( $oBets->count() === 0 )
		{
			if ( ( $nAllBetTypes & VoetbalOog_Bet_Score::$nId ) === VoetbalOog_Bet_Score::$nId )
				$sText = "-";
		}
		else
		{
			foreach ( $oBets as $oBet )
			{
				if ( $oBet instanceof VoetbalOog_Bet_Score_Interface )
					return $oBet->getHomeGoals()." - ".$oBet->getAwayGoals();
				else if ( $oBet instanceof VoetbalOog_Bet_Result_Interface )
				{
					if ( $oGame->getHomeGoals() === $oGame->getAwayGoals() )
						$sText = "Gelijk";
					else if ( $oGame->getHomeGoals() > $oGame->getAwayGoals() )
						$sText = "Thuis";
					else
						$sText = "Uit";
				}
			}
		}
		return $sText;
	}

	//////////////////////////////////////////////////// all times
	/*
	 * 1 headerregel
	 * 1 regel voor de columnheaders
	 * 3 regels minimaal voor de deelnemers
	 * 1 regel voor i + * + mannen vrouwen
	 * =
	 * 6 totaal
	 */
	public function getMinimalHeightAllTimes()
	{
		$nFontHeight = $this->m_oParent->getFontHeight();
		$nRowHeight = $nFontHeight + 1;
		return ( ( $this->getMinimumPoolUserRows() + $this->getNrOfExtraRows() ) * $nRowHeight );
	}

	/*
	 * 1 headerregel
	* 2 regels voor de columnheaders
	* 2 regels voor i + * + mannen vrouwen
	*/
	public function getNrOfExtraRows()
	{
		return $this->getNrOfExtraHeaderRows() + $this->getNrOfExtraFooterRows();
	}

	public function getNrOfExtraHeaderRows()
	{
		return 2;
	}

	public function getNrOfExtraFooterRows()
	{
		return 1;
	}

	public function getMinimumPoolUserRows()
	{
		return 3;
	}

	public function getColumnPlaceWidth()
	{
		return 20;
	}

	public function getColumnPoolUserNameWidth()
	{
		return 90;
	}

	public function getColumnTotalWidth()
	{
		return 25;
	}

	public function calculateColumnPoolUserNameWidth( $nBestNrOfPoolUserColumns )
	{
		$nAvailableWidth = $this->getWidth() - ( 2 * $this->getPageMargin() );
		$nAvailableWidth -= ( $this->getRestColumnsWidth() * $nBestNrOfPoolUserColumns );
		$nAvailableWidth /= $nBestNrOfPoolUserColumns;
		return floor( $nAvailableWidth );
	}

	protected function getRestColumnsWidth()
	{
		$oPool = $this->m_oParent->getPoolUser()->getPool();
		$nNrOfCompetitionSeasons = $this->getCompetitionSeasons( $this->getPools( $oPool ) )->count();
		$nRestColumnsWidth = $this->getColumnPlaceWidth() + ( $this->getColumnCompetitionSeasonWidth() * $nNrOfCompetitionSeasons );
		return $nRestColumnsWidth + $this->getColumnTotalWidth();
	}

	public function getColumnCompetitionSeasonWidth()
	{
		return 35;
	}

	public function getColumnPoolUserWidth()
	{
		$oPool = $this->m_oParent->getPoolUser()->getPool();
		$nNrOfCompetitionSeasons = $this->getCompetitionSeasons( $this->getPools( $oPool ) )->count();
		return $this->getColumnPlaceWidth() + $this->getColumnPoolUserNameWidth() + ( $nNrOfCompetitionSeasons * $this->getColumnCompetitionSeasonWidth() ) + $this->getColumnTotalWidth();
	}

	public function getMaximumNrOfPoolUserColumns()
	{
		$nNrOfPoolUserColumns = 0;
		{
			$nColumnPoolUserWidth = $this->getColumnPoolUserWidth();

			$nWidth = $this->getWidth() - ( 2 * $this->getPageMargin() );
			while( $nWidth >= $nColumnPoolUserWidth )
			{
				$nWidth -= $nColumnPoolUserWidth;
				$nNrOfPoolUserColumns++;
			}
		}
		return $nNrOfPoolUserColumns;
	}

	public function getNrOfAvailableRows()
	{
		return $this->m_nNrOfAvailablePoolUserRows;
	}

	public function putNrOfAvailablePoolUserRows( $nYPos )
	{
		if ( $this->m_nNrOfAvailablePoolUserRows === null )
		{
			$nFontHeight = $this->m_oParent->getFontHeight();
			$nRowHeight = $nFontHeight + 1;

			$nAvailableHeight = $nYPos - ( ( $this->getNrOfExtraRows() * $nRowHeight ) + $this->getPageMargin() );
			$this->m_nNrOfAvailablePoolUserRows = (int) floor( $nAvailableHeight/ $nRowHeight );
		}
		return $this->m_nNrOfAvailablePoolUserRows;
	}

	public function getBestNrOfPoolUserColumns( $nNrOfPoolUsers )
	{
		$nBestNrOfPoolUserColumns = 1;
		{
			$nMaximumNrOfPoolUserColumns = $this->getMaximumNrOfPoolUserColumns();
			$nNrOfAvailableRows = $this->getNrOfAvailableRows();

			while( $nBestNrOfPoolUserColumns < $nMaximumNrOfPoolUserColumns
				and ( $nNrOfPoolUsers > $nBestNrOfPoolUserColumns * $nNrOfAvailableRows )
			)
			{
				$nBestNrOfPoolUserColumns++;
			}
		}
		return $nBestNrOfPoolUserColumns;
	}

	public function drawAllTimes( $nYPos, $oPoolUser )
	{
		$this->putNrOfAvailablePoolUserRows( $nYPos );

		$oPool = $oPoolUser->getPool();

		$nWidth = $this->getWidth() - ( 2 * $this->getPageMargin() );

		$nFontHeight = $this->m_oParent->getFontHeight();
		$nRowHeight = $nFontHeight + 1;

		$nXPos = $this->getPageMargin();

		$this->drawCell( "stand aller tijden", $nXPos, $nYPos, $nWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $this->m_sNonBetBorder );
		$nYPos -= $nRowHeight;

		$nImageHeight = $nRowHeight -2;
		$oVictoryImage = Zend_Pdf_Image::imageWithPath( WEBSITE_LOCAL_PATH . "public/images/pdf/victory.png" );

		$arrAllTimeRankTotals = array();
		$arrAllTimeRankUsers = array();

		$this->getAllTimeArrays( $oPool, $arrAllTimeRankTotals, $arrAllTimeRankUsers );

		$nBestNrOfPoolUserColumns = $this->getBestNrOfPoolUserColumns( count( $arrAllTimeRankUsers ) );
		$nXPosStart = $this->getPageMargin();

		$oCompetitionSeasons = $this->getCompetitionSeasons( $this->getPools( $oPool ) );

		$nColumnPoolUserNameWidth = $this->calculateColumnPoolUserNameWidth( $nBestNrOfPoolUserColumns );
		$nColumnTotalWidth = $this->getColumnTotalWidth();
		$nColumnCompetitionSeasonWidth = $this->getColumnCompetitionSeasonWidth();

		$nNrOfAvailableRows = $this->getNrOfAvailableRows();
		$nNrOfUsers = count( $arrAllTimeRankTotals ); $nCurrentNr = 0;
		$nYPosStart = $nYPos;

		// start : content
		$bLastItemAlreadyDrawn = false;
		$nCounter = $nNrOfAvailableRows; $nCurrentNrOfColumns = 0;
		$nRanking = 0; $nPreviousPoints = null; $nRankingDelta = 0;
		foreach( $arrAllTimeRankTotals as $nUserId => $nTotalPoints )
		{
			if ( ++$nCounter >= $nNrOfAvailableRows )
			{
				$nCounter = 0;

				$nYPos = $nYPosStart;
				$nXPosStart = $nXPos;

				$this->setFillColor( new Zend_Pdf_Color_Html( "white" ) );

				// start : pooluser headers
				$nXPos = $this->drawCell( "nr", $nXPosStart, $nYPos, $this->getColumnPlaceWidth(), $nRowHeight * 2, ZendExt_Pdf_Page::ALIGNCENTER, $this->m_sNonBetBorder );

				$nXPos = $this->drawCell( "deelnemer", $nXPos, $nYPos, $nColumnPoolUserNameWidth, $nRowHeight * 2, ZendExt_Pdf_Page::ALIGNCENTER, $this->m_sNonBetBorder );

				foreach ( $oCompetitionSeasons as $oCompetitionSeason )
				{
					$arrTopLineColors = array( "l" => $this->m_sNonBetBorder, "t" => $this->m_sNonBetBorder
							, "r" => $this->m_sNonBetBorder, "b" => "white"
					);
					$sCompetitionName = strtolower( $oCompetitionSeason->getCompetition()->getAbbreviation() );
					$this->drawCell( $sCompetitionName, $nXPos, $nYPos, $nColumnCompetitionSeasonWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrTopLineColors );

					$arrBottomLineColors = array( "l" => $this->m_sNonBetBorder, "t" => "white"
							, "r" => $this->m_sNonBetBorder, "b" => $this->m_sNonBetBorder
					);
					$nXPos = $this->drawCell( $oCompetitionSeason->getSeason()->getName(), $nXPos, $nYPos - $nRowHeight, $nColumnCompetitionSeasonWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrBottomLineColors );
				}
				$nXPos = $this->drawCell( "tot", $nXPos, $nYPos, $nColumnTotalWidth, $nRowHeight * 2, ZendExt_Pdf_Page::ALIGNCENTER, $this->m_sNonBetBorder );
				// end : pooluser headers

				$nYPos -= $nRowHeight * 2;

				$nCurrentNrOfColumns++;
			}

			if ( $nPreviousPoints !== $nTotalPoints )
			{
				$nRanking++;
				$nRanking += $nRankingDelta;
				$nRankingDelta = 0;
			}
			else
				$nRankingDelta++;

			$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $nUserId );

			$sFillColor = ( ( ( $nCounter + 1 ) % 2 ) === 0 ) ? $this->m_sEven : $this->m_sOdd;
			$this->setFillColor( new Zend_Pdf_Color_Html( $sFillColor ) );

			$nCurrentNr++;

			if ( $nCurrentNrOfColumns === $nBestNrOfPoolUserColumns	)
			{
				if ( $nCounter + 2 === $nNrOfAvailableRows and $nCurrentNr + 1 < $nNrOfUsers )
				{
					$this->drawCell( "...", $nXPosStart, $nYPos, $nXPos - $nXPosStart, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $this->m_sNonBetBorder );
					$nYPos -= $nRowHeight;
					if ( $oPoolUser->getUser()->getId() === $nUserId )
					{
						$nXPos = $this->drawAllTimesRow( $nYPos, $nXPosStart, $arrAllTimeRankUsers[$nUserId], $nTotalPoints, $nRanking, $oCompetitionSeasons, $nColumnPoolUserNameWidth );

						$bLastItemAlreadyDrawn = true;
					}
					continue;
				}
				if ( $nCounter + 1 === $nNrOfAvailableRows and $nCurrentNr < $nNrOfUsers )
				{
					if ( $oPoolUser->getUser()->getId() === $nUserId )
					{
						$nXPos = $this->drawAllTimesRow( $nYPos, $nXPosStart, $arrAllTimeRankUsers[$nUserId], $nTotalPoints, $nRanking, $oCompetitionSeasons, $nColumnPoolUserNameWidth );

						$bLastItemAlreadyDrawn = true;
					}
					$nCounter--;
					continue;
				}
			}

			if ( $bLastItemAlreadyDrawn === true )
				break;

			$nXPos = $this->drawAllTimesRow( $nYPos, $nXPosStart, $arrAllTimeRankUsers[$nUserId], $nTotalPoints, $nRanking, $oCompetitionSeasons, $nColumnPoolUserNameWidth );

			$nPreviousPoints = $nTotalPoints;
			$nYPos -= $nRowHeight;
		}
		// end : content

		$this->setFillColor( new Zend_Pdf_Color_Html( "white" ) );
		$nXPosStart = $this->getPageMargin();
		$nYPos = $nYPosStart - ( $nNrOfAvailableRows * $nRowHeight + $this->getNrOfExtraHeaderRows() * $nRowHeight );
		$nYPosRoomLeft = $nYPosStart - ( $nNrOfUsers * $nRowHeight + $this->getNrOfExtraHeaderRows() * $nRowHeight );
		if ( $nYPos < $nYPosRoomLeft )
			$nYPos = $nYPosRoomLeft;
		// die();

		// start : footer
		$nWidth = $this->getWidth() - ( 2 * $this->getPageMargin() );
		$this->drawCell( "", $nXPosStart, $nYPos, $nWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $this->m_sNonBetBorder );
		$oInfoImage = Zend_Pdf_Image::imageWithPath( WEBSITE_LOCAL_PATH . "public/images/pdf/info.png" );
		$this->drawImage( $oInfoImage, $nXPosStart + 1, $nYPos - ( $nImageHeight + 1 ), $nXPosStart + ( $nImageHeight + 1 ), $nYPos - 1 );
		$sText = "1 punt per deelnemer die je onder je laat per deelname aan een pool";
		$this->drawString( $sText, $nXPosStart + $nImageHeight + 2, $nYPos - ( $nRowHeight - 2 ) );

		$nYPos -= $nRowHeight;
		// end : footer
	}

	public function drawAllTimesRow( $nYPos, $nXPos, $arrAllTimeRankUser, $nTotalPoints, $nRanking, $oCompetitionSeasons, $nColumnPoolUserNameWidth )
	{
		$nFontHeight = $this->m_oParent->getFontHeight();
		$nRowHeight = $nFontHeight + 1;

		$nColumnTotalWidth = $this->getColumnTotalWidth();
		$nColumnCompetitionSeasonWidth = $this->getColumnCompetitionSeasonWidth();

		$nImageHeight = $nRowHeight -2;
		$oVictoryImage = Zend_Pdf_Image::imageWithPath( WEBSITE_LOCAL_PATH . "public/images/pdf/victory.png" );

		$nXPos = $this->drawCell( $nRanking, $nXPos, $nYPos, $this->getColumnPlaceWidth(), $nRowHeight , ZendExt_Pdf_Page::ALIGNRIGHT, $this->m_sNonBetBorder );

		$arrNrOfWins = array();
		if ( array_key_exists( "mostrecentpooluserid", $arrAllTimeRankUser ) )
		{
			$nPoolUserId = $arrAllTimeRankUser["mostrecentpooluserid"];
			$oPoolUserTmp = VoetbalOog_Pool_User_Factory::createObjectFromDatabase( $nPoolUserId );
			$arrNrOfWins = $oPoolUserTmp->getNrOfWins( false );
		}

		$sUserName = $arrAllTimeRankUser["name"];
		$nLengtUserId = $this->getTextWidth( $sUserName );
		$nXPosVictory = $nXPos + $nLengtUserId;
		$nXPos = $this->drawCell( $sUserName, $nXPos, $nYPos, $nColumnPoolUserNameWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $this->m_sNonBetBorder );
		foreach ( $arrNrOfWins as $sWinCS )
		{
			$this->drawImage( $oVictoryImage, $nXPosVictory, $nYPos - ( $nImageHeight + 1 ), $nXPosVictory + $nImageHeight, $nYPos - 1 );
			$nXPosVictory += $nImageHeight;
		}

		foreach( $oCompetitionSeasons as $oCompetitionSeason )
		{
			$vtPoints = "na";
			if ( array_key_exists( $oCompetitionSeason->getId(), $arrAllTimeRankUser ) )
				$vtPoints = $arrAllTimeRankUser[ $oCompetitionSeason->getId() ];
			$nXPos = $this->drawCell( $vtPoints, $nXPos, $nYPos, $nColumnCompetitionSeasonWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $this->m_sNonBetBorder );
		}
		return $this->drawCell( $nTotalPoints, $nXPos, $nYPos, $nColumnTotalWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $this->m_sNonBetBorder );
	}

	protected function getCompetitionSeasons( $oPools )
	{
		$oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjects();
		foreach ( $oPools as $oPool )
		{
			$oCompetitionSeason = $oPool->getCompetitionSeason();
			if ( $oCompetitionSeason->getState() === Voetbal_Factory::STATE_PLAYED )
				$oCompetitionSeasons->add( $oCompetitionSeason );
		}
		return $oCompetitionSeasons;
	}

	protected function getPools( $oPool )
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_Season::StartDateTime", "SmallerThanOrEqualTo", $oPool->getCompetitionSeason()->getSeason()->getStartDateTime() );
		$oOptions->addFilter( "VoetbalOog_Pool::Name", "EqualTo", $oPool->getName() );
		$oOptions->addOrder( "Voetbal_Season::StartDateTime", false );
		return VoetbalOog_Pool_Factory::createObjectsFromDatabase( $oOptions );
	}


	protected function getAllTimeArrays( $oPool, &$arrAllTimeRankTotals, &$arrAllTimeRankUsers )
	{
		$oPools = $this->getPools( $oPool );

		foreach ( $oPools as $oPoolIt )
		{
			$oCompetitionSeason = $oPoolIt->getCompetitionSeason();
			if ( $oCompetitionSeason->getState() !== Voetbal_Factory::STATE_PLAYED )
				continue;

			$oRankedPoolUsers = $oPoolIt->getUsers();
			$oRankedPoolUsers->uasort(
				function( $oPoolUserA, $oPoolUserB )
				{
					return ( $oPoolUserA->getPoints() < $oPoolUserB->getPoints() ? -1 : 1 );
				}
			);

			$nRealPoints = -1;
			$nEqualPoints = 0;
			$nPreviousPoolPoints = -1;
			foreach ( $oRankedPoolUsers as $oRankedPoolUser )
			{
				$oUser = $oRankedPoolUser->getUser();

				if ( $oRankedPoolUser->getPoints() > $nPreviousPoolPoints )
				{
					$nRealPoints = $nRealPoints + 1 + $nEqualPoints;
					$nEqualPoints = 0;
				}
				else // dus gelijk
					$nEqualPoints++;

				if ( array_key_exists( $oUser->getId(), $arrAllTimeRankTotals ) === false )
				{
					$arrAllTimeRankTotals[$oUser->getId()] = 0;
					$arrAllTimeRankUsers[$oUser->getId()] = array();
				}
				$arrAllTimeRankUsers[$oUser->getId()]["mostrecentpooluserid"] = $oRankedPoolUser->getId();
				$arrAllTimeRankUsers[$oUser->getId()]["name"] = $oUser->getName();

				$arrAllTimeRankUsers[$oUser->getId()][$oCompetitionSeason->getId()] = $nRealPoints;
				$arrAllTimeRankTotals[$oUser->getId()] += $nRealPoints;

				$nPreviousPoolPoints = $oRankedPoolUser->getPoints();
			}
		}

		arsort( $arrAllTimeRankTotals );
	}
}
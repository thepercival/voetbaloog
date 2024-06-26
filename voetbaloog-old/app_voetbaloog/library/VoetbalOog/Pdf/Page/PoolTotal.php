<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: PoolTotal.php 1171 2016-06-23 13:34:29Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pdf_Page_PoolTotal extends VoetbalOog_Pdf_Page
{
	protected $m_sOuterBorder = "black";
	protected $m_sInnerBorder = "#A8A8A8";
	protected $m_sOdd = "#F0F0F0";
	protected $m_sEven = "white";
	protected $m_bHeadersFirstTime = true;

	public function __construct( $param1, $param2 = null, $param3 = null )
	{
		parent::__construct( $param1, $param2, $param3 );
	}

	public function draw( $p_oPoolUser, $p_oPool, $oPoolUsers )
	{
		$this->setFillColor( new Zend_Pdf_Color_Html( "white" ) );

		$oCompetitionSeason = $p_oPool->getCompetitionSeason();
		$oNow = $this->m_oParent->getNow();

		$nMargin = $this->getPageMargin();
		$nYPos = $this->drawHeader( $oCompetitionSeason, $p_oPool );

		$nBaseWidth = 0;
		$arrColumns = $this->m_oParent->getColumns();
		$nUserColumnWidth = $this->m_oParent->getUserColumnWidth();
		$nUserBetColumnWidth = $this->m_oParent->getUserBetColumnWidth();
		$nUserPointColumnWidth = $this->m_oParent->getUserPointColumnWidth();
		foreach ( $arrColumns as $sHeader => $nWidth )
			$nBaseWidth += $nWidth;

		// Start : Bereken height
		$nFontHeight = $this->m_oParent->getFontHeight();
		$nRowHeight = $this->m_oParent->getRowHeight();
		$nGameRowHeight =  $this->m_oParent->getGameRowHeight();
		$nXPos = $nMargin;
		// End : Initialiseer pagina

		$bSHowHeaders = true;

		$oRounds = $oCompetitionSeason->getRounds();
		foreach ( $oRounds as $oRound )
		{
			$oGames = $oRound->getGames( true );

			$oRoundBetConfigs = $p_oPool->getBetConfigs( $oRound );

			$oRoundBetConfigScore = null; $oRoundBetConfigResult = null;
			foreach( $oRoundBetConfigs as $oRoundBetConfig )
			{
				if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId )
					$oRoundBetConfigScore = $oRoundBetConfig;
				if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Result::$nId )
					$oRoundBetConfigResult = $oRoundBetConfig;
			}

			foreach( $oRoundBetConfigs as $oRoundBetConfig )
			{
				$sPointsDescription = null;

				if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId )
				{
					$sPointsDescription = "correcte score(1-2) ".$oRoundBetConfig->getPoints()." punten.";
					if ( $oRoundBetConfigResult !== null )
						continue;
				}
				else if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Result::$nId )
				{
					if ( $oRoundBetConfigScore !== null )
						$sPointsDescription = "correcte score(1-2) ".( $oRoundBetConfigScore->getPoints() + $oRoundBetConfig->getPoints() )." punten.";
					$sPointsDescription .= "correct resultaat(gelijk) ".$oRoundBetConfig->getPoints()." punt(en).";
				}
				else if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Qualify::$nId )
				{
					$sPointsDescription = "correct gekwalificeerd team ".$oRoundBetConfig->getPoints()." punt(en).";
				}

				$nYPos -= $nRowHeight; // empty line
				//var_dump( $nYPos );

				$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $this->m_sInnerBorder
					, "r" => $this->m_sInnerBorder
				);
				$this->setFillColor( new Zend_Pdf_Color_Html( "white" ) );
				$nXPos = $this->drawCell( $oRound->getName()."(".$sPointsDescription.")", $nXPos, $nYPos, $nBaseWidth - $arrColumns["uitslag"], $nRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $arrLineColors );

				if ( $oGames->count() > 0 )
				{
					$nYPos -= $nRowHeight; $nXPos = $nMargin;
					if ( $bSHowHeaders === true )
					{
						$nYPos = $this->setHeaders( $nYPos, $oPoolUsers );
						$nYPos++;
						if ( $oGames->count() <= 16 )
							$bSHowHeaders = false;
					}
					$nYPos--;
					//var_dump( $nYPos );

					$bFirst = true;
					$nEvenOdd = 0;
					foreach ( $oGames as $oGame )
					{
						$oDeadLine = $oRoundBetConfig->getDeadLine( $oGame );

						$sFillColor = ( ( ++$nEvenOdd % 2 ) === 0 ) ? $this->m_sEven : $this->m_sOdd;

						if ( $bFirst === true )
						{
							$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $this->m_sInnerBorder
								, "r" => $this->m_sInnerBorder, "b" => $sFillColor
							);
						}
						else
						{
							$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $sFillColor
								, "r" => $this->m_sInnerBorder, "b" => $sFillColor
							);
						}
						$this->setFillColor( new Zend_Pdf_Color_Html( $sFillColor ) );

						$nXPos = $this->drawCell( $oGame->getStartDateTime()->toString("D d M y"), $nXPos, $nYPos, $arrColumns["datum"], $nGameRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );

						if ( $bFirst === true )
						{
							$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $this->m_sInnerBorder
								, "r" => $this->m_sInnerBorder, "b" => $sFillColor
							);
						}
						else
						{
							$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $sFillColor
								, "r" => $this->m_sInnerBorder, "b" => $sFillColor
							);
						}

						$nXPos = $this->drawCell( $oGame->getStartDateTime()->toString("G:i"), $nXPos, $nYPos, $arrColumns["tijd"], $nGameRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );

						$sLocation = $oGame->getLocation() !== null ? $oGame->getLocation()->getName() : null;
						$nXPos = $this->drawCell( $sLocation, $nXPos, $nYPos, $arrColumns["plaats"], $nGameRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $arrLineColors );

						$oHomePoulePlace = $oGame->getHomePoulePlace();
						$oHomeTeam = $oHomePoulePlace->getTeam();
						$sHomeName = $oHomePoulePlace->getDisplayName();

                        $sPouleName = $oHomePoulePlace->getPoule()->getDisplayName( false );
                        $nXPos = $this->drawCell( $sPouleName, $nXPos, $nYPos, $arrColumns["p"], $nGameRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );

						if ( $oHomeTeam !== null )
						{
							$nXPosImg = $nXPos;
							$this->setPadding( $nGameRowHeight );
							$nXPos = $this->drawCell( $sHomeName, $nXPos, $nYPos, $arrColumns["thuisploeg"], $nGameRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $arrLineColors );
							$this->setPadding( 1 );

							$oImage = $this->getImage ( $oHomeTeam );
							$this->drawImage( $oImage, $nXPosImg + 1, $nYPos - ( $nGameRowHeight - 1 ), $nXPosImg + ( $nGameRowHeight - 1 ), $nYPos - 1 );
						}
						else
						{
							$nXPos = $this->drawCell( $sHomeName, $nXPos, $nYPos, $arrColumns["thuisploeg"], $nGameRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $arrLineColors );
						}

						$oAwayPoulePlace = $oGame->getAwayPoulePlace();
						$oAwayTeam = $oAwayPoulePlace->getTeam();
						$sAwayName = $oAwayPoulePlace->getDisplayName();

						if ( $oRound->isFirstRound() !== true and $oHomeTeam === null )
						{
							$sAwayName = $oAwayPoulePlace->getDisplayName();
						}

						if ( $oAwayTeam !== null )
						{
							$nXPosImg = $nXPos;
							$this->setPadding( $nGameRowHeight );
							$nXPos = $this->drawCell( $sAwayName, $nXPos, $nYPos, $arrColumns["thuisploeg"], $nGameRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $arrLineColors );
							$this->setPadding( 1 );

							$oImage = $this->getImage ( $oAwayTeam );
							$this->drawImage( $oImage, $nXPosImg + 1, $nYPos - ( $nGameRowHeight - 1 ), $nXPosImg + ( $nGameRowHeight - 1 ), $nYPos - 1 );
						}
						else
						{
							$nXPos = $this->drawCell( $sAwayName, $nXPos, $nYPos, $arrColumns["thuisploeg"], $nGameRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $arrLineColors );
						}

						$sResult = ( $oGame->getState() === Voetbal_Factory::STATE_PLAYED ) ? $oGame->getHomeGoals()." - ".$oGame->getAwayGoals() : "";
						$nXPos = $this->drawCell( $sResult, $nXPos, $nYPos, $arrColumns["uitslag"], $nGameRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );

						foreach ( $oPoolUsers as $oPoolUser )
						{
							$nPoints = 0;
							if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId
								or $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Result::$nId
							)
							{
								if ( $bFirst === true )
								{
									$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $this->m_sInnerBorder
										, "r" => $this->m_sInnerBorder, "b" => $sFillColor
									);
								}
								else
								{
									$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $sFillColor
										, "r" => $this->m_sInnerBorder, "b" => $sFillColor
									);
								}

								$oFilters = Construction_Factory::createOptions();
								$oFilters->addFilter( "VoetbalOog_Pool_User::Id", "EqualTo", $oPoolUser );
								$oFilters->addFilter( "VoetbalOog_Bet_Result::Game", "EqualTo", $oGame->getId() );
								$oBets = VoetbalOog_Bet_Factory::createObjectsFromDatabase( $oFilters );

								$sText = "";
								foreach ( $oBets as $oBet )
								{
									if ( $oBet instanceof VoetbalOog_Bet_Score_Interface ) {
										if ( $oNow < $oDeadLine )
											continue;
										$sText = $oBet->getHomeGoals() . " - " . $oBet->getAwayGoals();
									}
								}
								$nXPos = $this->drawCell( $sText, $nXPos, $nYPos, $nUserBetColumnWidth, $nGameRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );

								if ( $oGame->getState() === Voetbal_Factory::STATE_PLAYED )
								{
									foreach ( $oBets as $oBet )
										$nPoints += $oBet->getPoints();
								}
							}
							else
							{
								$oFilters = Construction_Factory::createOptions();
								$oFilters->addFilter( "VoetbalOog_Pool_User::Id", "EqualTo", $oPoolUser );
								$oFilters->addFilter( "VoetbalOog_Bet_Qualify::PoulePlace", "EqualTo", $oGame->getHomePoulePlace() );
								$oBet = VoetbalOog_Bet_Factory::createObjectFromDatabase( $oFilters );

								$sText = null;
								$oImage = null;
								if ( $oBet !== null )
								{
									$oHomeTeamBetted = $oBet->getTeam();
									if ( $oHomeTeamBetted !== null )
									{
										$nPoints += $oBet->getPoints();
										if ( $oNow > $oDeadLine )
											$sText = $oHomeTeamBetted->getAbbreviation();
									}
								}
								if ( $bFirst === true )
								{
									$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $this->m_sInnerBorder
										, "r" => $this->m_sInnerBorder, "b" => $sFillColor
									);
								}
								else
								{
									$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $sFillColor
										, "r" => $this->m_sInnerBorder, "b" => $sFillColor
									);
								}

								$nXPos = $this->drawCell( $sText, $nXPos, $nYPos, $nUserBetColumnWidth / 2, $nGameRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );

								$oFilters = Construction_Factory::createOptions();
								$oFilters->addFilter( "VoetbalOog_Pool_User::Id", "EqualTo", $oPoolUser );
								$oFilters->addFilter( "VoetbalOog_Bet_Qualify::PoulePlace", "EqualTo", $oGame->getAwayPoulePlace() );
								$oBet = VoetbalOog_Bet_Factory::createObjectFromDatabase( $oFilters );

								$sText = "";
								if ( $oBet !== null )
								{
									$oAwayTeamBetted = $oBet->getTeam();
									if ( $oAwayTeamBetted !== null )
									{
										$nPoints += $oBet->getPoints();
										if ( $oNow > $oDeadLine )
											$sText = $oAwayTeamBetted->getAbbreviation();
									}
								}

								if ( $bFirst === true )
								{
									$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $this->m_sInnerBorder
										, "r" => $this->m_sOuterBorder, "b" => $sFillColor
									);
								}
								else
								{
									$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $sFillColor
										, "r" => $this->m_sOuterBorder, "b" => $sFillColor
									);
								}

								$nXPos = $this->drawCell( $sText, $nXPos, $nYPos, $nUserBetColumnWidth / 2, $nGameRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );
							}

							if ( $bFirst === true )
							{
								$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $this->m_sInnerBorder
									, "r" => $this->m_sOuterBorder, "b" => $sFillColor
								);
							}
							else
							{
								$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $sFillColor
									, "r" => $this->m_sOuterBorder, "b" => $sFillColor
								);
							}

							$sTextPoints = "";
							if ( $oGame->getState() === Voetbal_Factory::STATE_PLAYED )
								$sTextPoints = $nPoints;
							$nXPos = $this->drawCell( $sTextPoints, $nXPos, $nYPos, $nUserPointColumnWidth, $nGameRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );
						}
						$nYPos -= $nGameRowHeight; $nXPos = $nMargin;
						$bFirst = false;
					}
				}
				else // if ( $oRound->isLastRound() )
				{
					//$nBaseWidth - $arrColumns["uitslag"]
					//$nXPos = $nBaseWidth;
					// Winnaar
					$oDeadLine = $oRoundBetConfig->getDeadLine();

					$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $this->m_sInnerBorder
						, "r" => $this->m_sOuterBorder, "b" => $this->m_sInnerBorder
					);

					$sAbbreviation = "";

					$oPoules = $oRound->getPoules();
					$oPoule = $oPoules->first();
					$oPoulePlaces = $oPoule->getPlaces();
					$oPoulePlace = $oPoulePlaces->first();
					$oTeam = $oPoulePlace->getTeam();

					$nXPosTmp = null;
					if ( $oTeam !== null )
						$sAbbreviation = $oTeam->getAbbreviation();
					$nXPosTmp = $this->drawCell( $sAbbreviation, $nXPos, $nYPos, $arrColumns["uitslag"], $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );

					if ( $oTeam !== null )
					{
						$oImage = $this->getImage ( $oTeam );
						$this->drawImage( $oImage, $nXPos + 1, $nYPos - ( $nRowHeight - 1 ), $nXPos + ( $nRowHeight - 1 ), $nYPos - 1 );
					}

					$nXPos = $nXPosTmp;

					foreach ( $oPoolUsers as $oPoolUser )
					{
						$nPoints = 0;

						$oFilters = Construction_Factory::createOptions();
						$oFilters->addFilter( "VoetbalOog_Pool_User::Id", "EqualTo", $oPoolUser );
						$oFilters->addFilter( "VoetbalOog_Bet_Qualify::PoulePlace", "EqualTo", $oPoulePlace );
						$oBet = VoetbalOog_Bet_Factory::createObjectFromDatabase( $oFilters );

						$sText = "";
						$oTeamBetted = null;
						if ( $oBet !== null )
						{
							$oTeamBetted = $oBet->getTeam();
							if ( $oTeamBetted !== null )
							{
								$sTeamBettedAbbreviation = $oTeamBetted->getAbbreviation();
								if ( $oTeam === $oTeamBetted )
									$nPoints += $oBet->getPoints();
								if ( $oNow > $oDeadLine )
									$sText = $sTeamBettedAbbreviation;
							}
						}

						$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $this->m_sInnerBorder
							, "r" => $this->m_sInnerBorder, "b" => $this->m_sInnerBorder
						);

						$nXPosImg = $nXPos;
						$nAlign = ZendExt_Pdf_Page::ALIGNCENTER;
						if ( $oTeamBetted !== null )
							$nAlign = ZendExt_Pdf_Page::ALIGNRIGHT;
						$nXPos = $this->drawCell( $sText, $nXPos, $nYPos, $nUserBetColumnWidth, $nRowHeight, $nAlign, $arrLineColors );

						if ( $oTeamBetted !== null and $oNow > $oDeadLine )
						{
							$oImage = $this->getImage ( $oTeamBetted );
							$this->drawImage( $oImage, $nXPosImg + 1, $nYPos - ( $nRowHeight - 1 ), $nXPosImg + ( $nRowHeight - 1 ), $nYPos - 1 );
						}

						$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $this->m_sInnerBorder
							, "r" => $this->m_sOuterBorder, "b" => $this->m_sInnerBorder
						);
						$sTextPoints = "";
						if ( $oPoule->getState() === Voetbal_Factory::STATE_PLAYED )
							$sTextPoints = $nPoints;
						$nXPos = $this->drawCell( $sTextPoints, $nXPos, $nYPos, $nUserPointColumnWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );
					}

					$nYPos -= $nRowHeight; $nXPos = $nMargin;
				}

				{
					$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $this->m_sInnerBorder
						, "r" => $this->m_sOuterBorder, "b" => $this->m_sInnerBorder
					);
					$this->setFillColor( new Zend_Pdf_Color_Html( "white" ) );
					$nXPos = $this->drawCell( "punten deze ronde", $nXPos, $nYPos, $nBaseWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );

					foreach ( $oPoolUsers as $oPoolUser )
					{
						$sTextPoints = "";
						if ( $oRound->getState() === Voetbal_Factory::STATE_PLAYED )
							$sTextPoints = $oPoolUser->getPoints( $oRound );

						$nXPos = $this->drawCell( $sTextPoints, $nXPos, $nYPos, $nUserColumnWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );
					}
				}

				$nYPos -= $nRowHeight;
				$nXPos = $nMargin;
			} // end for each roundbetconfig

			if ( $oRound->needsRanking() )
			{
				$nYPos = $this->createPouleStanden( $nYPos, $oRound );
			}
			//var_dump( $nYPos );
		} // end for each round
		$nYPos -= $nRowHeight;  $nXPos = $nMargin;

		$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $this->m_sInnerBorder
			, "r" => $this->m_sOuterBorder, "b" => $this->m_sInnerBorder
		);
		foreach ( $oRounds as $oRound )
		{
			$oRoundBetConfigs = $p_oPool->getBetConfigs( $oRound );

			$oRoundBetConfigScore = null; $oRoundBetConfigResult = null;
			foreach( $oRoundBetConfigs as $oRoundBetConfig )
			{
				if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId )
					$oRoundBetConfigScore = $oRoundBetConfig;
				if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Result::$nId )
					$oRoundBetConfigResult = $oRoundBetConfig;
			}

			foreach( $oRoundBetConfigs as $oRoundBetConfig )
			{
				$sRBCDescription = VoetbalOog_BetType_Factory::getDescription ( $oRoundBetConfig->getBetType() );
				if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Result::$nId
					and $oRoundBetConfigScore !== null
				)
					continue;
				else if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId
					and $oRoundBetConfigResult !== null
				)
					$sRBCDescription .= " + " . VoetbalOog_BetType_Factory::getDescription ( $oRoundBetConfigResult->getBetType() );

				$nXPos = $this->drawCell( "punten ".$oRound->getName() . " ( ".$sRBCDescription ." )", $nXPos, $nYPos, $nBaseWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );
				foreach ( $oPoolUsers as $oPoolUser )
				{
					$sText = "";
					if ( $oRound->getState() === Voetbal_Factory::STATE_PLAYED )
						$sText = $oPoolUser->getPoints( $oRound );
					$nXPos = $this->drawCell( $sText, $nXPos, $nYPos, $nUserColumnWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );
				}
				$nYPos -= $nRowHeight; $nXPos = $nMargin;
			}
		}

		$nXPos = $this->drawCell( "punten totaal", $nXPos, $nYPos, $nBaseWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );

		$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $this->m_sOuterBorder
			, "r" => $this->m_sOuterBorder, "b" => $this->m_sOuterBorder
		);
		foreach ( $oPoolUsers as $oPoolUser )
		{
			$sText = "";
			if ( $oCompetitionSeason->getState() === Voetbal_Factory::STATE_PLAYED )
				$sText = $oPoolUser->getPoints();
			$nXPos = $this->drawCell( $sText, $nXPos, $nYPos, $nUserColumnWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );
		}

		$nYPos -= $nRowHeight; $nXPos = $nMargin;

		$nXPos = $this->drawCell( "plaats", $nXPos, $nYPos, $nBaseWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );
		foreach ( $oPoolUsers as $sPoolUserId => $oPoolUser )
			$nXPos = $this->drawCell( "", $nXPos, $nYPos, $nUserColumnWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );

		//var_dump( $nYPos );
		//die();
	}

	protected function setHeaders( $nYPos, $oPoolUsers )
	{
		$this->setFillColor( new Zend_Pdf_Color_Html( "white" ) );

		$arrColumns = $this->m_oParent->getColumns();
		$nRowHeight = $this->m_oParent->getRowHeight();
		$nUserColumnWidth = $this->m_oParent->getUserColumnWidth();
		$nXPos = $this->getPageMargin();

		$arrLineColors = null;

		$nCount = 0;
		foreach ( $arrColumns as $sHeader => $nWidth )
		{
			if ( $this->m_bHeadersFirstTime === true )
			{
				$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $this->m_sOuterBorder
					, "r" => $this->m_sInnerBorder, "b" => $this->m_sInnerBorder
					);
				if ( $nCount++ === 0 )
				{
					$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $this->m_sOuterBorder
						, "r" => $this->m_sInnerBorder, "b" => $this->m_sInnerBorder
					);
				}
			}
			else
			{
				$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $this->m_sInnerBorder
					, "r" => $this->m_sInnerBorder, "b" => $this->m_sInnerBorder
				);
				if ( $nCount++ === 0 )
				{
					$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $this->m_sInnerBorder
						, "r" => $this->m_sInnerBorder, "b" => $this->m_sInnerBorder
					);
				}
			}
			// last columns is done by next paintcell
			$nXPos = $this->drawCell( $sHeader, $nXPos, $nYPos, $nWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );
		}

		if ( $this->m_bHeadersFirstTime === true )
		{
			$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $this->m_sOuterBorder
				, "r" => $this->m_sOuterBorder, "b" => $this->m_sInnerBorder
			);
		}
		else
		{
			$arrLineColors = array( "l" => $this->m_sOuterBorder, "t" => $this->m_sInnerBorder
				, "r" => $this->m_sOuterBorder, "b" => $this->m_sInnerBorder
			);
		}

		foreach ( $oPoolUsers as $oPoolUser )
			$nXPos = $this->drawCell( $oPoolUser->getUser()->getName(), $nXPos, $nYPos, $nUserColumnWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );

		$this->m_bHeadersFirstTime = false;

		return $nYPos - $nRowHeight;
	}

	protected function createPouleStanden ( $nYPos, $oRound )
	{
		$arrLineColors = array( "l" => $this->m_sInnerBorder, "t" => $this->m_sInnerBorder
			, "r" => $this->m_sInnerBorder, "b" => $this->m_sInnerBorder
		);

		// var_dump( $nYPos );
		$nMargin = $this->getPageMargin();
		$nRowHeight = $this->m_oParent->getRowHeight();
		$nYPos -= $nRowHeight; $nXPos = $nMargin;

		$nRankWidth = $this->m_oParent->getRankWidth();
		$nNumberOfPoulesPerLine = $this->m_oParent->getNrOfPoulesPerLine();

		$nDiff = 0;
		$nI = 1;
		$oPoules = $oRound->getPoules();
		foreach ( $oPoules as $sPouleId => $oPoule )
		{
			$nYPos += $nDiff;
			$nDiff = 0;

			$nXPos = $this->drawCell( $oPoule->getDisplayName( true ), $nXPos, $nYPos, $nRankWidth, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );
			$nYPos -= $nRowHeight;
			$nDiff += $nRowHeight;
			$nXPos -= $nRankWidth;

			// Set Headers
			{
				$nXPos = $this->drawCell( "nr", $nXPos, $nYPos, $nRankWidth * 0.08, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );
				$nXPos = $this->drawCell( "team", $nXPos, $nYPos, $nRankWidth * 0.68, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );
				$nXPos = $this->drawCell( "g", $nXPos, $nYPos, $nRankWidth * 0.06, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );
				$nXPos = $this->drawCell( "p", $nXPos, $nYPos, $nRankWidth * 0.06, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );
				$nXPos = $this->drawCell( "v", $nXPos, $nYPos, $nRankWidth * 0.06, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );
				$nXPos = $this->drawCell( "t", $nXPos, $nYPos, $nRankWidth * 0.06, $nRowHeight, ZendExt_Pdf_Page::ALIGNCENTER, $arrLineColors );

				$nYPos -= $nRowHeight;
				$nDiff += $nRowHeight;
				$nXPos -= $nRankWidth;
			}

			// Set PoulePlaces
			$oPoulePlaces = $oPoule->getPlacesByRank();
			$oPouleGames = $oPoule->getGames();
			$nNr = 1;
			foreach ( $oPoulePlaces as $oPoulePlace )
			{
				$sTeamName = null;
				$nNrOfPlayedGames = null;
				$nNrOfPoints = null;
				$nNrOfGoalsScored = null;
				$nNrOfGoalsReceived = null;
				$oImage = null;

				$nXPos = $this->drawCell( $nNr++, $nXPos, $nYPos, $nRankWidth * 0.08, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );

				if ( $oPoule->getState() === Voetbal_Factory::STATE_PLAYED )
				{
					$sTeamName = $oPoulePlace->getTeam()->getName();
					$nNrOfPlayedGames = $oPoulePlace->getNrOfPlayedGames( $oPouleGames );
					$nNrOfPoints = $oPoulePlace->getPoints( $oPouleGames );
					$nNrOfGoalsScored = $oPoulePlace->getNrOfGoalsScored( $oPouleGames );
					$nNrOfGoalsReceived = $oPoulePlace->getNrOfGoalsReceived( $oPouleGames );

					$oImage = $this->getImage ( $oPoulePlace->getTeam() );
				}

				$nXPosImg = $nXPos;
				if ( $oImage !== null )
				{
					$this->setPadding( $nRowHeight );
				}
				$nXPos = $this->drawCell( $sTeamName, $nXPos, $nYPos, $nRankWidth * 0.68, $nRowHeight, ZendExt_Pdf_Page::ALIGNLEFT, $arrLineColors );
				if ( $oImage !== null )
				{
					$this->setPadding( 1 );
					$this->drawImage( $oImage, $nXPosImg + 1, $nYPos - ( $nRowHeight - 1 ), $nXPosImg + ( $nRowHeight - 1 ), $nYPos - 1 );
				}
				$nXPos = $this->drawCell( $nNrOfPlayedGames, $nXPos, $nYPos, $nRankWidth * 0.06, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );
				$nXPos = $this->drawCell( $nNrOfPoints, $nXPos, $nYPos, $nRankWidth * 0.06, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );
				$nXPos = $this->drawCell( $nNrOfGoalsScored, $nXPos, $nYPos, $nRankWidth * 0.06, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );
				$nXPos = $this->drawCell( $nNrOfGoalsReceived, $nXPos, $nYPos, $nRankWidth * 0.06, $nRowHeight, ZendExt_Pdf_Page::ALIGNRIGHT, $arrLineColors );

				$nYPos -= $nRowHeight;
				$nDiff += $nRowHeight;
				$nXPos -= $nRankWidth;
			}

			if ( $nI % $nNumberOfPoulesPerLine === 0 )
			{
				$nYPos -= $nRowHeight; $nXPos = $nMargin;
				$nDiff = 0;
			}
			else
			{
				$nXPos += $nRankWidth;
				$nXPos += $nMargin;
			}
			$nI++;
		} // end for each poule
		// var_dump( $nYPos + $nRowHeight ); die();
		return $nYPos + $nRowHeight;
	}
}
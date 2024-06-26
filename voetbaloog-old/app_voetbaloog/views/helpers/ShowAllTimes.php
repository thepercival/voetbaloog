<?php

class Zend_View_Helper_ShowAllTimes
{
	protected $m_sTableClass = "table table-striped pu-highlight";
	protected $m_sRowClassPrefix = "pu-";
	protected $m_nMaxNrOfPoolsXS = 4;

	public function ShowAllTimes( $oPool )
	{
		$oCache = ZendExt_Cache::getDefaultCache();
		$sCacheId = "pool".$oPool->getId()."alltimes";
		$sHtml = $oCache->load( $sCacheId );
		if( $sHtml === false or APPLICATION_ENV !== "production" )
		{
			$sHtml = $this->getAllTimeRanking( $oPool );

			$oCache->save( $sHtml, $sCacheId, array( 'competitionseason'.$oPool->getCompetitionSeason()->getId(), 'pool'.$oPool->getId() ) );
		}
		return $sHtml;
	}

	protected function getCompetitionSeasons( $oPools )
	{
		$oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjects();
		$oNow = Agenda_Factory::createDateTime();
		foreach ( $oPools as $oPool )
		{
			$oCompetitionSeason = $oPool->getCompetitionSeason();
			if ( $oCompetitionSeason->getState() === Voetbal_Factory::STATE_PLAYED or
				$oNow > $oPool->getStartDateTime()
			) {
				$oCompetitionSeasons->add( $oCompetitionSeason );
			}

		}
		return $oCompetitionSeasons;
	}

	protected function getAllTimeRanking( $oPool )
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_Season::StartDateTime", "SmallerThanOrEqualTo", $oPool->getCompetitionSeason()->getSeason()->getStartDateTime() );
		$oOptions->addFilter( "VoetbalOog_Pool::Name", "EqualTo", $oPool->getName() );
		$oOptions->addOrder( "Voetbal_Season::StartDateTime", false );
		$oPools = VoetbalOog_Pool_Factory::createObjectsFromDatabase( $oOptions );

		$oCompetitionSeasons = $this->getCompetitionSeasons( $oPools );

		$oNow = Agenda_Factory::createDateTime();
		$arrAllTimeRankTotals = array();
		$arrAllTimeRankUsers = array();

		foreach ( $oPools as $oPoolIt )
		{
			$oCompetitionSeason = $oPoolIt->getCompetitionSeason();
			if ( !( $oCompetitionSeason->getState() === Voetbal_Factory::STATE_PLAYED or $oNow > $oPool->getStartDateTime() ) )
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
					$arrAllTimeRankUsers[$oUser->getId()]["name"] = $oUser->getName();
				}
				$arrAllTimeRankUsers[$oUser->getId()]["mostrecentpooluserid"] = $oRankedPoolUser->getId();

				$arrAllTimeRankUsers[$oUser->getId()][$oCompetitionSeason->getId()] = $nRealPoints;
				$arrAllTimeRankTotals[$oUser->getId()] += $nRealPoints;

				$nPreviousPoolPoints = $oRankedPoolUser->getPoints();
			}
		}

		arsort( $arrAllTimeRankTotals );

		$sHtml = "<table class=\"".$this->m_sTableClass."\">";
		$nStartShowingXS = $oCompetitionSeasons->count() - $this->m_nMaxNrOfPoolsXS;

		$sHtml .= "<tr class=\"tableheader\"><th>deelnemer</th>";
		$nCounter = 0;
		foreach ( $oCompetitionSeasons as $oCompetitionSeason )
		{
			$sClass = ( ++$nCounter <= $nStartShowingXS ) ? "class=\"hidden-xs\"" : null;
			$sHtml .= "<th ".$sClass." style='text-align: right;'>".$oCompetitionSeason->getCompetition()->getAbbreviation()."<br>";
			$sHtml .= $oCompetitionSeason->getSeason()->getName()."</th>";
		}
		$sHtml .= "<th style='text-align: right;'>tot.</th></tr>";

		foreach( $arrAllTimeRankTotals as $nUserId => $nTotalPoints )
		{
			$arrNrOfWins = array();
			if ( array_key_exists( "mostrecentpooluserid", $arrAllTimeRankUsers[$nUserId] ) )
			{
				$nPoolUserId = $arrAllTimeRankUsers[$nUserId]["mostrecentpooluserid"];
				$oPoolUserTmp = VoetbalOog_Pool_User_Factory::createObjectFromDatabase( $nPoolUserId );
				$arrNrOfWins = $oPoolUserTmp->getNrOfWins( false );
			}

			$sNameTotal = $arrAllTimeRankUsers[$nUserId]["name"];
			foreach ( $arrNrOfWins as $sCSWin )
				$sNameTotal .= "<span class=\"glyphicon glyphicon-star\" title=\"".$sCSWin."\"></span>";
			$sClassName = $this->m_sRowClassPrefix . $nUserId;
			$sHtml .= "<tr class=\"".$sClassName."\"><td>".$sNameTotal."</td>";

			$arrAllTimeRankUser = $arrAllTimeRankUsers[$nUserId];
			$nCounter = 0;
			foreach( $oCompetitionSeasons as $oCompetitionSeason )
			{
				$vtPoints = "na";
				if ( array_key_exists( $oCompetitionSeason->getId(), $arrAllTimeRankUser ) )
					$vtPoints = $arrAllTimeRankUser[ $oCompetitionSeason->getId() ];
				$sClass = ( ++$nCounter <= $nStartShowingXS ) ? "class=\"hidden-xs\"" : null;
				$sHtml .= "<td ".$sClass." style='text-align:right'>".$vtPoints."</td>";
			}
			$sHtml .= "<td style='text-align:right'>".$nTotalPoints."</td></tr>";
		}

		$sHtml .= "</table>";

		return $sHtml;
	}
}
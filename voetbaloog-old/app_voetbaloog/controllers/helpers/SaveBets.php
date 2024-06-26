<?php

/**
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class VoetbalOog_Helper_SaveBets extends Zend_Controller_Action_Helper_Abstract
{
	protected $m_sDivPrefix = null;
	protected $m_sControlPrefix = '_control_id_';
	protected $m_oPoolUser;
	protected $m_oPool;
	protected $m_arrErrors;

	public function direct( $sDivId, $oPoolUser, $oNow )
	{
		$this->m_sDivPrefix = $sDivId;
		$this->m_oPoolUser = $oPoolUser;
		$this->m_oPool = $oPoolUser->getPool();
		$this->m_arrErrors = array();

		$oDbWriter = VoetbalOog_Bet_Factory::createDbWriter();

		$oRounds = $this->m_oPool->getCompetitionSeason()->getRounds();
		foreach ( $oRounds as $oRound )
			$this->saveBets( $oRound, $oDbWriter, $oNow );

		try
		{
			if ( $oDbWriter->write() === true )
			{
				$oCache = ZendExt_Cache::getDefaultCache();
				$oCache->clean( Zend_Cache::CLEANING_MODE_MATCHING_TAG,	array( 'pool'.$this->m_oPool->getId() ) );
				return true;
			}

			if ( count( $this->m_arrErrors ) > 0 )
			{
				$sMessage = "";
				foreach ( $this->m_arrErrors as $sError )
					$sMessage .= "<div>".$sError."</div>";
				$sMessage .= "<div>Niet alle voorspellingen zijn opgeslagen.</div>";
				return $sMessage;
			}
		}
		catch ( Exception $oException )
		{
			return "onbekende fout: ".$oException->getMessage();
		}
		return "onbekende fout";
	}

	protected function saveBets( $oRound, $oDbWriter, $oNow )
	{
		$oRoundBetConfigs = $this->m_oPool->getBetConfigs( $oRound );

		$oRoundBetConfigScore = null;
		foreach( $oRoundBetConfigs as $oRoundBetConfig )
		{
			if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId )
				$oRoundBetConfigScore = $oRoundBetConfig;
		}

		foreach( $oRoundBetConfigs as $oRoundBetConfig )
		{
			$oBets = $this->m_oPoolUser->getBets( $oRoundBetConfig );
			$oBets->addObserver( $oDbWriter );
			$oBetsToRemove = VoetbalOog_Bet_Factory::createObjects();
			foreach( $oBets as $oBet )
			{
				if ( $oNow <= $oBet->getDeadLine() )
					$oBetsToRemove->add( $oBet );
			}
			$oBets->removeCollection( $oBetsToRemove );

			if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Score::$nId )
			{
				$oGames = $oRound->getGames();
				foreach( $oGames as $oGame )
				{
					$oDeadLine = $oRoundBetConfig->getDeadLine( $oGame );
					if ( $oNow > $oDeadLine )
						continue;

					$sId = $this->getControlId( $oRoundBetConfig, $oGame );

					$nHomeGoals = $this->getGoals( $oRoundBetConfig, $oGame, "_homegoals" );
					$nAwayGoals = $this->getGoals( $oRoundBetConfig, $oGame, "_awaygoals" );

					if ( $nHomeGoals > -1 and $nAwayGoals > -1 )
					{
						$oBetScore = VoetbalOog_Bet_Factory::createScore();

						$oBetScore->putId( $sId );
						$oBetScore->putPoolUser( $this->m_oPoolUser );
						$oBetScore->putRoundBetConfig( $oRoundBetConfig );
						$oBetScore->putGame( $oGame );
						$oBetScore->putHomeGoals( $nHomeGoals );
						$oBetScore->putAwayGoals( $nAwayGoals );

						$oBets->add( $oBetScore );
					}
				}
			}
			else if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Result::$nId )
			{
				$oGames = $oRound->getGames();
				foreach( $oGames as $oGame )
				{
					$oDeadLine = $oRoundBetConfig->getDeadLine( $oGame );
					if ( $oNow > $oDeadLine )
						continue;

					$sId = $this->getControlId( $oRoundBetConfig, $oGame );

					$nResult = -2;
					if ( $oRoundBetConfigScore !== null )
					{
						$nHomeGoals = $this->getGoals( $oRoundBetConfigScore, $oGame, "_homegoals" );
						$nAwayGoals = $this->getGoals( $oRoundBetConfigScore, $oGame, "_awaygoals" );

						// -1 for winst uitteam
						if ( $nHomeGoals > -1 and $nAwayGoals > -1 )
							$nResult = VoetbalOog_Bet_Factory::getResult( $nHomeGoals, $nAwayGoals );
					}
					else
					{
						$nResult = (int) $this->getRequest()->getParam( $sId );
					}

					if ( $nResult > -2 )
					{
						$oBetResult = VoetbalOog_Bet_Factory::createResult();

						$oBetResult->putId( $sId );
						$oBetResult->putPoolUser( $this->m_oPoolUser );
						$oBetResult->putRoundBetConfig( $oRoundBetConfig );
						$oBetResult->putGame( $oGame );
						$oBetResult->putResult( $nResult );

						$bRetVal = $oBets->add( $oBetResult );
					}
				}
			}
			else if ( $oRoundBetConfig->getBetType() === VoetbalOog_Bet_Qualify::$nId )
			{
				$oDeadLine = $oRoundBetConfig->getDeadLine();

				$arrTeams = array();
				$oPoulePlaces = $oRound->getPoulePlaces();
				foreach( $oPoulePlaces as $oPoulePlace )
				{
					if ( $oNow > $oDeadLine )
						continue;

					$sId = $this->getControlId( $oRoundBetConfig, $oPoulePlace );

					$nTeamId = (int) $this->getRequest()->getParam( $sId );

					if ( $nTeamId > 0 )
					{
						if ( array_key_exists( $nTeamId, $arrTeams ) === false )
							$arrTeams[ $nTeamId ] = true;
						else {
							$oTeam = Voetbal_Team_Factory::createObjectFromDatabase( $nTeamId );
							$this->m_arrErrors[] = "Je heeft meerdere keren ".$oTeam->getName()." laten doorgaan naar ronde ".$oRound->getName().".";
							continue;
						}

						$oBetQualify = VoetbalOog_Bet_Factory::createQualify();

						$oBetQualify->putId( $sId );
						$oBetQualify->putPoolUser( $this->m_oPoolUser );
						$oBetQualify->putRoundBetConfig( $oRoundBetConfig );
						$oBetQualify->putPoulePlace( $oPoulePlace );
						$oBetQualify->putTeam( $nTeamId );

						$oBets->add( $oBetQualify );
					}
				}
			}
		}
	}

	protected function getControlId( $oRoundBetConfig, $oObject, $sPostfix = null )
	{
		$sRetval = $this->m_sDivPrefix . $this->m_sControlPrefix . $oRoundBetConfig->getId() . "_" . $oObject->getId();
		if ( $sPostfix != null )
			$sRetval .= $sPostfix;
		return $sRetval;
	}

	protected function getGoals( $oRoundBetConfig, $oGame, $sPostfix )
	{
		$sId = $this->getControlId( $oRoundBetConfig, $oGame, $sPostfix );
		$vtGoals = $this->getRequest()->getParam( $sId );
		if ( $vtGoals === null )
			return -1;
		return (int) $vtGoals;
	}
}
?>
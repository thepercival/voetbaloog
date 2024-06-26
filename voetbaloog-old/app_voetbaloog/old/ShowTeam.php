<?php

class Zend_View_Helper_ShowTeam
{
	public function ShowTeam( $sDivId, $oCompetitionSeason, $oTeam, $arrOptions = array() )
	{
		if ( $oCompetitionSeason === null )
			return;

		$sJS = "";

		$nDataFlag = Voetbal_JSON::$nCompetitionSeason_Rounds;
		$nDataFlag += Voetbal_JSON::$nRound_Poules;
		$nDataFlag += Voetbal_JSON::$nPoule_Games;
		$nDataFlag += VoetbalOog_JSON::$nCompetitionSeason_Topscorers;
		$nDataFlag += VoetbalOog_JSON::$nGame_Participations;
		$nDataFlag += VoetbalOog_JSON::$nGame_Goals;
		$sJSON = Voetbal_CompetitionSeason_Factory::convertObjectToJSON( $oCompetitionSeason, $nDataFlag );

		$sJS .= "<script type=\"text/javascript\">";
		$sJS .= "var g_oCompetitionSeason = Voetbal_CompetitionSeason_Factory().createObjectFromJSON( ".$sJSON." );";

		$sJS .= "var g_oTeam = null;";
		if ( $oTeam !== null )
		{
			$sJS .= "var g_oTeams = g_oCompetitionSeason.getTeams();";
			$sJS .= "g_oTeam = g_oTeams[".$oTeam->getId()."];";
		}


		$sJSONOptions = "{";
		foreach( $arrOptions as $sId => $sValue )
		{
			if ( strlen( $sJSONOptions ) > 1 )
				$sJSONOptions .= ",";
			$sJSONOptions .= $sId . ":" . $sValue;
		}
		$sJSONOptions .= "}";

		$sJS .= "
						var g_oTeamViewExt = new Ctrl_TeamViewExt( '".$sDivId."', g_oCompetitionSeason, g_oTeam, ".$sJSONOptions." );
						g_oTeamViewExt.show();
					";

		$sJS .= "</script>";

		// oHeader.style.textDecoration = 'underline';

		return $sJS;
	}
}
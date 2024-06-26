function VoetbalOog_CompetitionSeason() 
{
	VoetbalOog_CompetitionSeason.baseConstructor.call( this );

	var m_sAbbreviation = null;
	var m_sName = null;
	var m_nPromotionRule = null;
	var m_oRounds = null;
	var m_oBetConfigs = null;
	var m_oTeams = null;
	var m_oTopscorers = null;
	var m_oStartDateTime = null;
	var m_nNrOfMinutesGame = null;
	var m_nNrOfMinutesExtraTime = null;
	var m_nWinPointsAfterGame = null;
	var m_nWinPointsAfterExtraTime = null;
	var m_oGames = null;
	var m_oTeamsInTheRace = null;

	this.getAbbreviation = function(){ return m_sAbbreviation; };
	this.putAbbreviation = function( sAbbreviation ){ m_sAbbreviation = sAbbreviation; };

	this.getName = function(){ return m_sName; };
	this.putName = function( sName ){ m_sName = sName; };

	this.getPromotionRule = function(){ return m_nPromotionRule; };
	this.putPromotionRule = function( nPromotionRule ){ m_nPromotionRule = nPromotionRule; };

	this.getNrOfMinutesGame = function(){ return m_nNrOfMinutesGame; };
	this.putNrOfMinutesGame = function( nNrOfMinutesGame ){ m_nNrOfMinutesGame = nNrOfMinutesGame; };

	this.getNrOfMinutesExtraTime = function(){ return m_nNrOfMinutesExtraTime; };
	this.putNrOfMinutesExtraTime = function( nNrOfMinutesExtraTime ){ m_nNrOfMinutesExtraTime = nNrOfMinutesExtraTime; };

	this.getWinPointsAfterGame = function(){ return m_nWinPointsAfterGame; };
	this.putWinPointsAfterGame = function( nWinPointsAfterGame ){ m_nWinPointsAfterGame = nWinPointsAfterGame; };

	this.getWinPointsAfterExtraTime = function(){ return m_nWinPointsAfterExtraTime; };
	this.putWinPointsAfterExtraTime = function( nWinPointsAfterExtraTime ){ m_nWinPointsAfterExtraTime = nWinPointsAfterExtraTime; };

	this.getRounds = function(){ return m_oRounds; };
	this.putRounds = function( oRounds ){ m_oRounds = oRounds; };

	this.getTeamsInTheRace = function(){ return m_oTeamsInTheRace; };
	this.putTeamsInTheRace = function( oTeamsInTheRace ){ m_oTeamsInTheRace = oTeamsInTheRace; };

	this.getBetConfigs = function( oRound )
	{
		$oBetConfigs = m_oBetConfigs[ oRound.getId() ];
		if ( $oBetConfigs == undefined ) {
			$oBetConfigs = new Array();
			m_oBetConfigs[ oRound.getId() ] = $oBetConfigs;
		}
		return $oBetConfigs;
	};
	this.putBetConfigs = function( oBetConfigs ){ m_oBetConfigs = oBetConfigs; };

	this.getTopscorers = function(){ return m_oTopscorers; };
	this.putTopscorers = function( oTopscorers ){ m_oTopscorers = oTopscorers; };

	this.getTeams = function()
	{
		if ( m_oTeams == null )
		{
			m_oTeams = new Object();
			var oRounds = this.getRounds();
			for ( var nI in oRounds )
			{
				if ( !( oRounds.hasOwnProperty( nI ) ) )
					continue;

				var oTeams = oRounds[nI].getTeams();
				for ( var nJ in oTeams )
				{
					if ( !( oTeams.hasOwnProperty( nJ ) ) )
						continue;

					var oTeam = oTeams[nJ];
					m_oTeams[ oTeam.getId() ] = oTeam;
				}
			}
		}
		return m_oTeams; 
	};

	this.getStartDateTime = function()
	{
		if ( m_oStartDateTime == null )
		{
			var oRounds = this.getRounds();
			for ( var nI in oRounds )
			{
				if ( !( oRounds.hasOwnProperty( nI ) ) )
					continue;

				var oRound = oRounds[nI];
				if ( oRound.isFirst() == true )
				{
					m_oStartDateTime = oRound.getStartDateTime();
					break;
				}
			}
		}
		return m_oStartDateTime;
	};

	this.getGames = function()
	{
		if ( m_oGames == null )
		{
			m_oGames = new Object();
			var oRounds = this.getRounds();
			for ( var nI in oRounds )
			{
				if ( !( oRounds.hasOwnProperty( nI ) ) )
					continue;

				var oGames = oRounds[nI].getGames();
				for ( var nJ in oGames )
				{
					if ( !( oGames.hasOwnProperty( nJ ) ) )
						continue;

					var oGame = oGames[nJ];
					m_oGames[ oGame.getId() ] = oGame;
				}
			}
		}
		return m_oGames; 
	};
}
Inheritance_Manager.extend(VoetbalOog_CompetitionSeason, Idable);
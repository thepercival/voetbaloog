function VoetbalOog_PoulePlace() 
{
	VoetbalOog_PoulePlace.baseConstructor.call( this );
	
	var m_nNumber = null;
	var m_oPoule = null;
	var m_oFromQualifyRule = null;
	var m_oToQualifyRule = null;
	var m_oTeam = null;
	var m_oGames = null;
	var m_nPenaltyPoints = null;
	
  	this.getName = function()
	{
		return this.getPoule().getName() + " - Nr. " + ( this.getNumber() + 1 );
	};
	
	this.getNumber = function(){ return m_nNumber; };
	this.putNumber = function( nNumber ){ m_nNumber = nNumber; };
	
	this.getPenaltyPoints = function(){ return m_nPenaltyPoints; };
	this.putPenaltyPoints = function( nPenaltyPoints ){ m_nPenaltyPoints = nPenaltyPoints; };
	
	this.getPoule = function()
	{
		if ( typeof m_oPoule == 'number' ) {
			m_oPoule = VoetbalOog_Poule_Factory().createObjectFromDatabase( m_oPoule );
		}
		return m_oPoule; 
	};
	this.putPoule = function( oPoule ){ m_oPoule = oPoule; };
	
	this.getTeam = function()
	{ 
		if ( typeof m_oTeam == 'number' )
			m_oTeam = VoetbalOog_Team_Factory().createObjectFromDatabase( m_oTeam );
		return m_oTeam; 
	};
	this.putTeam = function( oTeam ){ m_oTeam = oTeam; };

	this.getFromQualifyRule = function()
	{
		if ( typeof m_oFromQualifyRule == 'number' )
			m_oFromQualifyRule = VoetbalOog_QualifyRule_Factory().createObjectFromDatabase( m_oFromQualifyRule );
		return m_oFromQualifyRule;
	};
	this.putFromQualifyRule = function( oFromQualifyRule ){ m_oFromQualifyRule = oFromQualifyRule; };

	this.getToQualifyRule = function()
	{
		if ( typeof m_oToQualifyRule == 'number' )
			m_oToQualifyRule = VoetbalOog_QualifyRule_Factory().createObjectFromDatabase( m_oToQualifyRule );
		return m_oToQualifyRule;
	};
	this.putToQualifyRule = function( oToQualifyRule )
	{
		m_oToQualifyRule = oToQualifyRule;
	};
	
	
	
	this.getGames = function()
	{
		if ( m_oGames == null )
		{
			m_oGames = new Object();
			
			var oGames = this.getPoule().getGames();
			for ( var nI in oGames )
			{
				if ( !( oGames.hasOwnProperty( nI ) ) )
					continue;
				
				var oGame = oGames[nI];
				if ( this == oGame.getHomePoulePlace() || this == oGame.getAwayPoulePlace() )
				{	
					if ( m_oGames[nI] == undefined )
						m_oGames[nI] = oGame;
				}
			}
		}
		return m_oGames;
	};
	
	this.getNrOfPlayedGames = function( oGames )
	{
		var nNrOfPlayedGames = 0;
		for ( var nI in oGames )
		{
			if ( !( oGames.hasOwnProperty( nI ) ) )
				continue;
			
			var oGame = oGames[nI];
			
			if ( oGame.getState() == g_jsonVoetbal.nState_Played
			&& ( oGame.getHomePoulePlace() == this
				|| oGame.getAwayPoulePlace() == this
				)
			)
				nNrOfPlayedGames++;
		}
		return nNrOfPlayedGames;
	};
	
	this.getPoints = function( oGames )
	{
        if ( oGames == null )
            oGames = this.getGames();

		var oCompetitionSeason = this.getPoule().getRound().getCompetitionSeason();
		var nPoints = 0;
		for ( var nI in oGames )
		{
			if ( !( oGames.hasOwnProperty( nI ) ) )
				continue;
			
			var oGame = oGames[nI];
			
			if ( oGame.getState() == g_jsonVoetbal.nState_Played )
			{
				if ( oGame.getHomePoulePlace() == this )
				{
					if ( oGame.getHomeGoalsPenalty() > -1 )
					{
						if ( oGame.getHomeGoalsPenalty() > oGame.getAwayGoalsPenalty() )
							nPoints += oCompetitionSeason.getWinPointsAfterExtraTime(); // penalty
					}
					else if ( oGame.getHomeGoalsExtraTime() > -1 )
					{
						if ( oGame.getHomeGoalsExtraTime() > oGame.getAwayGoalsExtraTime() )
							nPoints += oCompetitionSeason.getWinPointsAfterExtraTime();
					}
					else if ( oGame.getHomeGoals() > oGame.getAwayGoals() )
						nPoints += oCompetitionSeason.getWinPointsAfterGame();
					else if ( oGame.getHomeGoals() == oGame.getAwayGoals() )
						nPoints += 1;
				}
				else if ( oGame.getAwayPoulePlace() == this )
				{
					if ( oGame.getHomeGoalsPenalty() > -1 )
					{
						if ( oGame.getAwayGoalsPenalty() > oGame.getHomeGoalsPenalty() )
							nPoints += oCompetitionSeason.getWinPointsAfterExtraTime(); // penalty
					}
					else if ( oGame.getHomeGoalsExtraTime() > -1 )
					{
						if ( oGame.getAwayGoalsExtraTime() > oGame.getHomeGoalsExtraTime() )
							nPoints += oCompetitionSeason.getWinPointsAfterExtraTime();
					}
					else if ( oGame.getAwayGoals() > oGame.getHomeGoals() )
						nPoints += oCompetitionSeason.getWinPointsAfterGame();
					else if ( oGame.getHomeGoals() == oGame.getAwayGoals() )
						nPoints += 1;
				}
			}
		}
		return nPoints;
	};

	this.getGoalDifference = function( oGames )
	{
        if ( oGames == null )
            oGames = this.getGames();
		return ( this.getNrOfGoalsScored( oGames ) - this.getNrOfGoalsReceived( oGames ) );
	};

	this.getNrOfGoalsScored = function( oGames )
	{
        if ( oGames == null )
            oGames = this.getGames();

		var nNrOfGoalsScored = 0;
		for ( var nI in oGames )
		{
			if ( !( oGames.hasOwnProperty( nI ) ) )
				continue;
			
			var oGame = oGames[nI];
			
			if ( oGame.getState() == g_jsonVoetbal.nState_Played )
			{
				if ( oGame.getHomePoulePlace() == this )
					nNrOfGoalsScored += oGame.getHomeGoalsCalc( false );
				else if ( oGame.getAwayPoulePlace() == this )
					nNrOfGoalsScored += oGame.getAwayGoalsCalc( false );
			}
		}
		return nNrOfGoalsScored;
	};

	this.getNrOfGoalsReceived = function( oGames )
	{
        if ( oGames == null )
            oGames = this.getGames();

		var nNrOfGoalsReceived = 0;
		for ( var nI in oGames )
		{
			if ( !( oGames.hasOwnProperty( nI ) ) )
				continue;
			
			var oGame = oGames[nI];
			
			if ( oGame.getState() == g_jsonVoetbal.nState_Played )
			{
				if ( oGame.getHomePoulePlace() == this )
					nNrOfGoalsReceived += oGame.getAwayGoalsCalc( false );
				else if ( oGame.getAwayPoulePlace() == this )
					nNrOfGoalsReceived += oGame.getHomeGoalsCalc( false );
			}
		}
		return nNrOfGoalsReceived;
	};
}
Inheritance_Manager.extend(VoetbalOog_PoulePlace, Idable);
function VoetbalOog_Round()
{
	// this.Idable();
	VoetbalOog_Round.baseConstructor.call( this );

	var m_sName = null;
	var m_nNumber = null;
	var m_nType = null;
	var m_bSemiCompetition = null;
	var m_oCompetitionSeason = null;
	var m_oPoules = null;
	var m_bPoulesNeedRanking = null;
	var m_oFromQualifyRules = null;
	var m_oToQualifyRules = null;
	var m_oTeams = null;
	var m_oStartDateTime = null;
	var m_oEndDateTime = null;
	var m_oGamesByPoule = null;
	var m_arrGamesByDate = null;
	var m_bGamesByDateSorted = false;
	var m_bInitPreviousRound = false;
	var m_oPreviousRound = null;
	var m_nState = null;

	this.getName = function(){ return m_sName; };
	this.putName = function( sName ){ m_sName = sName; };

	this.getNumber = function(){ return m_nNumber; };
	this.putNumber = function( nNumber ){ m_nNumber = nNumber; };

	this.getType = function(){ return m_nType; };
	this.putType = function( nType ){ m_nType = nType; };

	this.getSemiCompetition = function(){ return m_bSemiCompetition; };
	this.putSemiCompetition = function( bSemiCompetition ){ m_bSemiCompetition = bSemiCompetition; };

	this.getCompetitionSeason = function()
	{
		if ( typeof m_oCompetitionSeason == 'number' )
			m_oCompetitionSeason = VoetbalOog_CompetitionSeason_Factory().createObjectFromDatabase( m_oCompetitionSeason );
		return m_oCompetitionSeason;
	};
	this.putCompetitionSeason = function( oCompetitionSeason ){ m_oCompetitionSeason = oCompetitionSeason; };

	this.getPoules = function(){ return m_oPoules; };
  this.getPoulesAsArray = function() {
    var asarrRetVal = new AssociativeArray();
    var oPoules = this.getPoules(); // fill m_arrGamesByDate
    for (var nI in oPoules) {
      if (!(oPoules.hasOwnProperty(nI)))
        continue;

      asarrRetVal.add(oPoules[nI]);
    }
    return asarrRetVal.toArray();
  }
	this.putPoules = function( oPoules ){ m_oPoules = oPoules; };

	this.poulesNeedRanking = function()
	{
		if ( m_bPoulesNeedRanking == null )
		{
			m_bPoulesNeedRanking = false;

			var oPoules = this.getPoules();
			for ( var nI in oPoules )
			{
				if ( !( oPoules.hasOwnProperty( nI ) ) )
					continue;

				if ( oPoules[nI].needsRanking() == true )
				{
					m_bPoulesNeedRanking = true;
					break;
				}
			}
		}
		return m_bPoulesNeedRanking;
	};

	this.getFromQualifyRules = function(){ return m_oFromQualifyRules; };
	this.putFromQualifyRules = function( oFromQualifyRules ){ m_oFromQualifyRules = oFromQualifyRules; };

	this.getToQualifyRules = function(){ return m_oToQualifyRules; };
	this.putToQualifyRules = function( oToQualifyRules ){ m_oToQualifyRules = oToQualifyRules; };

	this.getTeams = function()
	{
		if ( m_oTeams == null )
		{
			m_oTeams = new Object();
			var oPoules = this.getPoules();
			for ( var nI in oPoules )
			{
				if ( !( oPoules.hasOwnProperty( nI ) ) )
					continue;

				var oTeams = oPoules[nI].getTeams();
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

	this.getGames = function()
	{
		if ( m_oGamesByPoule == null )
		{
			m_oGamesByPoule = new Object();
			m_arrGamesByDate = new Array();

			var oPoules = this.getPoules();
			for ( var nI in oPoules )
			{
				if ( !( oPoules.hasOwnProperty( nI ) ) )
					continue;

				var oGames = oPoules[nI].getGames();
				for ( var nJ in oGames )
				{
					if ( !( oGames.hasOwnProperty( nJ ) ) )
						continue;

					var oGame = oGames[nJ];
					m_oGamesByPoule[ oGame.getId() ] = oGame;

					m_arrGamesByDate.push( oGame );
				}
			}
		}
		return m_oGamesByPoule;
	};

	this.getGamesByDate = function()
	{
		if ( m_arrGamesByDate == null )
			this.getGames(); // fill m_arrGamesByDate

		if ( m_bGamesByDateSorted == false )
		{
			// sort m_arrGamesByDate
			m_arrGamesByDate.sort(
				function ( oGameA, oGameB )
				{
					if ( oGameA.getStartDateTime() < oGameB.getStartDateTime() )
						return -1;
					else if ( oGameA.getStartDateTime() > oGameB.getStartDateTime() )
						return 1;
					return ( oGameA.getViewOrder() > oGameB.getViewOrder() );
				}
			);

			m_bGamesByDateSorted = true;
		}

		return m_arrGamesByDate;
	};

	this.isFirst = function()
	{
		return this.getNumber() == 0;
	};

	this.isLast = function()
	{
		var oRound = null;
		var oRounds = this.getCompetitionSeason().getRounds();
		for ( var nI in oRounds )
		{
			if ( !( oRounds.hasOwnProperty( nI ) ) )
				continue;

			oRound = oRounds[nI];
		}
		return ( oRound == this );
	};

	this.getPrevious = function()
	{
		if ( m_bInitPreviousRound == false )
		{
			var nPreviousRoundNumber = this.getNumber(); nPreviousRoundNumber--;
			var oRounds = this.getCompetitionSeason().getRounds();
			for ( var nI in oRounds )
			{
				if ( !( oRounds.hasOwnProperty( nI ) ) )
					continue;

				oRound = oRounds[nI];
				if ( oRound.getNumber() == nPreviousRoundNumber ) {
					m_oPreviousRound = oRound;
					break;
				}
			}
			m_bInitPreviousRound = true;
		}

		return m_oPreviousRound;
	};

	this.getNext = function()
	{
		var nNextRoundNumber = this.getNumber(); nNextRoundNumber++;
		var oRounds = this.getCompetitionSeason().getRounds();
		for ( var nI in oRounds )
		{
			if ( !( oRounds.hasOwnProperty( nI ) ) )
				continue;

			oRound = oRounds[nI];
			if ( oRound.getNumber() == nNextRoundNumber )
				return oRound;
		}
		return null;
	};

	this.getStartDateTime = function()
	{
		if ( m_oStartDateTime == null )
		{
			// loop door de games en kijk welke de jongste datum heeft!
			var oPoules = this.getPoules();
			var bNoGames = true;
			for ( var nI in oPoules )
			{
				if ( !( oPoules.hasOwnProperty( nI ) ) )
					continue;

				var oGames = oPoules[nI].getGames();
				for ( var nJ in oGames )
				{
					if ( !( oGames.hasOwnProperty( nJ ) ) )
						continue;

					bNoGames = false;
					var oGame = oGames[nJ];
					if ( m_oStartDateTime == null || m_oStartDateTime > oGame.getStartDateTime() )
						m_oStartDateTime = oGame.getStartDateTime();
				}
			}

			if ( bNoGames == true )
			{
				var oPreviousRound = this.getPrevious();
				if ( oPreviousRound != null )
					m_oStartDateTime = oPreviousRound.getEndDateTime();
			}

		}
		return m_oStartDateTime;
	};

	this.getEndDateTime = function()
	{
		if ( m_oEndDateTime == null )
		{
			// loop door de games en kijk welke de jongste datum heeft!
			var oPoules = this.getPoules();
			var bNoGames = true;
			for ( var nI in oPoules )
			{
				if ( !( oPoules.hasOwnProperty( nI ) ) )
					continue;

				var oGames = oPoules[nI].getGames();
				for ( var nJ in oGames )
				{
					if ( !( oGames.hasOwnProperty( nJ ) ) )
						continue;

					bNoGames = false;
					var oGame = oGames[nJ];
					if ( m_oEndDateTime == null || m_oEndDateTime < oGame.getStartDateTime() )
						m_oEndDateTime = oGame.getStartDateTime();
				}
			}

			if ( bNoGames == true )
			{
				var oPreviousRound = this.getPrevious();
				if ( oPreviousRound != null )
					m_oEndDateTime = oPreviousRound.getEndDateTime();
			}

		}
		return m_oEndDateTime;
	};

	this.getState = function()
	{
		if ( m_nState == null )
		{
			var oPoules = this.getPoules();
			for ( var nI in oPoules )
			{
				if ( !( oPoules.hasOwnProperty( nI ) ) )
					continue;

				var oPoule = oPoules[nI];
				if ( oPoule.getState() != g_jsonVoetbal.nState_Played ) {
					m_nState = oPoule.getState();
					return m_nState;
				}
			}
			m_nState = g_jsonVoetbal.nState_Played;
		}
		return m_nState;
	};
}
Inheritance_Manager.extend(VoetbalOog_Round, Idable);

VoetbalOog_Round.TYPE_POULE = 1;
VoetbalOog_Round.TYPE_KNOCKOUT = 2;
VoetbalOog_Round.TYPE_WINNER = 4;

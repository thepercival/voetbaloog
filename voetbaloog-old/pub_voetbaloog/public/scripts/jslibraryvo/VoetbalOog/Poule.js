function VoetbalOog_Poule()
{
	VoetbalOog_Poule.baseConstructor.call( this );

	var m_sName = null;
	var m_nNumber = null;
	var m_oRound = null;
	var m_oPlaces = null;
	var m_oTeams = null;
	var m_oGames = null;
	var m_bNeedsRanking = null;
	var m_arrGamesByDate = null;
	var m_nState = null;
    var m_jsonQualifierLines = null;

	this.getName = function(){ return m_sName; };
	this.putName = function( sName ){ m_sName = sName; };

	this.getNumber = function(){ return m_nNumber; };
	this.putNumber = function( nNumber ){ m_nNumber = nNumber; };

	this.getRound = function()
	{
		if ( typeof m_oRound == 'number' )
			m_oRound = VoetbalOog_Round_Factory().createObjectFromDatabase( m_oRound );
		return m_oRound;
	};
	this.putRound = function( oRound ){ m_oRound = oRound; };

	this.getPlaces = function(){ return m_oPlaces; };
	this.putPlaces = function( oPlaces ){ m_oPlaces = oPlaces; };

	this.needsRanking = function()
	{
		if ( m_bNeedsRanking == null )
		{
			m_bNeedsRanking = false;
			var oPlaces = this.getPlaces();
			var nCount = 0;
			for ( var nI in oPlaces )
			{
				if ( !( oPlaces.hasOwnProperty( nI ) ) )
					continue;

				nCount++;
				if ( nCount > 2 )
				{
					m_bNeedsRanking = true;
					break;
				}
			}
		}
		return m_bNeedsRanking;
	};

	this.getTeams = function()
	{
		if ( m_oTeams == null )
		{
			m_oTeams = new Object();
			var oPlaces = this.getPlaces();
			for ( var nI in oPlaces )
			{
				if ( !( oPlaces.hasOwnProperty( nI ) ) )
					continue;

				var oTeam = oPlaces[nI].getTeam();
				if ( oTeam != null )
					m_oTeams[ oTeam.getId() ] = oTeam;
			}
		}
		return m_oTeams;
	};

	this.getGames = function(){ return m_oGames; };
  this.getGamesAsArray = function() {
    var asarrRetVal = new AssociativeArray();
    var oGames = this.getGames(); // fill m_arrGamesByDate
    for (var nI in oGames) {
      if (!(oGames.hasOwnProperty(nI)))
        continue;

      asarrRetVal.add(oGames[nI]);
    }
    return asarrRetVal.toArray();
  }
	this.putGames = function( oGames ){ m_oGames = oGames; };

	this.getGamesByDate = function()
	{
		if ( m_arrGamesByDate == null )
		{
			m_arrGamesByDate = new Array();

			var oGames = this.getGames(); // fill m_arrGamesByDate
			for ( var nI in oGames )
			{
				if ( !( oGames.hasOwnProperty( nI ) ) )
					continue;

				m_arrGamesByDate.push( oGames[nI] );
			}
			// sort
      orderGames(m_arrGamesByDate);
		}

		return m_arrGamesByDate;
	};

  function orderGames(arrGames) {
    arrGames.sort(
      function ( oGameA, oGameB )
      {
        if ( oGameA.getStartDateTime() < oGameB.getStartDateTime() )
          return -1;
        else if ( oGameA.getStartDateTime() > oGameB.getStartDateTime() )
          return 1;
        return ( oGameA.getViewOrder() > oGameB.getViewOrder() );
      }
    );
}

  this.getStartDateTime = function() {
    var games = this.getGamesAsArray();
    orderGames(games);
    firstGame = games.shift();
    if( firstGame == null) {
      return null;
    }
    return firstGame.getStartDateTime();
  }

	this.getState = function()
	{
		if ( m_nState == null )
		{
			var oGames = this.getGames();

			if ( Object_Factory().count( oGames ) == 0 )
			{
				var oPoulePlaces = this.getPlaces();
				if ( Object_Factory().count( oPoulePlaces ) == 1 )
				{
					for ( var nI in oPoulePlaces )
					{
						if ( !( oPoulePlaces.hasOwnProperty( nI ) ) )
							continue;

						if ( oPoulePlaces[nI].getTeam() != null )
						{
							m_nState = g_jsonVoetbal.nState_Played;
							return m_nState;
						}
						break;
					}
				}
				m_nState = g_jsonVoetbal.nState_Scheduled;
				return m_nState;
			}
			else
			{
				for ( var nI in oGames )
				{
					if ( !( oGames.hasOwnProperty( nI ) ) )
						continue;

					if ( oGames[nI].getState() != g_jsonVoetbal.nState_Played )
					{
						m_nState = g_jsonVoetbal.nState_Played;
						return m_nState;
					}
				}
			}
			m_nState = g_jsonVoetbal.nState_Played;
		}
		return m_nState;
	};

    this.getQualifierLines = function()
    {
        if ( m_jsonQualifierLines !== null )
            return m_jsonQualifierLines;

        m_jsonQualifierLines = { "single" : 0, "multi" : 0 };
        {
            var bPreviousQualifySingle = false;
            var bPreviousQualifyMulti = false;
            var oPlaces = this.getPlaces();
            for( var nI in oPlaces )
            {
                var oPoulePlace = oPlaces[nI];
                var oToQualifyRule = oPoulePlace.getToQualifyRule();

                var bQualifySingle = ( oToQualifyRule != null && oToQualifyRule.isSingle() );
                var bQualifyMulti = ( oToQualifyRule != null && !oToQualifyRule.isSingle() );

                if (bQualifySingle == false && bPreviousQualifySingle == true)
                    m_jsonQualifierLines.single = oPoulePlace.getNumber();
                else if (bQualifyMulti == false && bPreviousQualifyMulti == true)
                    m_jsonQualifierLines.multi = oPoulePlace.getNumber();

                bPreviousQualifySingle = bQualifySingle;
                bPreviousQualifyMulti = bQualifyMulti;
            }
        }
        return this.getQualifierLines();
    }
}
Inheritance_Manager.extend(VoetbalOog_Poule, Idable);

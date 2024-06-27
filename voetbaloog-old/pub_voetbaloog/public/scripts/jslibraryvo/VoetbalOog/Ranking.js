function VoetbalOog_Ranking( nPromotionRule )
{
	var fnGetPoulePlacesWithMostPoints;
	var m_nPromotionRule = nPromotionRule;
	var m_bSubtractPenaltyPoints = true;
	var m_arrFunctions = null;

	fnGetPoulePlacesWithMostPoints = function (asarrPoulePlaces, oGames) {
		var nMostPoints = null;
		var asarrRetVal = new AssociativeArray();
		for ( var nI in asarrPoulePlaces.Items) {
			if (!( asarrPoulePlaces.Items.hasOwnProperty(nI) ))
				continue;

			var oPoulePlace = asarrPoulePlaces.Items[nI];

			var nPoints = oPoulePlace.getPoints(oGames);
			if (m_bSubtractPenaltyPoints == true)
				nPoints -= oPoulePlace.getPenaltyPoints();

			if (nMostPoints == null || nPoints == nMostPoints) {
				nMostPoints = nPoints;
				asarrRetVal.add(oPoulePlace);
			}
			else if (nPoints > nMostPoints) {
				nMostPoints = nPoints;
				asarrRetVal.flush();
				asarrRetVal.add(oPoulePlace);
			}
		}

		return asarrRetVal;
	};

	var fnGetPoulePlacesWithFewestGamesPlayed = function( asarrPoulePlaces, oGames )
	{
		var nFewestGamesPlayed = -1;
		var asarrRetVal = new AssociativeArray();
		for ( var nI in asarrPoulePlaces.Items )
		{
			if ( !( asarrPoulePlaces.Items.hasOwnProperty( nI ) ) )
				continue;

			var oPoulePlace = asarrPoulePlaces.Items[nI];
			var nGamesPlayed = oPoulePlace.getNrOfPlayedGames( oGames );
			if ( nFewestGamesPlayed == -1 || nGamesPlayed == nFewestGamesPlayed )
			{
				nFewestGamesPlayed = nGamesPlayed;
				asarrRetVal.add( oPoulePlace );
			}
			else if( nGamesPlayed < nFewestGamesPlayed )
			{
				nFewestGamesPlayed = nGamesPlayed;
				asarrRetVal.flush();
				asarrRetVal.add( oPoulePlace );
			}
		}

		return asarrRetVal;
	};

	var fnGetBestPoulePlacesAgainstEachOther = function( asarrPoulePlaces, oGames )
	{
		var oGamesAgainstEachOther = getPlayedGamesBetweenEachOther( asarrPoulePlaces, oGames );

		return getBestPoulePlaces( asarrPoulePlaces, oGamesAgainstEachOther, true );
	};

	function getPlayedGamesBetweenEachOther( asarrPoulePlaces, oGames )
	{
		var oGamesRetVal = new Object();

		for ( var nJ in oGames )
		{
			if ( !( oGames.hasOwnProperty( nJ ) ) )
				continue;

			var oGame = oGames[nJ];
			if ( oGame.getState() == g_jsonVoetbal.nState_Played
				&& asarrPoulePlaces.has( oGame.getHomePoulePlace() ) == true
				&& asarrPoulePlaces.has( oGame.getAwayPoulePlace() ) == true
			)
				oGamesRetVal[ oGame.getId() ] = oGame;
		}

		return oGamesRetVal;
	}

	var fnGetPoulePlacesWithBestGoalDifference = function( asarrPoulePlaces, oGames )
	{
		var nBestGoalDifference = null;
		var asarrRetVal = new AssociativeArray();
		for ( var nI in asarrPoulePlaces.Items )
		{
			if ( !( asarrPoulePlaces.Items.hasOwnProperty( nI ) ) )
				continue;

			var oPoulePlace = asarrPoulePlaces.Items[nI];
			var nGoalDifference = oPoulePlace.getGoalDifference( oGames );
			if ( nBestGoalDifference == null )
			{
				nBestGoalDifference = nGoalDifference;
				asarrRetVal.add( oPoulePlace );
			}
			else
			{
				if ( nGoalDifference == nBestGoalDifference )
					asarrRetVal.add( oPoulePlace );
				else if( nGoalDifference > nBestGoalDifference )
				{
					nBestGoalDifference = nGoalDifference;
					asarrRetVal.flush();
					asarrRetVal.add( oPoulePlace );
				}
			}
		}

		return asarrRetVal;
	};

	var fnGetPoulePlacesWithMostGoalsScored = function ( asarrPoulePlaces, oGames )
	{
		var nMostGoalsScored = 0;
		var asarrRetVal = new AssociativeArray();
		for ( var nI in asarrPoulePlaces.Items )
		{
			if ( !( asarrPoulePlaces.Items.hasOwnProperty( nI ) ) )
				continue;

			var oPoulePlace = asarrPoulePlaces.Items[nI];
			var nGoalsScored = oPoulePlace.getNrOfGoalsScored( oGames );
			if ( nGoalsScored == nMostGoalsScored )
				asarrRetVal.add( oPoulePlace );
			else if( nGoalsScored > nMostGoalsScored )
			{
				nMostGoalsScored = nGoalsScored;
				asarrRetVal.flush();
				asarrRetVal.add( oPoulePlace );
			}
		}

		return asarrRetVal;
	};

	this.updatePoulePlaceRankings = function( oGames, oPoulePlaces )
	{
		var asarrPoulePlaces = null;

        if ( oPoulePlaces == null && oGames != null )
            asarrPoulePlaces = this.getPoulePlaces( oGames );
        else if ( oPoulePlaces != null) {
            asarrPoulePlaces = new AssociativeArray();
            for ( var nI in oPoulePlaces )
                asarrPoulePlaces.add( oPoulePlaces[nI] );
        }

		var nRanking = 1;
		rankingHelper( asarrPoulePlaces, oGames, nRanking );
	};

	this.getPoulePlaces = function( oGames )
	{
		var asarrPoulePlaces = new AssociativeArray();

		for ( var nI in oGames )
		{
			if ( !( oGames.hasOwnProperty( nI ) ) )
				continue;

			var oGame = oGames[nI];

			var oHomePoulePlace = oGame.getHomePoulePlace();
			if ( asarrPoulePlaces.has( oHomePoulePlace ) == false )
				asarrPoulePlaces.add( oHomePoulePlace );

			var oAwayPoulePlace = oGame.getAwayPoulePlace();
			if ( asarrPoulePlaces.has( oAwayPoulePlace ) == false )
				asarrPoulePlaces.add( oAwayPoulePlace );
		}
		return asarrPoulePlaces;
	};

	this.getPoulePlacesByRanking = function( oGames, oPoulePlaces )
	{
        var arrRetVal = new Array();

        if ( oPoulePlaces == null && oGames != null ) {
            var asarrPoulePlaces = this.getPoulePlaces( oGames );
            for (var nI in asarrPoulePlaces.Items) {
                if (!( asarrPoulePlaces.Items.hasOwnProperty(nI) ))
                    continue;
                arrRetVal.push(asarrPoulePlaces.Items[nI]);
            }
        }
        else
        {
            for (var nI in oPoulePlaces ) {
                arrRetVal.push( oPoulePlaces[nI] );
            }
        }

        arrRetVal.sort(
			function ( oPoulePlaceA, oPoulePlaceB )
			{
				return ( oPoulePlaceA.Ranking - oPoulePlaceB.Ranking ); // >
			}
		);

		return arrRetVal;
	};

	function rankingHelper( asarrPoulePlaces, oGames, nRanking )
	{
		if ( asarrPoulePlaces.count() == 0 )
			return;

		var asarrBestPoulePlaces = getBestPoulePlaces( asarrPoulePlaces, oGames, false );

		for ( var nI in asarrBestPoulePlaces.Items )
		{
			if ( !( asarrBestPoulePlaces.Items.hasOwnProperty( nI ) ) )
				continue;

			var oPoulePlace = asarrBestPoulePlaces.Items[nI];
			oPoulePlace.Ranking = nRanking++;
			asarrPoulePlaces.remove( oPoulePlace );
		}
		rankingHelper( asarrPoulePlaces, oGames, nRanking );
	}

	function getBestPoulePlaces( asarrPoulePlaces, oGames, bSkip )
	{
    var nrOfStartingPoulePlaces = asarrPoulePlaces.count();
		var arrFunctions = getFunctions();
		for ( var nI = 0 ; nI < arrFunctions.length ; nI++ )
		{
			var fnFunction = arrFunctions[nI];

			if ( fnFunction == fnGetBestPoulePlacesAgainstEachOther && ( bSkip == true || oGames == null  ) ) {
				continue;
      }

			if ( fnFunction == fnGetBestPoulePlacesAgainstEachOther ) {
				m_bSubtractPenaltyPoints = false;
        if( asarrPoulePlaces.count() === nrOfStartingPoulePlaces) {
          continue;
        }
      }

			asarrPoulePlaces = fnFunction( asarrPoulePlaces, oGames );

			if ( asarrPoulePlaces.count() < 2 ) {
				break;
      }
		}
		m_bSubtractPenaltyPoints = true;
    if ( asarrPoulePlaces.count() > 1 ) {
      doManualSorting(asarrPoulePlaces);
    }
		return asarrPoulePlaces;
	}

  function doManualSorting( asarrPoulePlaces )
  {
    var seasonName = getCompetitionSeasonAbbreviation(asarrPoulePlaces);
    if( seasonName === 'EK 24' && allFromSamePoule(2, asarrPoulePlaces))
    {
      var arrPoulePlaces = asarrPoulePlaces.toArray();
      arrPoulePlaces.sort(function (oPoulePlaceA, oPoulePlaceB) {
        return (oPoulePlaceA.getTeam().getName() < oPoulePlaceB.getTeam().getName() ? -1 : 1);
      });
      asarrPoulePlaces = new AssociativeArray();
      var oPoulePlace = undefined;
      while( oPoulePlace = arrPoulePlaces.shift() ) {
        asarrPoulePlaces.add(oPoulePlace);
      }
    }
  }

  function getCompetitionSeasonAbbreviation(asarrPoulePlaces)
  {
    for ( var nI in asarrPoulePlaces.Items ) {
      if (!(asarrPoulePlaces.Items.hasOwnProperty(nI))){
        continue;
      }

      var oPoulePlace = asarrPoulePlaces.Items[nI];
      return oPoulePlace.getPoule().getRound().getCompetitionSeason().getAbbreviation();
    }
    return '';
  }

  function allFromSamePoule(pouleNr, asarrPoulePlaces)
  {
    for ( var nI in asarrPoulePlaces.Items ) {
      if (!(asarrPoulePlaces.Items.hasOwnProperty(nI))){
        continue;
      }

      var oPoulePlace = asarrPoulePlaces.Items[nI];
      if( oPoulePlace.getPoule().getNumber() !== pouleNr) {
        return false;
      }
    }
    return true;
  }

	/*
	 *
	 */
	function getFunctions()
	{
		if ( m_arrFunctions == null )
		{
			m_arrFunctions = new Array();
			if ( m_nPromotionRule == 1 )
			{
				m_arrFunctions.push( fnGetPoulePlacesWithMostPoints );
				m_arrFunctions.push( fnGetPoulePlacesWithFewestGamesPlayed );
				m_arrFunctions.push( fnGetPoulePlacesWithBestGoalDifference );
				m_arrFunctions.push( fnGetPoulePlacesWithMostGoalsScored );
				m_arrFunctions.push( fnGetBestPoulePlacesAgainstEachOther );
			}
			else if ( m_nPromotionRule == 2 )
			{
				m_arrFunctions.push( fnGetPoulePlacesWithMostPoints );
				m_arrFunctions.push( fnGetPoulePlacesWithFewestGamesPlayed );
				m_arrFunctions.push( fnGetBestPoulePlacesAgainstEachOther );
				m_arrFunctions.push( fnGetPoulePlacesWithBestGoalDifference );
				m_arrFunctions.push( fnGetPoulePlacesWithMostGoalsScored );
			}
			else
			{
				throw "Unknown qualifying rule";
			}
		}
		return m_arrFunctions;
	}
}

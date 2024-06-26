function VoetbalOog_CompetitionSeason_Factory() 
{
	var instance = (function() 
	{
		var m_arrPool = new Array();

		function privateMethod () 
		{
			// ...
		}

		return { 
			createObjectsFromJSON: function ( oJSONs ) 
			{
				var arrObjects = new Array();

				for ( var nI in oJSONs ) 
				{
					if ( !( oJSONs.hasOwnProperty( nI ) ) )
						continue;

					var oJSON = oJSONs[nI];
					if ( oJSON == null )
						continue;
					else if ( typeof ( oJSON ) == 'number' )
						arrObjects[ oJSON ] = this.createObjectFromDatabase( oJSON );
					else
						arrObjects[ oJSON.Id ] = this.createObjectFromJSON( oJSON );
				}

				return arrObjects;
			}
			,
			createObjectFromJSON: function ( oJSON ) 
			{
				if ( oJSON == null )
					return null;

				var sId = oJSON.Id;
				var oObject = m_arrPool[sId];
				if ( oObject == null )
				{
					oObject = new VoetbalOog_CompetitionSeason();
					m_arrPool[sId] = oObject;

					oObject.putId( sId );
					oObject.putAbbreviation( oJSON.Abbreviation );
					oObject.putName( oJSON.Name );
					oObject.putPromotionRule( oJSON.PromotionRule );
					oObject.putNrOfMinutesGame( oJSON.NrOfMinutesGame );
					oObject.putNrOfMinutesExtraTime( oJSON.NrOfMinutesExtraTime );
					oObject.putWinPointsAfterGame( oJSON.WinPointsAfterGame );
					oObject.putWinPointsAfterExtraTime( oJSON.WinPointsAfterExtraTime );
					if ( oJSON.Rounds != null )
						oObject.putRounds( VoetbalOog_Round_Factory().createObjectsFromJSON( oJSON.Rounds ) );
					if ( oJSON.BetConfigs != null )
						oObject.putBetConfigs( VoetbalOog_Round_BetConfig_Factory().createObjectsFromJSON( oJSON.BetConfigs ) );
					if ( oJSON.Topscorers != null )
						oObject.putTopscorers( VoetbalOog_Person_Factory().createObjectsFromJSON( oJSON.Topscorers ) );
					if ( oJSON.TeamsInTheRace != null )
						oObject.putTeamsInTheRace( VoetbalOog_Team_Factory().createObjectsFromJSON( oJSON.TeamsInTheRace ) );
					}
				return oObject;
			}
			,
			createObjectFromDatabase: function ( nId ) 
			{
				if ( nId == null )
					return null;

				// var sId = oJSON.Id;
				var oObject = m_arrPool[nId];
				if ( oObject == null )
				{
					alert('create with ajakkes CompetitionSeason');
				}

				return oObject;
			}
		};
	})();

	VoetbalOog_CompetitionSeason_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_CompetitionSeason_Factory();
}
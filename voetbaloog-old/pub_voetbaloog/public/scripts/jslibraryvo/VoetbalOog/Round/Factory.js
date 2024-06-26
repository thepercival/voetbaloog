function VoetbalOog_Round_Factory() 
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
				var arrObjects = new Object();
				for ( var nI in oJSONs ) 
				{ 
					if ( !( oJSONs.hasOwnProperty( nI ) ) )
						continue;
						
					var oJSON = oJSONs[nI];
					if ( oJSON == null )
					{
						continue;
					}
					else if ( typeof ( oJSON ) == 'number' )
					{
						arrObjects[ oJSON ] = this.createObjectFromDatabase( oJSON );
						//alert('roundnr:'+oJSON);						
					}
					else
					{
						arrObjects[ oJSON.Id ] = this.createObjectFromJSON( oJSON );
						//alert('round:'+oJSON.Id);
					}
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
					oObject = new VoetbalOog_Round();
					m_arrPool[sId] = oObject;
					
					oObject.putId( sId );
					oObject.putName( oJSON.Name );
					oObject.putNumber( oJSON.Number );
					oObject.putSemiCompetition( oJSON.SemiCompetition );
					oObject.putType( oJSON.Type );
					if ( typeof( oJSON.CompetitionSeason ) == 'number' )
						oObject.putCompetitionSeason( oJSON.CompetitionSeason );
					else
						oObject.putCompetitionSeason( VoetbalOog_CompetitionSeason_Factory().createObjectFromJSON( oJSON.CompetitionSeason ) );
					oObject.putPoules( VoetbalOog_Poule_Factory().createObjectsFromJSON( oJSON.Poules ) );
					oObject.putFromQualifyRules( VoetbalOog_QualifyRule_Factory().createObjectsFromJSON( oJSON.FromQualifyRules ) );
					oObject.putToQualifyRules( VoetbalOog_QualifyRule_Factory().createObjectsFromJSON( oJSON.ToQualifyRules ) );
				}				
				return oObject;
			}	
			,
			createObjectFromDatabase: function ( nId ) 
			{
				if ( nId == null )
					return null;
				
				var oObject = m_arrPool[nId];
				if ( oObject == null )
				{
					alert('create with ajakkes round'); // See CompetitionSeasonFactory
				}				
				
				return oObject;
			}
			,
			getName: function ( oRound )
			{
				var oRounds = oRound.getCompetitionSeason().getRounds();

				var nPouleRounds = 0;
				{
					for ( var nI in oRounds )
					{
						if ( !( oRounds.hasOwnProperty( nI ) ) )
							continue;
						var oRoundIt = oRounds[nI];

						if ( oRoundIt.getType() == VoetbalOog_Round.TYPE_KNOCKOUT )
							break;
						nPouleRounds++;
					}
				}

				var sRoundName = "";
				if ( oRound.getNumber() + 1 > nPouleRounds ) {
					var nFromWinning = ( Object_Factory().count( oRounds ) ) - ( oRound.getNumber() + 1 );
					if ( nFromWinning == 5 ) { sRoundName = "<span style='font-size: 80%'><sup>1</sup>&frasl;<sub>16</sub></span> finale"; }
					else if ( nFromWinning == 4 ) { sRoundName = "&frac18; finale"; }
					else if ( nFromWinning == 3 ) { sRoundName = "&frac14; finale"; }
					else if ( nFromWinning == 2 ) { sRoundName = "&frac12; finale"; }
					else if ( nFromWinning == 1 ) { sRoundName = "finale"; }
					else if ( nFromWinning == 0 ) { sRoundName = "winnaar"; }
				}
				else {
					sRoundName = ( oRound.getNumber() + 1 ) + '<sup>' + ( oRound.getNumber() == 0 ? 'st' : 'd' ) + "e</sup> ronde";
				}
				return sRoundName;
			}
		};
	})();

	VoetbalOog_Round_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Round_Factory();
}
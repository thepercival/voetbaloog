function VoetbalOog_Poule_Factory() 
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
					oObject = new VoetbalOog_Poule();
					m_arrPool[sId] = oObject;
					
					oObject.putId( sId );
					oObject.putName( oJSON.Name );
					oObject.putNumber( oJSON.Number );
					if ( typeof( oJSON.Round ) == 'number' )
						oObject.putRound( oJSON.Round );
					else
						oObject.putRound( VoetbalOog_Round_Factory().createObjectFromJSON( oJSON.Round ) );
					oObject.putPlaces( VoetbalOog_PoulePlace_Factory().createObjectsFromJSON( oJSON.Places ) );
					oObject.putGames( VoetbalOog_Game_Factory().createObjectsFromJSON( oJSON.Games ) );
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
					alert('create with ajakkes poule'); // See CompetitionSeasonFactory
				}				
				
				return oObject;
			}
			,
			getName: function ( oPoule, bWithPrefix )
			{
				var nPreviousNrOfPoules = 0;
				{
					var oRounds = oPoule.getRound().getCompetitionSeason().getRounds();
					for ( var nI in oRounds ) {
						if (!( oRounds.hasOwnProperty(nI) ))
							continue;
						var oRoundIt = oRounds[nI];

						if (oRoundIt.getNumber() == oPoule.getRound().getNumber())
							break;
						nPreviousNrOfPoules += Object_Factory().count(oRoundIt.getPoules());
					}
				}
				var sPouleName = "";
				if ( bWithPrefix == true )
					sPouleName = oPoule.getRound().getType() == VoetbalOog_Round.TYPE_KNOCKOUT ? "wed. " : "poule ";
				sPouleName += String.fromCharCode(65 + nPreviousNrOfPoules + oPoule.getNumber() );
				return sPouleName;
			}
		};
	})();

	VoetbalOog_Poule_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Poule_Factory();		
}
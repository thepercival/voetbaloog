function VoetbalOog_Round_BetConfig_Factory() 
{
	var instance = (function() 
	{
		var m_arrPool = new Array();

		function privateMethod () 
		{
			// ...
		}

		return { 
			createObject: function () 
			{
				return new VoetbalOog_Round_BetConfig();
			}
			,			
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
					
					if ( oJSON.BetConfigsTmp != undefined )
						arrObjects[ oJSON.Id ] = this.createObjectsFromJSON( oJSON.BetConfigsTmp );
					else
						arrObjects[ oJSON.BetType ] = this.createObjectFromJSON( oJSON );
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
					oObject = this.createObject();
					m_arrPool[sId] = oObject;
					
					oObject.putId( sId );
					oObject.putPoints( oJSON.Points );
					oObject.putBetType( oJSON.BetType );
					oObject.putBetTime( oJSON.BetTime );
					if ( typeof( oJSON.Round ) == 'number' )
						oObject.putRound( oJSON.Round );
					else
						oObject.putRound( VoetbalOog_Round_Factory().createObjectFromJSON( oJSON.Round ) );
					if ( typeof( oJSON.Pool ) == 'number' )
						oObject.putPool( oJSON.Pool );
					else
						oObject.putPool( VoetbalOog_Pool_Factory().createObjectFromJSON( oJSON.Pool ) );
					if ( oJSON.SameTeams != null )
						oObject.putSameTeams( VoetbalOog_Team_Factory().createObjectsFromJSON( oJSON.SameTeams ) );
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
					alert('create with ajakkes round_betconfig'); // See CompetitionSeasonFactory
				}				
				
				return oObject;
			}	
		};
	})();

	VoetbalOog_Round_BetConfig_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Round_BetConfig_Factory();
}
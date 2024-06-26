function VoetbalOog_Team_Membership_Player_Factory() 
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
					oObject = new VoetbalOog_Team_Membership_Player();
					m_arrPool[sId] = oObject;
					
					oObject.putId( sId );
					oObject.putBackNumber( oJSON.BackNumber );
					if ( typeof( oJSON.Client ) == 'number' )
						oObject.putClient( oJSON.Client );
					else
						oObject.putClient( VoetbalOog_Person_Factory().createObjectFromJSON( oJSON.Client ) );
					if ( typeof( oJSON.Provider ) == 'number' )
						oObject.putProvider( oJSON.Provider );
					else
						oObject.putProvider( VoetbalOog_Team_Factory().createObjectFromJSON( oJSON.Provider ) );
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
					alert('create with ajakkes teammembershipplayer'); // See CompetitionSeasonFactory
				}				
				
				return oObject;
			}
		};
	})();

	VoetbalOog_Team_Membership_Player_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Team_Membership_Player_Factory();
}
function VoetbalOog_Game_Participation_Factory() 
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
					oObject = new VoetbalOog_Game_Participation();
					m_arrPool[sId] = oObject;
					
					oObject.putId( sId );
					if ( typeof( oJSON.Game ) == 'number' )
						oObject.putGame( oJSON.Game );
					else
						oObject.putGame( VoetbalOog_Game_Factory().createObjectFromJSON( oJSON.Game ) );
					if ( typeof( oJSON.TeamMembershipPlayer ) == 'number' )
						oObject.putTeamMembershipPlayer( oJSON.TeamMembershipPlayer );
					else
						oObject.putTeamMembershipPlayer( VoetbalOog_Team_Membership_Player_Factory().createObjectFromJSON( oJSON.TeamMembershipPlayer ) );
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
					alert('create with ajakkes game'); // See Game_Participation_Factory
				}				
				
				return oObject;
			}			
		};
	})();

	VoetbalOog_Game_Participation_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Game_Participation_Factory(); // call the new function
}

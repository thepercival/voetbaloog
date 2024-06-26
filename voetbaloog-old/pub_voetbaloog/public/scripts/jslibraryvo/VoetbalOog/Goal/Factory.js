function VoetbalOog_Goal_Factory() 
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
					oObject = new VoetbalOog_Goal();
					m_arrPool[sId] = oObject;
					
					oObject.putId( sId );
					if ( typeof( oJSON.GameParticipant ) == 'number' )
						oObject.putGameParticipant( oJSON.GameParticipant );
					else
						oObject.putGameParticipant( VoetbalOog_Game_Participant_Factory().createObjectFromJSON( oJSON.GameParticipant ) );
					oObject.putMinute( oJSON.Minute );
					oObject.putOwnGoal( oJSON.OwnGoal );
					oObject.putPenalty( oJSON.Penalty );
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
					alert('create with ajakkes game');
				}				
				
				return oObject;
			}
		};
	})();

	VoetbalOog_Goal_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Goal_Factory();
}
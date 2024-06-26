function VoetbalOog_Pool_User_Factory() 
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
					oObject = new VoetbalOog_Pool_User();
					m_arrPool[sId] = oObject;
					
					oObject.putId( sId );
					oObject.putAdmin( oJSON.Admin );
					oObject.putPaid( oJSON.Paid );
					oObject.putPoints( oJSON.Points );
					oObject.putRanking( oJSON.Ranking );
					if ( typeof( oJSON.Pool ) == 'number' )
						oObject.putPool( oJSON.Pool );
					else
						oObject.putPool( VoetbalOog_Pool_Factory().createObjectFromJSON( oJSON.Pool ) );
					if ( typeof( oJSON.User ) == 'string' )
						oObject.putUser( oJSON.User );
					else
						oObject.putUser( VoetbalOog_User_Factory().createObjectFromJSON( oJSON.User ) );
					
					oObject.putBets( VoetbalOog_Bet_Factory().createObjectsFromJSON( oJSON.Bets ) );
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
					alert('create with ajakkes pool_user'); // See CompetitionSeasonFactory
				}				
				
				return oObject;
			}
		};
	})();

	VoetbalOog_Pool_User_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Pool_User_Factory(); // call the new function
}
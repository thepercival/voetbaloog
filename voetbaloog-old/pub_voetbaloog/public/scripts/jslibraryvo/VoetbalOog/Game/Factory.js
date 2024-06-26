function VoetbalOog_Game_Factory()
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
					{
						arrObjects[ oJSON.Id ] = this.createObjectFromJSON( oJSON );
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
					oObject = new VoetbalOog_Game();
					m_arrPool[sId] = oObject;

					oObject.putId( sId );

					if ( oJSON.StartDateTime != null )
						oObject.putStartDateTime( new Date( oJSON.StartDateTime ) );
					if ( typeof( oJSON.HomePoulePlace ) == 'number' )
						oObject.putHomePoulePlace( oJSON.HomePoulePlace );
					else
						oObject.putHomePoulePlace( VoetbalOog_PoulePlace_Factory().createObjectFromJSON( oJSON.HomePoulePlace ) );
					if ( typeof( oJSON.AwayPoulePlace ) == 'number' )
						oObject.putAwayPoulePlace( oJSON.AwayPoulePlace );
					else
						oObject.putAwayPoulePlace( VoetbalOog_PoulePlace_Factory().createObjectFromJSON( oJSON.AwayPoulePlace ) );
					oObject.putHomeGoals( oJSON.HomeGoals );
					oObject.putAwayGoals( oJSON.AwayGoals );
					oObject.putHomeGoalsExtraTime( oJSON.HomeGoalsExtraTime );
					oObject.putAwayGoalsExtraTime( oJSON.AwayGoalsExtraTime );
					oObject.putHomeGoalsPenalty( oJSON.HomeGoalsPenalty );
					oObject.putAwayGoalsPenalty( oJSON.AwayGoalsPenalty );
					oObject.putCity( oJSON.City );
					oObject.putState( oJSON.State );
					oObject.putViewOrder( oJSON.ViewOrder );

					oObject.putParticipations( VoetbalOog_Game_Participation_Factory().createObjectsFromJSON( oJSON.Participations ) );
					oObject.putGoals( VoetbalOog_Goal_Factory().createObjectsFromJSON( oJSON.Goals ) );
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
					alert('create with ajakkes game'); // See CompetitionSeasonFactory
				}

				return oObject;
			}
			,
			clone: function ( oGame )
			{
				var oObject = new VoetbalOog_Game();

				oObject.putId( oGame.getId() + "__clone" );
				oObject.putStartDateTime( oGame.getStartDateTime() );
				oObject.putHomePoulePlace( oGame.getHomePoulePlace() );
				oObject.putAwayPoulePlace( oGame.getAwayPoulePlace() );
				oObject.putHomeGoals( oGame.getHomeGoals() );
				oObject.putAwayGoals( oGame.getAwayGoals() );
				oObject.putHomeGoalsExtraTime( oGame.getHomeGoalsExtraTime() );
				oObject.putAwayGoalsExtraTime( oGame.getAwayGoalsExtraTime() );
				oObject.putHomeGoalsPenalty( oGame.getHomeGoalsPenalty() );
				oObject.putAwayGoalsPenalty( oGame.getAwayGoalsPenalty() );
				oObject.putCity( oGame.getCity() );
				oObject.putState( oGame.getState() );
				oObject.putViewOrder( oGame.getViewOrder() );

				return oObject;
			}
		};
	})();

	VoetbalOog_Game_Factory = function ()
	{
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Game_Factory(); // call the new function
}

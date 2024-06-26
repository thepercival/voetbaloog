function VoetbalOog_Pool_Factory()
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
					oObject = new VoetbalOog_Pool();
					m_arrPool[sId] = oObject;

					oObject.putId( sId );
					oObject.putName( oJSON.Name );
					oObject.putNrOfAvailableBets( oJSON.NrOfAvailableBets );

					if ( typeof( oJSON.CompetitionSeason ) == 'number' )
						oObject.putCompetitionSeason( oJSON.CompetitionSeason );
					else
						oObject.putCompetitionSeason( VoetbalOog_CompetitionSeason_Factory().createObjectFromJSON( oJSON.CompetitionSeason ) );
					if ( oJSON.BetConfigs != null )
						oObject.putBetConfigs( VoetbalOog_Round_BetConfig_Factory().createObjectsFromJSON( oJSON.BetConfigs ) );
					if ( oJSON.Users != null )
						oObject.putUsers( VoetbalOog_Pool_User_Factory().createObjectsFromJSON( oJSON.Users ) );
					if ( oJSON.Payments != null )
						oObject.putPayments( VoetbalOog_Pool_Payment_Factory().createObjectsFromJSON( oJSON.Payments ) );
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
					alert('create with ajakkes pool'); // See CompetitionSeasonFactory
				}

				return oObject;
			}
		};
	})();

	VoetbalOog_Pool_Factory = function ()
	{
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Pool_Factory(); // call the new function
}
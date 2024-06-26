function VoetbalOog_Bet_Factory()
{
	var instance = (function()
	{
		var m_arrPool = new Array();

		function privateMethod ()
		{
			// ...
		}

		return {
			getResultDescription: function ( nResultBetted )
			{
				if ( nResultBetted == 1 )
					return 'thuis wint';
				if ( nResultBetted == 0 )
					return 'gelijk';
				if ( nResultBetted == -1 )
					return 'uit wint';

				return null;
			}
			,
			createObjectsFromJSON: function ( oJSONs )
			{
				// oJSONs is
				//		[
				//			{ "RoundBetConfigId" : 12,
				//				"Bets"			:  [
				//			                            { Id : 13 /*pouleplaceid or gameid */, HomeGoals : 2 }
				//			                         , 	{ Id : 14 /*pouleplaceid or gameid */, HomeGoals : 3 }
				//			                         ]
				//			}
				//		, 	{ "RoundBetConfigId" : 13,
				//				"Bets"			:  [
				//			                            { Id : 23 /*pouleplaceid or gameid */, HomeGoals : 2 }
				//			                         , 	{ Id : 34 /*pouleplaceid or gameid */, HomeGoals : 1 }
				//			                         ]
				//			}
				//		]

				var arrObjects = new Object();

				for ( var nI in oJSONs )
				{
					if ( !( oJSONs.hasOwnProperty( nI ) ) )
						continue;

					var oJSON = oJSONs[nI];
					if ( oJSON == null )
						continue;

					if ( oJSON.RoundBetConfigId != undefined )
						arrObjects[ oJSON.RoundBetConfigId ] = this.createObjectsFromJSON( oJSON.Bets );
					//else if ( typeof ( oJSON ) == 'number' )
						//arrObjects[ oJSON ] = this.createObjectFromDatabase( oJSON );
					else
						arrObjects[ oJSON.IdExtra ] = this.createObjectFromJSON( oJSON );
				}

				return arrObjects;
			}
			,
			createObjectFromJSON: function ( oJSON )
			{
				if ( oJSON == null )
					return null;

				var sId = oJSON.Id;
				// pool kent dezefde waarden voor result en score!!!!!!!!!!!!!!!!!11
				var oObject = m_arrPool[sId];
				if ( oObject == null )
				{
					var nBetType = oJSON.BetType;
					oObject = this.createObject( nBetType );
					m_arrPool[sId] = oObject;

					oObject.putId( sId );

					oObject.putCorrect( oJSON.Correct );
					if ( typeof( oJSON.PoolUser ) == 'number' )
						oObject.putPoolUser( oJSON.PoolUser );
					else
						oObject.putPoolUser( VoetbalOog_Pool_User_Factory().createObjectFromJSON( oJSON.PoolUser ) );

					if ( typeof( oJSON.RoundBetConfig ) == 'number' )
						oObject.putRoundBetConfig( oJSON.RoundBetConfig );
					else
						oObject.putRoundBetConfig( VoetbalOog_Round_BetConfig_Factory().createObjectFromJSON( oJSON.RoundBetConfig ) );

					if ( nBetType == VoetbalOog_Bet_Qualify.nId )
					{
						if ( typeof( oJSON.PoulePlace ) == 'number' )
							oObject.putPoulePlace( oJSON.PoulePlace );
						else
							oObject.putPoulePlace( VoetbalOog_PoulePlace_Factory().createObjectFromJSON( oJSON.PoulePlace ) );

						if ( typeof( oJSON.Team ) == 'number' )
							oObject.putTeam( oJSON.Team );
						else
							oObject.putTeam( VoetbalOog_Team_Factory().createObjectFromJSON( oJSON.Team ) );
					}
					else if ( nBetType == VoetbalOog_Bet_Score.nId || nBetType == VoetbalOog_Bet_Result.nId )
					{
						if ( typeof( oJSON.Game ) == 'number' )
							oObject.putGame( oJSON.Game );
						else
							oObject.putGame( VoetbalOog_Game_Factory().createObjectFromJSON( oJSON.Game ) );

						if ( nBetType == VoetbalOog_Bet_Score.nId )
						{
							oObject.putHomeGoals( oJSON.HomeGoals );
							oObject.putAwayGoals( oJSON.AwayGoals );
						}

						if ( nBetType == VoetbalOog_Bet_Result.nId )
							oObject.putResult( oJSON.Result );
					}
				}
				return oObject;
			}
			,
			createObject: function ( nBetType )
			{
				if ( nBetType == VoetbalOog_Bet_Qualify.nId )
					return new VoetbalOog_Bet_Qualify();
				else if ( nBetType == VoetbalOog_Bet_Score.nId )
					return new VoetbalOog_Bet_Score();
				else if ( nBetType == VoetbalOog_Bet_Result.nId )
					return new VoetbalOog_Bet_Result();
				throw ( "Not a correct bettype" );
			}
			,
			createObjectFromDatabase: function ( nId )
			{
				if ( nId == null )
					return null;

				var oObject = m_arrPool[nId];
				if ( oObject == null )
				{
					alert('create with ajakkes bet');
				}

				return oObject;
			}
		};
	})();

	VoetbalOog_Bet_Factory = function ()
	{
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Bet_Factory(); // call the new function
}

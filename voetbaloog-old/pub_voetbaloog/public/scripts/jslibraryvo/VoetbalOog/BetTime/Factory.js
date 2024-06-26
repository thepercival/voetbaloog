function VoetbalOog_BetTime_Factory() 
{
	var instance = (function() 
	{
		function privateMethod () 
		{
			// ...
		}

		return { 
			get: function ( nBetType ) 
			{
				var nAll = VoetbalOog_BetTime.nBeforeStartGame + VoetbalOog_BetTime.nBeforeStartRound + VoetbalOog_BetTime.nBeforeCompetitionSeason + VoetbalOog_BetTime.nBeforeStartPreviousRound;
				if ( nBetType == null )
					return nAll;
				else if ( nBetType == VoetbalOog_Bet_Qualify.nId )
		    		return ( VoetbalOog_BetTime.nBeforeCompetitionSeason + VoetbalOog_BetTime.nBeforeStartPreviousRound );
		    	else if ( nBetType == VoetbalOog_Bet_Result.nId || nBetType == VoetbalOog_Bet_Score.nId )
			    	return ( VoetbalOog_BetTime.nBeforeStartGame + VoetbalOog_BetTime.nBeforeStartRound + VoetbalOog_BetTime.nBeforeCompetitionSeason );
				return nAll;
			}
			,			
			getDescription: function ( nBetTime ) 
			{
			    if ( nBetTime == VoetbalOog_BetTime.nBeforeStartGame )
					return 'begin wedstrijd';
				else if ( nBetTime == VoetbalOog_BetTime.nBeforeStartRound )
					return 'begin ronde';
				else if ( nBetTime == VoetbalOog_BetTime.nBeforeCompetitionSeason )
					return 'begin toernooi';
				else if ( nBetTime == VoetbalOog_BetTime.nBeforeStartPreviousRound )
					return 'begin vorige ronde';
				return null;
			}
		};
	})();

	VoetbalOog_BetTime_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_BetTime_Factory();
}

function VoetbalOog_BetTime(){
	
} 
 
VoetbalOog_BetTime.nBeforeStartGame = 1;
VoetbalOog_BetTime.nBeforeStartRound = 2;
VoetbalOog_BetTime.nBeforeCompetitionSeason = 4;
VoetbalOog_BetTime.nBeforeStartPreviousRound = 8;
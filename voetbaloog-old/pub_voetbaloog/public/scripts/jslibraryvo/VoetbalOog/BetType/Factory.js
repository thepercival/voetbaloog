function VoetbalOog_BetType_Factory() 
{
	var instance = (function() 
	{
		function privateMethod () 
		{
			// ...
		}
		 
		return { 
			getDescription: function ( nBetType ) 
			{
				if ( nBetType == VoetbalOog_Bet_Qualify.nId )
					return 'gekwalificeerden';
				else if ( nBetType == VoetbalOog_Bet_Result.nId )
					return 'resultaat';
				else if ( nBetType == VoetbalOog_Bet_Score.nId )
					return 'score';
				return null;
			}
			,
			getAll: function ( oRound )
			{
				if ( oRound == null )
					return VoetbalOog_Bet_Qualify.nId + VoetbalOog_Bet_Result.nId + VoetbalOog_Bet_Score.nId;
				else if ( oRound.isFirst() )
		    		return VoetbalOog_Bet_Result.nId + VoetbalOog_Bet_Score.nId;
		    	else if ( oRound.isLast() )
			    	return VoetbalOog_Bet_Qualify.nId;
				return VoetbalOog_Bet_Qualify.nId + VoetbalOog_Bet_Result.nId + VoetbalOog_Bet_Score.nId;
			}
			,
			getDependant: function ( nBetType ) 
			{
				if ( nBetType == VoetbalOog_Bet_Result.nId || nBetType == VoetbalOog_Bet_Score.nId )
					return VoetbalOog_Bet_Result.nId + VoetbalOog_Bet_Score.nId;
				return nBetType;
			}
		};
	})();

	VoetbalOog_BetType_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_BetType_Factory();
}
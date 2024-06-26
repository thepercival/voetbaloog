function VoetbalOog_Pool()
{
	VoetbalOog_Pool.baseConstructor.call( this );

	var m_sName = null;
	var m_arrNrOfAvailableBets = null;
	var m_oCompetitionSeason = null;
	var m_oBetConfigs = null;
	var m_oUsers = null;
	var m_arrUsersByRanking = null;
	var m_oPayments = null;
	var m_nLastRanking = null;

	this.getName = function(){ return m_sName; };
	this.putName = function( sName ){ m_sName = sName; };

	this.getNrOfAvailableBets = function( oRound ){
        if ( oRound == null ) {
            return m_arrNrOfAvailableBets.reduce(function(a, b) { return a + b; });
        }
        return m_arrNrOfAvailableBets[ oRound.getNumber() ];
    };
	this.putNrOfAvailableBets = function( arrNrOfAvailableBets ){ m_arrNrOfAvailableBets = arrNrOfAvailableBets; };

	this.getCompetitionSeason = function()
	{
		if ( typeof( m_oCompetitionSeason ) == 'number' )
			m_oCompetitionSeason = VoetbalOog_CompetitionSeason_Factory().createObjectFromDatabase( m_oCompetitionSeason );
		return m_oCompetitionSeason;
	};
	this.putCompetitionSeason = function( oCompetitionSeason ){ m_oCompetitionSeason = oCompetitionSeason; };

	this.getBetConfigs = function( oRound )
	{
		$oBetConfigs = m_oBetConfigs[ oRound.getId() ];
		if ( $oBetConfigs == undefined ) {
			$oBetConfigs = new Array();
			m_oBetConfigs[ oRound.getId() ] = $oBetConfigs;
		}

		return $oBetConfigs;
	};
	this.putBetConfigs = function( oBetConfigs ){ m_oBetConfigs = oBetConfigs; };

	// m_nLastRanking is set in this.getUsers( true )
	this.getLastRanking = function()
	{
		if ( m_nLastRanking == null )
			this.getUsers( true );
		return m_nLastRanking;
	};

	this.getUsers = function( bByRanking )
	{
		if ( bByRanking == true )
		{
			if ( m_arrUsersByRanking == null )
			{
				var oUsers = this.getUsers( false );
				m_arrUsersByRanking = new Array();
				for ( var nI in oUsers )
				{
					if ( !( oUsers.hasOwnProperty( nI ) ) )
						continue;

					m_arrUsersByRanking.push( oUsers[nI] );
				}
				m_arrUsersByRanking.sort(
					function ( oUserA, oUserB )
					{
						if ( ( oUserA.getRanking() > oUserB.getRanking() ) )
							return 1;
						return -1;
					}
				);
				// set ranking
				{
					var oTmpPoolUser = m_arrUsersByRanking.pop();
					m_nLastRanking = oTmpPoolUser.getRanking();
					m_arrUsersByRanking.push( oTmpPoolUser );
				}
			}
			return m_arrUsersByRanking;
		}
		return m_oUsers;
	};
	this.putUsers = function( oUsers ){	m_oUsers = oUsers; };

	this.getPayments = function(){ return m_oPayments; };
	this.putPayments = function( oPayments ){ m_oPayments = oPayments; };

	this.getBetTypes = function( oRound )
	{
		var nBetTypes = 0;
		var oBetConfigs = this.getBetConfigs( oRound );
		for ( var nI in oBetConfigs )
		{
			if ( !( oBetConfigs.hasOwnProperty( nI ) ) )
				continue;

			nBetTypes += oBetConfigs[nI].getBetType();
		}
		return nBetTypes;
	};

	this.getBetTimes = function( oRound )
	{
		var nBetTimes = 0;
		var oBetConfigs = this.getBetConfigs( oRound );
		for ( var nI in oBetConfigs )
		{
			if ( !( oBetConfigs.hasOwnProperty( nI ) ) )
				continue;

			var nBetTime = oBetConfigs[nI].getBetTime();
			if ( ( nBetTimes & nBetTime ) == nBetTime )
				continue;

			nBetTimes += nBetTime;
		}
		return nBetTimes;
	};

	this.getRoundEndDateTime = function( oRound )
	{
		var oDateTime = null;

		var bBeforeStartGame = false;
		var oRoundBetConfigs = this.getBetConfigs( oRound );
		for ( var nI in oRoundBetConfigs )
		{
			if ( !( oRoundBetConfigs.hasOwnProperty( nI ) ) )
				continue;

			var oRoundBetConfig = oRoundBetConfigs[nI];
			if ( oRoundBetConfig.getBetTime() == VoetbalOog_BetTime.nBeforeStartGame )
				bBeforeStartGame = true;
			else
			{
				if ( oDateTime == null || oDateTime < oRoundBetConfig.getDeadLine( null ) )
					oDateTime = oRoundBetConfig.getDeadLine( null );
			}
		}

		if ( bBeforeStartGame == true )
		{
			var oNextRound = oRound.getNext();
			if ( oNextRound != null )
			{
				if ( oDateTime == null || oDateTime < oNextRound.getStartDateTime() )
					oDateTime = oNextRound.getStartDateTime();
			}
			else
			{
				if ( oDateTime == null || oDateTime < oRound.getEndDateTime() )
					oDateTime = oRound.getEndDateTime();
			}
		}

		return oDateTime;
	}
}
Inheritance_Manager.extend( VoetbalOog_Pool, Idable);
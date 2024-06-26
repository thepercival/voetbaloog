function VoetbalOog_Round_BetConfig() 
{
	VoetbalOog_Round_BetConfig.baseConstructor.call( this );
	
	var m_nPoints = null;		// int	
	var m_nBetType = null;		// int
	var m_oBetTime = null;		// VoetbalOog_BetTime_Interface
	var m_oRound = null;  		// Voetbal_Round_Interface
	var m_oPool = null;  		// VoetbalOog_Pool_Interface
	var m_oSameTeams = null;	// Collection
	
	this.getPoints = function(){ return m_nPoints; };
	this.putPoints = function( nPoints ){ m_nPoints = nPoints; };
	
	this.getBetType = function(){ return m_nBetType; };
	this.putBetType = function( nBetType ){ m_nBetType = nBetType; };
	
	this.getBetTime = function(){ return m_oBetTime; };
	this.putBetTime = function( oBetTime ){ m_oBetTime = oBetTime; };
	
	this.getSameTeams = function(){ return m_oSameTeams; };
	this.putSameTeams = function( oSameTeams ){ m_oSameTeams = oSameTeams; };
	
	this.getRound = function()
	{
		if ( typeof( m_oRound ) == 'number' )
			m_oRound = VoetbalOog_Round_Factory().createObjectFromDatabase( m_oRound );
		return m_oRound; 
	};
	this.putRound = function( oRound )
	{ 
		m_oRound = oRound;		
	};
	
	this.getPool = function()
	{ 
		if ( typeof( m_oPool ) == 'number' )
			m_oPool = VoetbalOog_Pool_Factory().createObjectFromDatabase( m_oPool );		
		return m_oPool; 
	};
	this.putPool = function( oPool ){ m_oPool = oPool; };
	
	this.getDeadLine = function ( oGame )
	{
		var oDeadLine = null;
		
		var nBetTime = this.getBetTime();
		if ( nBetTime == VoetbalOog_BetTime.nBeforeStartGame )
			oDeadLine = oGame.getStartDateTime();
		else if ( nBetTime == VoetbalOog_BetTime.nBeforeStartRound )
			oDeadLine = this.getRound().getStartDateTime();
		else if ( nBetTime == VoetbalOog_BetTime.nBeforeStartPreviousRound )
		{
			var oPreviousRound = this.getRound().getPrevious();
			if ( oPreviousRound != null )
				oDeadLine = oPreviousRound.getStartDateTime();
			else
				oDeadLine = this.getRound().getCompetitionSeason().getStartDateTime();
		}
		else if ( nBetTime == VoetbalOog_BetTime.nBeforeCompetitionSeason )
			oDeadLine = this.getRound().getCompetitionSeason().getStartDateTime();
		
		return oDeadLine;
	};
}
Inheritance_Manager.extend( VoetbalOog_Round_BetConfig, Idable );
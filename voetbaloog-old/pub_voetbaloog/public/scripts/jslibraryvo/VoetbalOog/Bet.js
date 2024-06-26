function VoetbalOog_Bet() 
{
	VoetbalOog_Bet.baseConstructor.call( this );
	
	var m_bCorrect = null;
	var m_oPoolUser = null;
	var m_oRoundBetConfig = null;
	
	this.getCorrect = function(){ return m_bCorrect; };
	this.putCorrect = function( bCorrect ){ m_bCorrect = bCorrect; };
	
	this.getPoolUser = function()
	{ 
		if ( typeof m_oPoolUser == 'number' )
			m_oPoolUser = VoetbalOog_Pool_User_Factory().createObjectFromDatabase( m_oPoolUser );
		return m_oPoolUser; 
	};
	this.putPoolUser = function( oPoolUser ){ m_oPoolUser = oPoolUser; };
	
	this.getRoundBetConfig = function()
	{ 
		if ( typeof m_oRoundBetConfig == 'number' )
			m_oRoundBetConfig = VoetbalOog_Round_BetConfig_Factory().createObjectFromDatabase( m_oRoundBetConfig );
		return m_oRoundBetConfig; 
	};
	this.putRoundBetConfig = function( oRoundBetConfig ){ m_oRoundBetConfig = oRoundBetConfig; };
}
Inheritance_Manager.extend( VoetbalOog_Bet, Idable );
function VoetbalOog_Bet_Score() 
{
	VoetbalOog_Bet_Score.baseConstructor.call( this );
	
	var m_oGame = null;
	var m_nHomeGoals = null;
	var m_nAwayGoals = null;
	
	this.getGame = function()
	{
		if ( typeof m_oGame == 'number' )
			m_oGame = VoetbalOog_Game_Factory().createObjectFromDatabase( m_oGame );
		return m_oGame; 
	};
	this.putGame = function( oGame ){ m_oGame = oGame; };
	
	this.getHomeGoals = function(){ return m_nHomeGoals; };
	this.putHomeGoals = function( nHomeGoals ){ m_nHomeGoals = nHomeGoals; };
	
	this.getAwayGoals = function(){ return m_nAwayGoals; };
	this.putAwayGoals = function( nAwayGoals ){ m_nAwayGoals = nAwayGoals; };
	
	// getRound
	// getPoints
}
Inheritance_Manager.extend( VoetbalOog_Bet_Score, VoetbalOog_Bet );

VoetbalOog_Bet_Score.nId = 4;
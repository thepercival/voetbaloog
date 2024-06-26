function VoetbalOog_Bet_Result() 
{
	VoetbalOog_Bet_Result.baseConstructor.call( this );
	
	var m_oGame = null;
	var m_nResult = null;
	
	this.getGame = function()
	{ 
		if ( typeof m_oGame == 'number' )
			m_oGame = VoetbalOog_Game_Factory().createObjectFromDatabase( m_oGame );
		return m_oGame; 
	};
	this.putGame = function( oGame ){ m_oGame = oGame; };
	
	this.getResult = function(){ return m_nResult; };
	this.putResult = function( nResult ){ m_nResult = nResult; };
	
	// getRound
	// getPoints
}
Inheritance_Manager.extend( VoetbalOog_Bet_Result, VoetbalOog_Bet );

VoetbalOog_Bet_Result.nId = 2;
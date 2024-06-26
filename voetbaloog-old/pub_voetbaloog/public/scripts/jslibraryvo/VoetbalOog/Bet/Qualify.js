function VoetbalOog_Bet_Qualify() 
{
	VoetbalOog_Bet_Qualify.baseConstructor.call( this );
	
	var m_oPoulePlace = null;
	var m_oTeam = null;
	
	this.getPoulePlace = function()
	{
		if ( typeof m_oPoulePlace == 'number' )
			m_oPoulePlace = VoetbalOog_PoulePlace_Factory().createObjectFromDatabase( m_oPoulePlace );
		return m_oPoulePlace; 
	};
	this.putPoulePlace = function( oPoulePlace ){ m_oPoulePlace = oPoulePlace; };
	
	this.getTeam = function()
	{ 
		if ( typeof m_oTeam == 'number' )
			m_oTeam = VoetbalOog_Team_Factory().createObjectFromDatabase( m_oTeam );
		return m_oTeam; 
	};
	this.putTeam = function( oTeam ){ m_oTeam = oTeam; };
	
	// getRound
}
Inheritance_Manager.extend( VoetbalOog_Bet_Qualify, VoetbalOog_Bet );

VoetbalOog_Bet_Qualify.nId = 1;
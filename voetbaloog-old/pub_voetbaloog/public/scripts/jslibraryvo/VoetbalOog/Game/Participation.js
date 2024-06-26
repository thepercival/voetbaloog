function VoetbalOog_Game_Participation() 
{
	VoetbalOog_Game_Participation.baseConstructor.call( this );
	
	var m_oGame = null;
	var m_oTeamMembershipPlayer = null;
		
	this.getGame = function()
	{ 
		if ( typeof m_oGame == 'number' )
			m_oGame = VoetbalOog_Game_Factory().createObjectFromDatabase( m_oGame );
		return m_oGame; 
	};
	this.putGame = function( oGame ){ m_oGame = oGame; };
	
	this.getTeamMembershipPlayer = function()
	{ 
		if ( typeof m_oTeamMembershipPlayer == 'number' )
			m_oTeamMembershipPlayer = VoetbalOog_Team_Membership_Player_Factory().createObjectFromDatabase( m_oTeamMembershipPlayer );
		return m_oTeamMembershipPlayer; 
	};
	this.putTeamMembershipPlayer = function( oTeamMembershipPlayer ){ m_oTeamMembershipPlayer = oTeamMembershipPlayer; };
	
	
}
Inheritance_Manager.extend( VoetbalOog_Game_Participation, Agenda_TimeSlot );
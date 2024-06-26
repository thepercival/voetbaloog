function VoetbalOog_Team_Membership_Player() 
{
	VoetbalOog_Team_Membership_Player.baseConstructor.call( this );
	
	var m_nBackNumber = null;
		
	this.getBackNumber = function(){ return m_nBackNumber; };
	this.putBackNumber = function( nBackNumber ){ m_nBackNumber = nBackNumber; };
}
Inheritance_Manager.extend( VoetbalOog_Team_Membership_Player, VoetbalOog_Team_Membership );
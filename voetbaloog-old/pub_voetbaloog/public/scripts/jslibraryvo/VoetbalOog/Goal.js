function VoetbalOog_Goal() 
{
	VoetbalOog_Goal.baseConstructor.call( this );
	
	var m_oGameParticipant = null;
	var m_nMinute = null;
	var m_bOwnGoal = null;
	var m_bPenalty = null;
		
	this.getGameParticipant = function()
	{ 
		if ( typeof m_oGameParticipant == 'number' )
			m_oGameParticipant = VoetbalOog_Game_Participant_Factory().createObjectFromDatabase( m_oGameParticipant );
		return m_oGameParticipant; 
	};
	this.putGameParticipant = function( oGameParticipant ){ m_oGameParticipant = oGameParticipant; };
	
	this.getMinute = function(){ return m_nMinute; };
	this.putMinute = function( nMinute ){ m_nMinute = nMinute; };
	
	this.getOwnGoal = function(){ return m_bOwnGoal; };
	this.putOwnGoal = function( bOwnGoal ){ m_bOwnGoal = bOwnGoal; };
	
	this.getPenalty = function(){ return m_bPenalty; };
	this.putPenalty = function( bPenalty ){ m_bPenalty = bPenalty; };
}
Inheritance_Manager.extend( VoetbalOog_Goal, Idable );
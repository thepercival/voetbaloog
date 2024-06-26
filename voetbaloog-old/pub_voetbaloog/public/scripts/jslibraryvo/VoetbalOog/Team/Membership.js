function VoetbalOog_Team_Membership() 
{
	VoetbalOog_Team_Membership.baseConstructor.call( this );
	
	var m_oClient = null;
	var m_oProvider = null;
		
	this.getClient = function()
	{ 
		if ( typeof m_oClient == 'number' )
			m_oClient = VoetbalOog_Person_Factory().createObjectFromDatabase( m_oClient );
		return m_oClient; 
	};
	this.putClient = function( oClient ){ m_oClient = oClient; };
	
	this.getProvider = function()
	{ 
		if ( typeof m_oProvider == 'number' )
			m_oProvider = VoetbalOog_Team_Factory().createObjectFromDatabase( m_oProvider );
		return m_oProvider; 
	};
	this.putProvider = function( oProvider ){ m_oProvider = oProvider; };
}
Inheritance_Manager.extend( VoetbalOog_Team_Membership, Agenda_TimeSlot );
function Agenda_Week() 
{
	Agenda_Week.baseConstructor.call( this );
	
	var m_sName = null;		// string
	
	this.getName = function(){ return m_sName; };
	this.putName = function( sName ){ m_sName = sName; };
}
Inheritance_Manager.extend(Agenda_Week, Agenda_TimeSlot);
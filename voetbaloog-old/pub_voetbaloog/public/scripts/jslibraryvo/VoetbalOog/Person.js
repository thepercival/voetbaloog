function VoetbalOog_Person() 
{
	VoetbalOog_Person.baseConstructor.call( this );
	
	var m_sFullName = null;
	var m_nNrOfGoalsTmp = null;
	
	this.getFullName = function(){ return m_sFullName; };
	this.putFullName = function( sFullName ){ m_sFullName = sFullName; };
	
	this.getNrOfGoalsTmp = function(){ return m_nNrOfGoalsTmp; };
	this.putNrOfGoalsTmp = function( nNrOfGoalsTmp ){ m_nNrOfGoalsTmp = nNrOfGoalsTmp; };
}
Inheritance_Manager.extend( VoetbalOog_Person, Idable );
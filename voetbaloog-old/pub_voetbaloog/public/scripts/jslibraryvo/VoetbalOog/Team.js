function VoetbalOog_Team() 
{
	VoetbalOog_Team.baseConstructor.call( this );
	
	var m_sName = null;
	var m_sAbbreviation = null;
	var m_sImageName = null;
	
	this.getName = function(){ return m_sName; };
	this.putName = function( sName ){ m_sName = sName; };
	
	this.getAbbreviation = function(){ return m_sAbbreviation; };
	this.putAbbreviation = function( sAbbreviation ){ m_sAbbreviation = sAbbreviation; };
	
	this.getImageName = function(){ return m_sImageName; };
	this.putImageName = function( sImageName ){ m_sImageName = sImageName; };
}
Inheritance_Manager.extend(VoetbalOog_Team, Idable);

VoetbalOog_Team.LINE_KEEPER = 1;
VoetbalOog_Team.LINE_DEFENSE = 2;
VoetbalOog_Team.LINE_MIDFIELD = 4;
VoetbalOog_Team.LINE_ATTACK = 8;
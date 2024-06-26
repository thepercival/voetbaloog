function VoetbalOog_User()
{
	VoetbalOog_User.baseConstructor.call( this );

	var m_sName = null;

	this.getName = function(){ return m_sName; };
	this.putName = function( sName ){ m_sName = sName; };
}
Inheritance_Manager.extend( VoetbalOog_User, Idable);
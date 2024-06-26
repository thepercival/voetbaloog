function VoetbalOog_Pool_Payment() 
{
	VoetbalOog_Pool_User.baseConstructor.call( this );
	
	var m_nPlace = null;
	var m_nTimesStake = null;
	// var m_oPool = null;
	
	this.getPlace = function(){ return m_nPlace; };
	this.putPlace = function( nPlace ){ m_nPlace = nPlace; };
	
	this.getTimesStake = function(){ return m_nTimesStake; };
	this.putTimesStake = function( nTimesStake ){ m_nTimesStake = nTimesStake; };
	
	/*this.getPool = function()
	{
		if ( typeof m_oPool == 'number' )
			m_oPool = VoetbalOog_Pool_Factory().createObjectFromDatabase( m_oPool );
		return m_oPool; 
	};
	this.putPool = function( oPool ){ m_oPool = oPool; };*/
}
Inheritance_Manager.extend( VoetbalOog_Pool_Payment, Idable );
function VoetbalOog_Pool_User() 
{
	VoetbalOog_Pool_User.baseConstructor.call( this );
	
	var m_bAdmin = null;
	var m_bPaid = null;
	var m_nPoints = null;
	var m_nRanking = null;
	var m_oPool = null;
	var m_oUser = null;
	var m_oBets = null;
	
	this.getAdmin = function(){ return m_bAdmin; };
	this.putAdmin = function( bAdmin ){ m_bAdmin = bAdmin; };
	
	this.getPaid = function(){ return m_bPaid; };
	this.putPaid = function( bPaid ){ m_bPaid = bPaid; };
	
	this.getPoints = function(){ return m_nPoints; };
	this.putPoints = function( nPoints ){ m_nPoints = nPoints; };
	
	this.getRanking = function(){ return m_nRanking; };
	this.putRanking = function( nRanking ){ m_nRanking = nRanking; };
	
	this.getPool = function()
	{
		if ( typeof m_oPool == 'number' )
			m_oPool = VoetbalOog_Pool_Factory().createObjectFromDatabase( m_oPool );		
		return m_oPool; 
	};
	this.putPool = function( oPool ){ m_oPool = oPool; };
	
	this.getUser = function()
	{
		if ( typeof m_oUser == 'string' )
			m_oUser = VoetbalOog_User_Factory().createObjectFromDatabase( m_oUser );		
		return m_oUser; 
	};
	this.putUser = function( oUser ){ m_oUser = oUser; };
	
	this.getBets = function( oRoundBetConfig )
	{ 
		var oBets = m_oBets[ oRoundBetConfig.getId() ];
		if ( oBets == undefined )
		{
			oBets = new Object(); 
			m_oBets[ oRoundBetConfig.getId() ] = oBets;
		}
		return oBets; 
	};
	this.putBets = function( oBets ){ m_oBets = oBets; };
}
Inheritance_Manager.extend( VoetbalOog_Pool_User, Idable );
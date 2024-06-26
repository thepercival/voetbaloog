function Idable() 
{	
	var m_vtId = null;
	
	this.getId = function(){ return m_vtId; };
	this.putId = function( vtId ){ m_vtId = vtId; };
}
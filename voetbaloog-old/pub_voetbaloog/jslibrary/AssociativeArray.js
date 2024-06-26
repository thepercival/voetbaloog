function AssociativeArray() 
{		
	var m_nLength = 0;
	this.Items = new Object();
	/*
	for (var i = 0; i < arguments.length; i += 2) {
		if (typeof(arguments[i + 1]) != 'undefined') {
			this.Items[arguments[i]] = arguments[i + 1];
			this.length++;
		}
	}
	*/
   
	this.remove = function( oIdable )
	{
		var vtId = oIdable.getId();
		if ( this.has( oIdable ) == true ) 
		{
			m_nLength--;
			delete this.Items[ vtId ];
		}
	};
	
	this.flush = function()
	{
		for ( var nI in this.Items) 
			delete this.Items[ nI ];
		
		m_nLength = 0;
	};

	this.add = function( oIdable )
	{
		var vtId = oIdable.getId();
		if ( this.has( oIdable ) == false ) 
		{
			m_nLength++;
			this.Items[ vtId ] = oIdable;
		}				
	};

	this.get = function( vtId ) {
		return this.Items[ vtId ];
	};
	
	this.has = function( oIdable )
	{
		return typeof( this.Items[ oIdable.getId() ] ) != 'undefined';
	};
	
	this.count = function()
	{
		return m_nLength;
	};
}

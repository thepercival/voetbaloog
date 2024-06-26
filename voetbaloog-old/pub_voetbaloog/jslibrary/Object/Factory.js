function Object_Factory()
{
	var instance = (function()
	{
		//var m_arrPool = new Array();

		function privateMethod ()
		{
			// ...
		}

		return {
			createObjectsFromJSON: function ( oJSONs )
			{
				var arrObjects = new Array();

				for ( var nI in oJSONs )
				{
					var oJSON = oJSONs[nI];
					if ( oJSON == null )
						continue;
					else if ( typeof ( oJSON ) == 'number' )
						arrObjects[ oJSON ] = this.createObjectFromDatabase( oJSON );
					else
						arrObjects[ oJSON.Id ] = this.createObjectFromJSON( oJSON );
				}

				return arrObjects;
			}
			,
			count: function ( oObject )
			{
				var nCount = 0;
				for ( var p in oObject )
				{
					if ( oObject.hasOwnProperty( p ) )
						++nCount;
				}
				return nCount;
			}
		};
	})();

	Object_Factory = function ()
	{
		// re-define the function for subsequent calls
		return instance;
	};

	return Object_Factory();
}
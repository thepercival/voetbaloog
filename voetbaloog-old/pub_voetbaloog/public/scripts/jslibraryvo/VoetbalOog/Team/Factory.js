function VoetbalOog_Team_Factory()
{
	var instance = (function()
	{
		var m_arrPool = new Array();

		function privateMethod ()
		{
			// ...
		}

		return {
			createObjectsFromJSON: function ( oJSONs )
			{
				var arrObjects = new Object();

				for ( var nI in oJSONs )
				{
					if ( !( oJSONs.hasOwnProperty( nI ) ) )
						continue;

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
			createObjectFromJSON: function ( oJSON )
			{
				if ( oJSON == null )
					return null;

				var sId = oJSON.Id;
				var oObject = m_arrPool[sId];
				if ( oObject == null )
				{
					oObject = new VoetbalOog_Team();
					m_arrPool[sId] = oObject;

					oObject.putId( sId );
					// console.log( oJSON.Name );
					oObject.putName( oJSON.Name );
					oObject.putAbbreviation( oJSON.Abbreviation );
					oObject.putImageName( oJSON.ImageName );
				}
				return oObject;
			}
			,
			getLineDescription: function( nLine )
			{
				if ( nLine == VoetbalOog_Team.LINE_KEEPER )
					return "keeper";
				else if ( nLine == VoetbalOog_Team.LINE_DEFENSE )
					return "verdediging";
				else if ( nLine == VoetbalOog_Team.LINE_MIDFIELD )
					return "middenveld";
				else if ( nLine == VoetbalOog_Team.LINE_ATTACK )
					return "aanval";				
				return null;
			}
			,
			createObjectFromDatabase: function ( nId )
			{
				if ( nId == null )
					return null;

				var oObject = m_arrPool[nId];
				if ( oObject == null )
				{
					alert('create with ajakkes team');
				}

				return oObject;
			}
		};
	})();

	VoetbalOog_Team_Factory = function ()
	{
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Team_Factory();
}
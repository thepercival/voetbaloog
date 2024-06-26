function Agenda_Factory() 
{
	var instance = ( function() 
	{
		var m_arrPool = new Array();
		
		function privateMethod () 
		{
			// ...
		}

		return { 
			// public interface			
			createDay: function ( oDate ) 
			{
				var oStartDate = new Date( oDate );
				var oEndDate = new Date( oDate );
				oEndDate.add( "D", 1 );
				return new Agenda_TimeSlot( oStartDate, oEndDate );
			}
			,
			getDaysOfWeek: function () 
			{
				return new Array("ma", "di", "wo", "do", "vr", "za", "zo");
			}
			,
			createObjectsFromJSON: function ( oJSONs ) 
			{
				var oObjects = new Object();
				
				for ( var nI in oJSONs ) 
				{ 
					var oJSON = oJSONs[nI];
					if ( oJSON == null )
						continue;
					else
						oObjects[ oJSON.Id ] = this.createObjectFromJSON( oJSON );
				}
				
				return oObjects;
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
					oObject = new Agenda_TimeSlot();
					m_arrPool[sId] = oObject;
					
					oObject.putId( sId );
					oObject.putStartDateTime( new Date( oJSON.StartDateTime ) );
					if ( oJSON.EndDateTime != null )
						oObject.putEndDateTime( new Date( oJSON.EndDateTime ) );
				}				
				return oObject;
			}
		};
	} )();

	Agenda_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return Agenda_Factory(); // call the new function
}
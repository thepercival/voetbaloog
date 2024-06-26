function Agenda_Week_Factory()
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
			createObjectsFromJSON: function ( oJSONs )
			{
				var arrObjects = new Array();

				for ( var nI in oJSONs )
				{
					var oJSON = oJSONs[nI];
					if ( oJSON == null )
						continue;
					else if ( typeof( oJSON ) == 'number' )
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
					oObject = new Agenda_Week();
					m_arrPool[sId] = oObject;

					oObject.putId( sId );
					oObject.putName( oJSON.Name );
					oObject.putStartDateTime( new Date( oJSON.StartDateTime ) );
					oObject.putEndDateTime( new Date( oJSON.EndDateTime ) );
				}
				return oObject;
			}
			,
			createObjectFromDatabase: function ( nId )
			{
				if ( nId == null )
					return null;

				// var sId = oJSON.Id;
				var oObject = m_arrPool[nId];
				if ( oObject == null )
				{
					// console.log( nId );
					alert('create with ajakkes agenda_week:' + nId);
				}

				return oObject;
			}
			,
			/*
			Agenda_TimeSlot.nExcludeNone : when not all of oWeek + nDaysOfWeek are in the FreePeriods than return true
			Agenda_TimeSlot.nExcludeBoth : when all of oWeek + nDaysOfWeek are in the FreePeriods than return true
			 */
			isInFreePeriod: function ( oFreePeriods, oWeek, nDaysOfWeek, nRange )
			{
				for ( var nI = 0 ; nI <= 6 ; nI++ )
				{
					var nDayOfWeek = Math.pow( 2, nI );
					if ( ( nDayOfWeek & nDaysOfWeek ) == nDayOfWeek )
					{
						var oDate = new Date( oWeek.getStartDateTime() );
						oDate.add( "D", nI );

						var oDay = Agenda_Factory().createDay( oDate );

						var bInFreePeriod = false;
						for( var nJ in oFreePeriods )
						{
							var oFreePeriod = oFreePeriods[nJ];

							if ( oFreePeriod.overlapses( oDay ) )
							{
								bInFreePeriod = true;
								break;
							}
						}
						if ( bInFreePeriod == false && nRange == Agenda_TimeSlot.nExcludeBoth )
							return false;
						if ( bInFreePeriod == true && nRange == Agenda_TimeSlot.nExcludeNone )
							return true;
					}
				}

				if ( nRange == Agenda_TimeSlot.nExcludeBoth )
					return true;
				if ( nRange == Agenda_TimeSlot.nExcludeNone )
					return false;
			}
		};
	} )();

	Agenda_Week_Factory = function ()
	{
		// re-define the function for subsequent calls
		return instance;
	};

	return Agenda_Week_Factory(); // call the new function
}
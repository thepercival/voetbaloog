function VoetbalOog_PoulePlace_Factory() 
{
	var instance = (function() 
	{
		var m_arrPool = new Array();

		function privateMethod () 
		{
			// ...
		}

		return {
            createArrayFromJSON: function ( oJSONs )
            {
                var arrObjects = new Array();

                for ( var nI in oJSONs )
                {
                    if ( !( oJSONs.hasOwnProperty( nI ) ) )
                        continue;

                    var oJSON = oJSONs[nI];
                    if ( oJSON == null )
                        continue;

                    var oObject = null;
                    if ( typeof ( oJSON ) == 'number' )
                        oObject = this.createObjectFromDatabase( oJSON );
                    else
                        oObject = this.createObjectFromJSON( oJSON );
                    arrObjects.push( oObject );
                }
                return arrObjects;
            }
            ,
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
					oObject = new VoetbalOog_PoulePlace();
					m_arrPool[sId] = oObject;
					
					oObject.putId( sId );
					oObject.putNumber( oJSON.Number );
					oObject.putPenaltyPoints( oJSON.PenaltyPoints );
					if ( typeof( oJSON.Poule ) == 'number' )
						oObject.putPoule( oJSON.Poule );
					else
						oObject.putPoule( VoetbalOog_Poule_Factory().createObjectFromJSON( oJSON.Poule ) );
					if ( typeof( oJSON.Team ) == 'number' )
						oObject.putTeam( oJSON.Team );
					else
						oObject.putTeam( VoetbalOog_Team_Factory().createObjectFromJSON( oJSON.Team ) );
					if ( typeof( oJSON.FromQualifyRule ) == 'number' )
						oObject.putFromQualifyRule( oJSON.FromQualifyRule );
					else
						oObject.putFromQualifyRule( VoetbalOog_QualifyRule_Factory().createObjectFromJSON( oJSON.FromQualifyRule ) );
					if ( typeof( oJSON.ToQualifyRule ) == 'number' )
						oObject.putToQualifyRule( oJSON.ToQualifyRule );
					else
						oObject.putToQualifyRule( VoetbalOog_QualifyRule_Factory().createObjectFromJSON( oJSON.ToQualifyRule ) );


				}				
				return oObject;
			}	
			,
			createObjectFromDatabase: function ( nId ) 
			{
				if ( nId == null )
					return null;
				
				var oObject = m_arrPool[nId];
				if ( oObject == null )
				{
					alert('create with ajakkes pouleplace'); // See CompetitionSeasonFactory
				}				
				
				return oObject;
			}
			,
			getName: function ( oPoulePlace )
			{
				if ( oPoulePlace.getTeam() != undefined )
					return oPoulePlace.getTeam().getName();

				var oFromQualifyRule = oPoulePlace.getFromQualifyRule();

				if ( oFromQualifyRule == undefined )
                    return ( oPoulePlace.getNumber() + 1 );

                var arrFromPoulePlaces = oFromQualifyRule.getFromPoulePlaces();

                var sPoulePlaceName = '';
                if ( !oFromQualifyRule.isSingle() ){

                    // console.log( oFromQualifyRule.getConfig() );
                    var nIndex = oFromQualifyRule.getToPoulePlaceIndex( oPoulePlace );
                    // console.log( nIndex );
                    var nPouleNumbersPow = oFromQualifyRule.getConfig().display[ nIndex ];
                    var nPouleNumber = 0;
                    while( Math.pow( 2, nPouleNumber ) <= nPouleNumbersPow )
                    {
                        var nPouleNrPow = Math.pow( 2, nPouleNumber );
                        if ( ( nPouleNrPow & nPouleNumbersPow ) == nPouleNrPow ) {
                            sPoulePlaceName += String.fromCharCode(65 + nPouleNumber);
                        }
						nPouleNumber++;
                    }
                    /*if ( arrFromPoulePlaces.length > 3 )
                     {
                     var oFirst = null; var oLast = null;
                     for ( var nI in oFromPoulePlaces ) {
                     if (!( oFromPoulePlaces.hasOwnProperty(nI) ))
                     continue;
                     var oFromPoulePlace = oFromPoulePlaces[nI];
                     if ( oFirst == null ) { oFirst = oFromPoulePlace; }
                     oLast = oFromPoulePlace;
                     }
                     sPoulePlaceName += VoetbalOog_Poule_Factory().getName( oFirst.getPoule(), false );
                     sPoulePlaceName += "..";
                     sPoulePlaceName += VoetbalOog_Poule_Factory().getName( oLast.getPoule(), false );
                     }*/
                }
                else {
					for ( var nI = 0 ; nI < arrFromPoulePlaces.length ; nI++ ) {
						sPoulePlaceName += VoetbalOog_Poule_Factory().getName( arrFromPoulePlaces[nI].getPoule(), false );
					}
                }

                // add number
                if ( arrFromPoulePlaces.length > 0 ) {
					sPoulePlaceName += ( arrFromPoulePlaces[0].getNumber() + 1 );
                }


				return sPoulePlaceName;
			}
		};
	})();

	VoetbalOog_PoulePlace_Factory = function () 
	{ 
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_PoulePlace_Factory();
}
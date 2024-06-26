function VoetbalOog_QualifyRule_Factory()
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
                    {
                        continue;
                    }
                    else if ( typeof ( oJSON ) == 'number' )
                    {
                        arrObjects[ oJSON ] = this.createObjectFromDatabase( oJSON );
                        //alert('roundnr:'+oJSON);
                    }
                    else
                    {
                        arrObjects[ oJSON.Id ] = this.createObjectFromJSON( oJSON );
                        //alert('round:'+oJSON.Id);
                    }
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
                    oObject = new VoetbalOog_QualifyRule();
                    m_arrPool[sId] = oObject;

                    oObject.putId( sId );
                    oObject.putConfigNr( oJSON.ConfigNr );
                    oObject.putConfig( oJSON.Config );
                    oObject.putFromPoulePlaces( VoetbalOog_PoulePlace_Factory().createArrayFromJSON( oJSON.FromPoulePlaces ) );
                    oObject.putToPoulePlaces( VoetbalOog_PoulePlace_Factory().createArrayFromJSON( oJSON.ToPoulePlaces ) );
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
                    alert('create with ajakkes qualifyrule'); // See CompetitionSeasonFactory
                }

                return oObject;
            }
        };
    })();

    VoetbalOog_QualifyRule_Factory = function ()
    {
        // re-define the function for subsequent calls
        return instance;
    };

    return VoetbalOog_QualifyRule_Factory();
}
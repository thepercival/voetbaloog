function VoetbalOog_QualifyRule()
{
    // this.Idable();
    VoetbalOog_QualifyRule.baseConstructor.call( this );

    var m_nConfigNr = null;
    var m_oConfig = null;
    var m_arrFromPoulePlaces = null;
    var m_arrToPoulePlaces = null;
    var m_oFromPoules = null;
    var m_oFromRound = null;
    var m_oToRound = null;

    this.getConfigNr = function(){ return m_nConfigNr; };
    this.putConfigNr = function( nConfigNr ){ m_nConfigNr = nConfigNr; };

    this.getConfig = function(){ return m_oConfig; };
    this.putConfig = function( oConfig ){ m_oConfig = oConfig; };

    this.getFromPoulePlaces = function(){ return m_arrFromPoulePlaces; };
    this.putFromPoulePlaces = function( oFromPoulePlaces ){ m_arrFromPoulePlaces = oFromPoulePlaces; };

    this.getToPoulePlaces = function(){ return m_arrToPoulePlaces; };
    this.putToPoulePlaces = function( oToPoulePlaces ){ m_arrToPoulePlaces = oToPoulePlaces; };

    this.getFromPoules = function()
    {
        if (m_oFromPoules == null)
        {
            m_oFromPoules = new Object();
            var arrFromPoulePlaces = this.getFromPoulePlaces();
            for ( var nI = 0 ; nI < arrFromPoulePlaces.length ; nI++ ) {
                var oFromPoule = arrFromPoulePlaces[nI].getPoule();
                m_oFromPoules[ oFromPoule.getId() ] = oFromPoule;
            }
        }
        return m_oFromPoules;
    };

    this.getFromRound = function()
    {
        if (m_oFromRound == null)
        {
            var arrFromPoulePlaces = this.getFromPoulePlaces();
            if ( arrFromPoulePlaces.length > 0 )
                m_oFromRound = arrFromPoulePlaces[0].getPoule().getRound();
        }
        return m_oFromRound;
    };
    this.getToRound = function()
    {
        if (m_oToRound == null)
        {
            var arrToPoulePlaces = this.getToPoulePlaces();
            if ( arrToPoulePlaces.length > 0 )
                m_oToRound = arrToPoulePlaces[0].getPoule().getRound();
        }
        return m_oToRound;
    };

    this.getFromPoulesByPoulePlace = function( oToPoulePlace )
    {
        // @TODO CHECKEN ALS DEZE FUNCTIE OOK VOOR SINGLE QUALIFYRULE WERKT
        var nConfigPoulesFilter = 0;
        {
            var oConfig = this.getConfig();
            if ( oConfig != null && this.getConfigNr() > 0) {
                nConfigPoulesFilter = oConfig.display[this.getToPoulePlaceIndex(oToPoulePlace)];
            }
        }

        var oFromPoules = new Object();
        var arrFromPoulePlaces = this.getFromPoulePlaces();
        for ( var nI = 0 ; nI < arrFromPoulePlaces.length ; nI++ ) {
            var oFromPoulePlace = arrFromPoulePlaces[nI];
            var oFromPoule = oFromPoulePlace.getPoule();
            var nPouleNumberPow = Math.pow( 2, oFromPoule.getNumber() );
            if ( nConfigPoulesFilter == 0 || ( nConfigPoulesFilter & nPouleNumberPow ) == nPouleNumberPow ) {
                oFromPoules[oFromPoule.getId()] = oFromPoule;
            }
        }
        return oFromPoules;
    };

    this.getToPoulePlaceIndex = function( oToPoulePlace )
    {
        var arroPoulePlaces = this.getToPoulePlaces();
        return arroPoulePlaces.indexOf( oToPoulePlace );
    }

    this.isSingle = function()
    {
        return ( this.getFromPoulePlaces().length == 1 && this.getToPoulePlaces().length == 1 );
    }
}
Inheritance_Manager.extend(VoetbalOog_QualifyRule, Idable);
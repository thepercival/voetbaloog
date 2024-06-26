/**
 * Created by coen on 11-12-15.
 */

appVoetbal.value('noUiSliderConfig', {
    step: 1
});

appVoetbal.controller( "TeamController", [ '$http', 'csFactory', 'teamFactory', 'nameFactory', function( $http, csFactory, teamFactory, nameFactory) {

    this.csid = null;
    this.competitionseason = null;
    this.teams = [];
    this.firstRound = null;
    this.savestate = { type: null, message: null };
    var that = this;
    this.inputtypeselect = null;;

    this.roundtype_poule = 1;
    this.roundtype_knockout = 2;
    this.roundtype_winner = 4;

    function appendTransform(defaults, transform) {
        defaults = angular.isArray(defaults) ? defaults : [defaults];
        defaults.unshift(transform);
        return defaults;
    }

    angular.element(document).ready(function () {

        var nDataflag = g_jsonVoetbal.nCompetitionSeason_Rounds + g_jsonVoetbal.nRound_Poules;
        if ( that.inputtypeselect == true ) { nDataflag += g_jsonVoetbal.nAssociation_Teams; }
        csFactory.getCompetitionSeason.query( {'dataflag':nDataflag}, {'id': that.csid })
            .$promise.then(function( response ) {
            that.competitionseason = response.data;
            // console.log( that.competitionseason );
            that.firstRound = that.competitionseason.rounds[0];
            //  this.editable = false;

            var associationId = that.competitionseason.association ? that.competitionseason.association.id : null;
            teamFactory.getTeams.query( {'dataflag':nDataflag}, {'associationid': associationId })
                .$promise.then(function( response ) {
                that.teams = response.data.sort(
                    function ( teamA, teamB )
                    {
                        if ( ( teamA.name > teamB.name ) )
                            return 1;
                        return -1;
                    }
                );;
                // console.log( that.teams );
                // that.firstRound = that.competitionseason.rounds[0];
                //  this.editable = false;

            });
        });
    });

    this.getNrOfPoulePlaces = function( round ){
        var nrOfPoulePlaces = 0;
        for ( var nPouleNr = 0; nPouleNr < round.poules.length; nPouleNr++) {
            nrOfPoulePlaces += round.poules[nPouleNr].places.length;
        }
        return nrOfPoulePlaces;
    };

    this.getPouleName = function( poule, bWithPrefix ) {
        return nameFactory.getPoule( poule, bWithPrefix );
    };

    this.togglePoulePlaceState = function( pouleplace ) {
        pouleplace.editable = pouleplace.editable ? false : true;
    };
	
    this.getPoulePlaceClassName = function( pouleplace ){

        if ( pouleplace.toqualifyrule != undefined ){
            if( pouleplace.toqualifyrule.frompouleplaces.length == 1 ) {
                return 'label-success';
            }
            return 'label-warning';
        }
        return null;
    };

    this.processTeamsForm = function() {
        this.savestate = null;
        $http({
            method  : 'POST',
            url     : g_sPubMap + 'voetbal/api/competitionseason/?subaction=saveteams',
            data    : this.stringify( this.firstRound ),  // pass in data as strings
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
        })
        .success(function(data) {
            console.log(data);
            if (data.code == 0 ) {
                // console.log( data.data );
                that.savestate = { type: 'success', message: 'teams opgeslagen' };
                // success
            } else {
                // if successful, bind success message to message
                that.savestate = { type: 'danger', message: data.message };
            }
        })
        .error(function(data, status, headers, config) {
            console.error('Repos error', status, data, headers, config);
        });
    };

    this.stringify = function( object ) {
        return nameFactory.stringify( object );
    };
}]);

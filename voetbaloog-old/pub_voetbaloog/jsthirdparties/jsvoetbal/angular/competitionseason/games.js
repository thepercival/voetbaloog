/**
 * Created by coen on 11-12-15.
 */

appVoetbal.value('noUiSliderConfig', {
    step: 1
});

appVoetbal.controller( "GameController", [ '$http', 'csFactory', 'nameFactory', function( $http, csFactory, nameFactory ) {

    this.csid = null;
    this.competitionseason = null;
    var that = this;
    this.allocateIsCollapsed = true;
    this.cssettingsIsCollapsed = true;
    this.csStartDateTime = new Date();
    this.plansavestate = null;

    this.sortType     = 'startdatetime'; // set the default sort type
    this.sortReverse  = false;  // set the default sort order
    this.searchGame   = '';     // set the default search/filter term

    this.roundtype_poule = 1;
    this.roundtype_knockout = 2;
    this.roundtype_winner = 4;

    /* -------------------------------- */
    this.dateOptions = {
        formatYear: 'yyyy',
        /*minDate: new Date(2016, 1, 22),*/
        startingDay: 1
    };
    this.openCSStartDateTime = function() { this.datePopup.opened = true; };
    this.clearCSStartDateTime = function() { this.csStartDateTime = null; };
    this.setCSStartDateTime = function(y, m, d) { this.csStartDateTime = new Date( y, m, d); };
    this.dateFormat = 'd MMM yyyy';
    this.format = 'd MMM yyyy';
    this.datePopup = { opened: false };
    /* ------------------------------------------------------ */

    angular.element(document).ready(function () {

        var nDataflag = g_jsonVoetbal.nCompetitionSeason_Rounds + g_jsonVoetbal.nRound_Poules;
        csFactory.getCompetitionSeason.query( {'dataflag':nDataflag}, {'id': that.csid })
        .$promise.then(function( response ) {
            that.competitionseason = response.data;
            // console.log( that.competitionseason );
        });
    });

    this.hasGames = function( round ) {
        for ( var nI = 0 ; nI < round.poules.length ; nI++ ) {
            var poule = round.poules[nI];
            if ( poule.games != undefined && poule.games.length > 0 ) { return true; }
        }
        return false;
    };

    this.hasPlayedGames = function( round ) {
        for ( var nI = 0 ; nI < round.poules.length ; nI++ ) {
            var poule = round.poules[nI];
            if ( poule.games == undefined )
                continue;

            for ( var nJ = 0 ; nJ < poule.games.length ; nJ++ ) {
                if ( poule.games[nJ].state == g_jsonVoetbal.nState_Played )
                    return true;
            }
        }
        return false;
    };

    this.getResult = function( game ){
        if ( game.state !== g_jsonVoetbal.nState_Played )
            return null;

        if ( game.homegoalspenalty > -1 && game.awaygoalspenalty > -1 )
            return game.homegoalspenalty + " - " + game.awaygoalspenalty;
        if ( game.homegoalsextratime > -1 && game.awaygoalsextratime > -1 )
            return game.homegoalsextratime + " - " + game.awaygoalsextratime;
        if ( game.homegoals > -1 && game.awaygoals > -1 )
            return game.homegoals + " - " + game.awaygoals;

        return null;
    };

    var match = function (item, val) {
        var regex = new RegExp(val, 'i');
        return item.year.toString().search(regex) == 0 ||
            item.make.search(regex) == 0 ||
            item.model.search(regex) == 0;
    };

    this.filterGames = function( game ) {
        // No filter, so return everything
        // console.log(game);
        return true;
        if (!$scope.q) return true;
        var matched = true;

        // Otherwise apply your matching logic
        $scope.q.split(' ').forEach(function(token) {
            matched = matched && match(car, token);
        });

        return matched;
    };

    this.planGames = function() {
        this.plansavestate = null;
        $http({
            method  : 'POST',
            url     : g_sPubMap + 'voetbal/api/competitionseason/?subaction=savegames',
            data    : { "startdatetime" : this.csStartDateTime, "csid" : this.competitionseason.id },
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
        })
        .success(function(data) {
            console.log(data);
            if (data.code == 0 ) {
                // console.log( data.data );
                that.plansavestate = { type: 'success', message: 'wedstrijden zijn gepland' };
                // success
            } else {
                // if successful, bind success message to message
                that.plansavestate = { type: 'danger', message: data.message };
            }
        })
        .error(function(data, status, headers, config) {
            console.error('Repos error', status, data, headers, config);
        });
    };

    this.updateSettingsCS = function() {
        this.plansavestate = null;
        $http({
            method  : 'POST',
            url     : g_sPubMap + 'voetbal/api/competitionseason/?subaction=saveproperties',
            data    : { "public" : this.competitionseason.public, "csid" : this.competitionseason.id },
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
        })
            .success(function(data) {
                console.log(data);
                if (data.code == 0 ) {
                    // console.log( data.data );
                    that.plansavestate = { type: 'success', message: 'ge(de)publiceerd' };
                    // success
                } else {
                    // if successful, bind success message to message
                    that.plansavestate = { type: 'danger', message: data.message };
                }
            })
            .error(function(data, status, headers, config) {
                console.error('Repos error', status, data, headers, config);
            });
    };


    this.getRoundName = function( round ) {
        return nameFactory.getRound( round );
    };

    this.getPouleName = function( poule, bWithPrefix ) {
        return nameFactory.getPoule( poule, bWithPrefix );
    };

    this.getPoulePlaceName = function( pouleplace ){
        return nameFactory.getPoulePlace( pouleplace );
    };

    this.stringify = function() {
        return nameFactory.stringify( this.competitionseason );
    }
}]);

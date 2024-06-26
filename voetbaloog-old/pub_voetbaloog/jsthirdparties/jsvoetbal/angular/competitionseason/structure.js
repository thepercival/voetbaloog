/**
 * Created by cdunnink on 11-12-2015.
 */

appVoetbal.value('noUiSliderConfig', {
    step: 1
});

appVoetbal.controller( "CompSeasonCtrl", [ '$scope', '$http', 'csFactory', 'nameFactory', function($scope, $http, csFactory, nameFactory) {

    var that = this;
    this.csid = null;
    this.competitionseason = null;
    this.nrofteams = 6;
    this.minNrOfTeams = 2;
    this.maxNrOfTeams = 16;
    this.nrofqualifyers = 0;
    this.savestate = { type: null, message: null };

    this.roundtype_poule = 1;
    this.roundtype_knockout = 2;
    this.roundtype_winner = 4;

    // switch fromqualifyrule and switch pouleplace within, watch out to execute calls in right order, CDK
    this.onDrop = function(target, source){
        var round = that.competitionseason.rounds[ source.rnumber ];

        var pouleSource = round.poules[ source.pnumber ];
        var pouleplaceSource = pouleSource.places[ source.ppnumber ];

        var pouleTarget = round.poules[ target.pnumber ];
        var pouleplaceTarget = pouleTarget.places[ target.ppnumber ];

        var fromqualifyruleSource = pouleplaceSource.fromqualifyrule;
        var fromqualifyruleTarget = pouleplaceTarget.fromqualifyrule;

        var index = fromqualifyruleSource.topouleplaces.indexOf( pouleplaceSource );
        if (index !== -1) {
            fromqualifyruleSource.topouleplaces[index] = pouleplaceTarget;
            pouleplaceTarget.fromqualifyrule = fromqualifyruleSource;
        }

        index = fromqualifyruleTarget.topouleplaces.indexOf( pouleplaceTarget );
        if (index !== -1) {
            fromqualifyruleTarget.topouleplaces[index] = pouleplaceSource;
            pouleplaceSource.fromqualifyrule = fromqualifyruleTarget;
        }
    };

    this.dropValidate = function(target, source) {
        return ( target.rnumber != source.rnumber || target.pnumber != source.pnumber || target.ppnumber != source.ppnumber );
    };

    this.defaultNrOfTeamsPerPoule = {
        2 : { "nrofpoules" : 1 },
        3 : { "nrofpoules" : 1 },
        4 : { "nrofpoules" : 1 },
        5 : { "nrofpoules" : 1 },
        6 : { "nrofpoules" : 2 },
        7 : { "nrofpoules" : 1 },
        8 : { "nrofpoules" : 2 },
        9 : { "nrofpoules" : 2 },
        10 : { "nrofpoules" : 2 },
        11 : { "nrofpoules" : 2 },
        12 : { "nrofpoules" : 3 },
        13 : { "nrofpoules" : 3 },
        14 : { "nrofpoules" : 3 },
        15 : { "nrofpoules" : 3 },
        16 : { "nrofpoules" : 4 }
    };

    angular.element(document).ready(function () {

        var nDataflag = g_jsonVoetbal.nCompetitionSeason_Rounds + g_jsonVoetbal.nRound_Poules;
        csFactory.getCompetitionSeason.query( {'dataflag':nDataflag}, {'id': that.csid })
            .$promise.then(function( response ) {
            that.competitionseason = response.data;
            console.log( that.competitionseason );

            for (var nI = 0; nI < that.competitionseason.rounds.length; nI++) {
                var round = that.competitionseason.rounds[nI];
                that.addUIToRound( round );
                round.nrofqualifiersoptions.range.max = that.getNrOfPoulePlaces( round );
                round.nrofqualifiersoptions.start = that.getNrOfToPoulePlaces( round );
            }
        });
    });

    /**
     *
     * @param nNrOfTeams
     * @param nRoundType
     * @returns {{number: number, poules: Array}}
     */
    this.addRound = function( nNrOfTeams, nRoundType, bSemiCompetition, bCascade ) {

        var nRoundNumber = this.competitionseason.rounds.length;

        if ( nRoundType == undefined ) {

            // als rondenummer is 0 of aantal teams is geen macht van 2
            if ( nRoundNumber == 0 || !( ( nNrOfTeams & ( nNrOfTeams - 1) ) == 0 ) )
                nRoundType = this.roundtype_poule;
            else
                nRoundType = this.roundtype_knockout;
        }

        var nNrOfPoules = 1;
        if ( nRoundType == this.roundtype_poule ) {
            if ( this.defaultNrOfTeamsPerPoule[ nNrOfTeams] != undefined )
                nNrOfPoules = this.defaultNrOfTeamsPerPoule[ nNrOfTeams].nrofpoules;
        }
        else {
            nNrOfPoules = Math.ceil( nNrOfTeams / 2 );
        }

        var round = {
            class: "round",
            number: nRoundNumber,
            poules : [],
            type : nRoundType,
            semicompetition : bSemiCompetition,
            fromqualifyrules : [],
            toqualifyrules : [],
            competitionseason : this.competitionseason
        };
        this.addUIToRound( round );

        var nNrOfTeamsPerPoule = parseInt( Math.floor( nNrOfTeams / nNrOfPoules ), 10 );
        var nNrOfExtraTeams = nNrOfTeams % nNrOfPoules;
        for( var nI = 0 ; nI < nNrOfPoules ; nI++ ){
            var nNrOfTeamsPerPouleTmp = nNrOfTeamsPerPoule;
            if ( nNrOfExtraTeams > 0 ) {
                nNrOfTeamsPerPouleTmp++;
                nNrOfExtraTeams--;
            }
            this.addPoule( round, nNrOfTeamsPerPouleTmp );
        }
        this.competitionseason.rounds.push( round );

        if ( bCascade == true ){
            if ( round.type == this.roundtype_knockout && nNrOfTeams > 1 ){
                if ( ( nNrOfTeams % 2 ) == 1 ) {
                    nNrOfTeams--;
                    nNrOfTeams /= 2;
                    nNrOfTeams++;
                }
                else {
                    nNrOfTeams /= 2;
                }
                this.refreshAtSliding( round, nNrOfTeams );
            }
        }

        return round;
    };
    this.addUIToRound = function( round )
    {
        round.nrofqualifiersoptions = {
            start: 0,
            connect: 'lower',
            orientation : 'vertical',
            step: 1,
            range: {min: 0, max: 0}
        };
        round.nrofqualifiersevents = {
            update: function( params ) {
                var unencoded = params[2];
                if ( round.nrofqualifiersoptions.start /* old */ !=  unencoded )
                    that.refreshAtSliding( round, unencoded );

            }
        }
    };

    this.removeRound = function( arrRounds ) {
        var removedRound = arrRounds.pop();
        while( removedRound.fromqualifyrules.length > 0 ){
            var qualifyRule = removedRound.fromqualifyrules.pop();
            qualifyRule = null;
        }

        var lastRound = arrRounds[ arrRounds.length - 1 ];
        lastRound.toqualifyrules = new Array();
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

    this.updateRoundType = function( round ) {
        // verwijder alle ronden tot en met de huidige
        while ( this.competitionseason.rounds[ round.number ] != undefined ) {
            this.removeRound( this.competitionseason.rounds );
        }

        // remove all poules and add them again by k.o.
        var nNrOfPoulePlaces = createPoulePlacesZigZag( round ).length;
        var nRoundType = round.type;

        this.addRound( nNrOfPoulePlaces, nRoundType, false, true );
    };

    this.getNrOfPoulePlaces = function( round ){
        var nrOfPoulePlaces = 0;
        for ( var nPouleNr = 0; nPouleNr < round.poules.length; nPouleNr++) {
            nrOfPoulePlaces += round.poules[nPouleNr].places.length;
        }
        return nrOfPoulePlaces;
    }

    this.getNrOfToPoulePlaces = function( round ){
        var nrOfToPoulePlaces = 0;
        for ( var nI = 0 ; nI < round.toqualifyrules.length ; nI++ ) {
            nrOfToPoulePlaces += round.toqualifyrules[nI].frompouleplaces.length;
        }
        return nrOfToPoulePlaces;
    }

    this.addPoule = function( round, nrofplaces ) {
        // get nrofplaces from last poule
        if ( nrofplaces == undefined ){
            nrofplaces = 4;
            if (round.poules.length > 0 ){
                var pouletmp = round.poules.pop();
                nrofplaces = pouletmp.places.length;
                round.poules.push(pouletmp);
            }
        }

        var poule = { class: "poule",number: round.poules.length, places: [], round: round };
        round.poules.push( poule );
        for( var nJ = 0 ; nJ < nrofplaces ; nJ++ ) {
            this.addPoulePlace( poule );
        }
        return poule;
    };
    /**
     * should only be called if there are no round after this round
     * @param round
     * @param poule
     */
    this.popPoule = function( round, poule ) {
        if ( poule == undefined ){
            return round.poules.pop();
        }
        var nIndexToRemove = round.poules.indexOf( poule );
        round.poules.splice( nIndexToRemove, 1 );
        // number poules again
        for (var i = 0; i < round.poules.length ; i++) {
            round.poules[i].number = i;
        }
    };
    this.addPoulePlace = function( poule ) {
        var pouleplace = {
            class: "pouleplace",
            number : poule.places.length,
            fromqualifyrule : null,
            toqualifyrule : null,
            poule : poule,
        };
        poule.places.push( pouleplace );
        this.updateNrOfQualifiersOptions( poule );
        return pouleplace;
    };
    this.updateNrOfQualifiersOptions = function( poule ) {
        var nNrOfRoundPlaces = this.getNrOfPoulePlaces( poule.round );

        // console.log( " addPoulePlace " + nNrOfRoundPlaces + " for roundnr " + poule.round.number + " ( poule.round.nrofqualifiers : " + poule.round.nrofqualifiers + " )");
        if ( nNrOfRoundPlaces > poule.round.nrofqualifiersoptions.range.max  ){
            poule.round.nrofqualifiersoptions.range = { min : poule.round.nrofqualifiersoptions.range.min, max: nNrOfRoundPlaces };
        }
    };

    this.popPoulePlace = function( poule ) {
        poule.places.pop();
        var nNrOfRoundPlaces = this.getNrOfPoulePlaces( poule.round );
        // console.log( " popPoulePlace " + nNrOfRoundPlaces + " for roundnr " + poule.round.number + " ( poule.round.nrofqualifiers : " + poule.round.nrofqualifiers + " )");
        if ( nNrOfRoundPlaces < poule.round.nrofqualifiersoptions.range.max  ){
            poule.round.nrofqualifiersoptions.range = { min : poule.round.nrofqualifiersoptions.range.min, max: nNrOfRoundPlaces };
            // console.log( 'poule.round.nrofqualifiersoptions.start: ' + poule.round.nrofqualifiersoptions.start );
            if ( poule.round.nrofqualifiersoptions.start > nNrOfRoundPlaces ){
                poule.round.nrofqualifiersoptions.start = nNrOfRoundPlaces;
            }
            // console.log( 'poule.round.nrofqualifiers : ' + poule.round.nrofqualifiers );
            // console.log( 'updating qualifiers to ' + poule.round.nrofqualifiers + ' ...' );
            this.refreshAtSliding( poule.round, nNrOfRoundPlaces );
        }
        if ( poule.places.length == 0 ) {
            this.popPoule( poule.round, poule );
        }
    };
    /**
     * 1 remove all next rounds
     * 2 add new round
     * 3 update QualifyRules
     *
     * @param round
     * @param newnrofqualifyers
     */
    this.refreshAtSliding = function( round, newnrofqualifyers ){

        var nNrOfTeams = newnrofqualifyers;

        // verwijder alle ronden tot de huidige
        while ( this.competitionseason.rounds[ round.number + 1 ] != undefined ) {
            this.removeRound( this.competitionseason.rounds );
        }
        var nextRound = this.addRound( nNrOfTeams, null, false, true );

        updateQualifyRules( round, nextRound, newnrofqualifyers );

        // needs to be done after updateQualifyRules, because else current round is not refreshed
        if ( nNrOfTeams == 0 )
            this.removeRound( this.competitionseason.rounds );
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

    function updateQualifyRules( fromRound, toRound, newnrofqualifyers )
    {
        var roundpouleplacespernumber = createPoulePlacesPerNumber( fromRound );
        var nextroundpouleplaceszigzag = createPoulePlacesZigZag( toRound );
        for ( var nI = 0 ; nI < roundpouleplacespernumber.length ; nI++ ){

            if ( newnrofqualifyers >= roundpouleplacespernumber[nI].length )
            {
                var tmp = roundpouleplacespernumber[ nI ];
                for ( var nJ = 0 ; nJ < tmp.length ; nJ++ ){
                    var nextroundpouleplace = nextroundpouleplaceszigzag.shift();
                    addQualifyRule( fromRound, toRound, [ tmp[nJ] ], [ nextroundpouleplace ] );
                }
                newnrofqualifyers -= tmp.length;
            }
            else if ( newnrofqualifyers > 0 ) {
                var tmp = roundpouleplacespernumber[ nI ];
                var nexttmp = [];
                for ( var nJ = 0 ; nJ < tmp.length ; nJ++ ) {
                    var nextroundpouleplacezigzag = nextroundpouleplaceszigzag.shift();
                    if ( nextroundpouleplacezigzag != undefined )
                        nexttmp.push( nextroundpouleplacezigzag );
                }
                // now tmp and nexttmp are filled correct
                addQualifyRule( fromRound, toRound, tmp, nexttmp );
                newnrofqualifyers = 0;
            }
            else {
                var tmp = roundpouleplacespernumber[ nI ];
                for ( var nJ = 0 ; nJ < tmp.length ; nJ++ ){
                    tmp[nJ].toqualifyrule = null;
                }
            }
        }
    }

    function addQualifyRule( fromRound, toRound, arrFromPoulePlaces, arrToPoulePlaces )
    {
        var nConfigNr = 0;
        if ( arrFromPoulePlaces.length > 1 )
            nConfigNr = 1;
        // console.log( 'nroffrompp: ' + arrFromPoulePlaces.length + ', nroftopp: ' + arrToPoulePlaces.length );

        var qualifyrule = {
            class: "ppqualifyrule",
            frompouleplaces : arrFromPoulePlaces,
            topouleplaces : arrToPoulePlaces,
            confignr : nConfigNr
        };

        for ( var nI = 0 ; nI < arrFromPoulePlaces.length ; nI++ ){
            arrFromPoulePlaces[nI].toqualifyrule = qualifyrule;
        }

        for ( nI = 0 ; nI < arrToPoulePlaces.length ; nI++ ){
            arrToPoulePlaces[nI].fromqualifyrule = qualifyrule;
        }

        fromRound.toqualifyrules.push( qualifyrule );
        toRound.fromqualifyrules.push( qualifyrule );
    }

    function createPoulePlacesPerNumber( round ) {
        // reset next and reorder pouleplaces
        var pouleplacespernumber = [];
        {
            var bValidPoulePlaces = true;
            var nPoulePlaceNumber = 0;
            while (bValidPoulePlaces) {
                bValidPoulePlaces = false;
                for (var nPouleNumber = 0; nPouleNumber < round.poules.length; nPouleNumber++) {
                    if (round.poules[nPouleNumber] != undefined && round.poules[nPouleNumber].places[nPoulePlaceNumber] != undefined) {
                        bValidPoulePlaces = true;

                        if ( pouleplacespernumber[nPoulePlaceNumber] == undefined )
                            pouleplacespernumber[nPoulePlaceNumber] = [];

                        var pouleplace = round.poules[nPouleNumber].places[nPoulePlaceNumber];
                        pouleplacespernumber[nPoulePlaceNumber].push(pouleplace);
                    }
                }
                nPoulePlaceNumber++;
            }
        }
        return pouleplacespernumber;
    }


    function createPoulePlacesZigZag( round ) {
        // reset next and reorder pouleplaces
        var pouleplaces = [];
        {
            var nZigZagNumber = -1;
            var bValidPoulePlaces = true;
            var nPoulePlaceNumber = 0;
            while (bValidPoulePlaces) {
                var pouleplacesfornumber = [];
                bValidPoulePlaces = false;
                for (var nPouleNumber = 0; nPouleNumber < round.poules.length; nPouleNumber++) {
                    if (round.poules[nPouleNumber] != undefined && round.poules[nPouleNumber].places[nPoulePlaceNumber] != undefined) {
                        bValidPoulePlaces = true;

                        var pouleplace = round.poules[nPouleNumber].places[nPoulePlaceNumber];
                        pouleplacesfornumber.push(pouleplace);
                    }
                }
                if ( nPoulePlaceNumber == nZigZagNumber ) {
                    pouleplacesfornumber.reverse();
                    nZigZagNumber += 2;
                }
                pouleplaces = pouleplaces.concat( pouleplacesfornumber );
                nPoulePlaceNumber++;
            }
        }
        return pouleplaces;
    }

    /**
     * axelpale/combinations.js
     * example : k_combinations([1, 2, 3], 2) -> [[1,2], [1,3], [2, 3]
     * @param set
     * @param k
     * @returns {*}
     */
    function k_combinations(set, k) {
        var i, j, combs, head, tailcombs;

        if (k > set.length || k <= 0) {
            return [];
        }

        if (k == set.length) {
            return [set];
        }

        if (k == 1) {
            combs = [];
            for (i = 0; i < set.length; i++) {
                combs.push([set[i]]);
            }
            return combs;
        }

        // Assert {1 < k < set.length}

        combs = [];
        for (i = 0; i < set.length - k + 1; i++) {
            head = set.slice(i, i+1);
            tailcombs = k_combinations(set.slice(i + 1), k - 1);
            for (j = 0; j < tailcombs.length; j++) {
                combs.push(head.concat(tailcombs[j]));
            }
        }
        return combs;
    }

    this.stringify = function( object ) {
        return nameFactory.stringify( object );
    };

        /*return JSON.stringify( cs, function( key, value) {
                if( key == 'frompouleplaces' || key == 'topouleplaces' ) {
                    if ( value.length == 0 ) {
                        return value;
                    }
                    else
                    {
                        var arrPreviousHashKeys = new Array();
                        for( var nI = 0 ; nI < value.length ; nI++ ){
                            arrPreviousHashKeys.push( value[nI].$$hashKey );
                        }
                        return arrPreviousHashKeys;
                    }
                }
            }
            */

    // process the form
    this.processStructureForm = function() {
        this.savestate = null;
        $http({
            method  : 'POST',
            url     : g_sPubMap + 'voetbal/api/competitionseason/?subaction=savestructure',
            data    : that.stringify( that.competitionseason ),  // pass in data as strings
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
        })
            .success(function(data) {
                console.log(data);

                if (data.code == 0 ) {

                    // console.log( data.data );
                    that.savestate = { type: 'success', message: 'structuur opgeslagen' };
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
}]);

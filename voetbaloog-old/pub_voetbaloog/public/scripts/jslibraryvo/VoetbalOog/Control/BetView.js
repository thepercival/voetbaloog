function Ctrl_BetView( oPool, oNow ) {

    var m_oPool = oPool;
    var m_oNow = oNow;
    var m_oTwoHoursBeforeNow = new Date( m_oNow.getTime() - ( 1000 * 60 * 60 * 2 ) );
    var m_nMaxVisibleXS = 4;
    var m_oRoundNotPlayed = null;
    var m_oGameNotPlayed = null;
    var m_jsonNavigateTo = null;

    this.show = function () {
        
        var oRounds = m_oPool.getCompetitionSeason().getRounds();
        for (var nI in oRounds) {
            if (!( oRounds.hasOwnProperty(nI) ))
                continue;

            var oRound = oRounds[nI];
            var oBetConfigs = m_oPool.getBetConfigs(oRound);

            var oBetConfigQualify = oBetConfigs[VoetbalOog_Bet_Qualify.nId];
            if (oBetConfigQualify != null)
                showQualify(oBetConfigQualify);

            var oBetConfigScore = oBetConfigs[VoetbalOog_Bet_Score.nId];
            var oBetConfigResult = oBetConfigs[VoetbalOog_Bet_Result.nId];
            if (oBetConfigScore != null || oBetConfigResult != null)
                showScoreSelect(oRound, oBetConfigScore, oBetConfigResult);
        }
    }

    function showScoreSelect(oRound, oBetConfigScore, oBetConfigResult) {
        var oPoolUsers = m_oPool.getUsers(true);
        var oBetConfig = ( oBetConfigScore != undefined ? oBetConfigScore : oBetConfigResult );
        var oGames = oRound.getGamesByDate();
        for (var nI in oGames) {
            if (!( oGames.hasOwnProperty(nI) ))
                continue;

            var oGame = oGames[nI];

            if ( m_oNow < oBetConfig.getDeadLine( oGame ) )
                continue;

            var sContainerId = "betsview-roundnr-" + oRound.getNumber() + "-scoreresult-panel-gamenr-" + oGame.getId();
            var oContainer = document.getElementById( sContainerId );

            if ( m_jsonNavigateTo == null && oGame.getStartDateTime() > m_oTwoHoursBeforeNow )
            {
                m_jsonNavigateTo = { "containerid" : sContainerId, "roundnr" : oRound.getNumber() };
            }

            var oTable = oContainer.appendChild(document.createElement("table"));
            oTable.className = "table table-striped table-betview-scoreselect"; // hidden

            for (var nI in oPoolUsers) {
                if (!( oPoolUsers.hasOwnProperty(nI) ))
                    continue;

                var oPoolUser = oPoolUsers[nI];

                var oRow = oTable.insertRow(oTable.rows.length);

                oCell = oRow.insertCell(oRow.cells.length);
                oCell.style.textAlign = "right";
                oCell.appendChild(document.createTextNode(oPoolUser.getRanking()));

                oCell = oRow.insertCell(oRow.cells.length);
                oCell.appendChild(document.createTextNode(oPoolUser.getUser().getName()));

                oCell = oRow.insertCell(oRow.cells.length);
                oCell.style.textAlign = "center";
                oCell.align = "center";

                var nPoints = 0;
                var sDescription = "niets ingevuld";
                var sClassName = null;
                if (oBetConfigResult != undefined) {
                    var oBetsResults = oPoolUser.getBets(oBetConfigResult);
                    var oBet = oBetsResults[oGame.getId()];
                    if (oBet != null) {
                        sDescription = VoetbalOog_Bet_Factory().getResultDescription(oBet.getResult());
                        if (oBet.getCorrect() == true) {
                            if (oBetConfigScore != undefined)
                                sClassName = "warning";
                            else
                                sClassName = "success";
                            nPoints += oBetConfigResult.getPoints();
                        }
                    }
                }
                if (oBetConfigScore != undefined) {
                    var oBetsScore = oPoolUser.getBets(oBetConfigScore);
                    var oBet = oBetsScore[oGame.getId()];
                    if (oBet != null) {
                        sDescription = oBet.getHomeGoals() + " - " + oBet.getAwayGoals();
                        if (oBet.getCorrect() == true) {
                            sClassName = "success";
                            nPoints += oBetConfigScore.getPoints();
                        }
                    }
                }

                oCell.className = sClassName;
                oCell.appendChild(document.createTextNode(sDescription));

                if (oGame.getState() == g_jsonVoetbal.nState_Played) {
                    oCell = oRow.insertCell(oRow.cells.length);
                    oCell.style.textAlign = "right";
                    oCell.appendChild(document.createTextNode(nPoints));
                    oCell.className = sClassName;
                }
            }
        }
    }

    function showQualify(oBetConfigQualify) {
        var oRound = oBetConfigQualify.getRound();

        if ( m_oNow < oBetConfigQualify.getDeadLine() )
            return;

        var sContainerId = "betsview-roundnr-" + oRound.getNumber() + "-qualify";
        var oContainer = document.getElementById("betsview-roundnr-" + oRound.getNumber() + "-qualify");

        if ( m_jsonNavigateTo == null && oRound.getStartDateTime() > m_oTwoHoursBeforeNow )
        {
            m_jsonNavigateTo = { "containerid" : sContainerId, "roundnr" : oRound.getNumber() };
        }

        var oTable = oContainer.appendChild(document.createElement("table"));
        oTable.className = "table table-striped table-condensed table-betview-qualify";

        var oPoolUsers = m_oPool.getUsers(true);
        var oTeamsInTheRace = m_oPool.getCompetitionSeason().getTeamsInTheRace();
        var oPoules = oRound.getPoules();

        var oRow = oTable.insertRow(oTable.rows.length);
        oRow.className = "tableheader";

        var oCell = oRow.appendChild(document.createElement("th"));
        oCell.style.textAlign = "right";
        oCell.appendChild(document.createTextNode("pl"));

        oCell = oRow.appendChild(document.createElement("th")); // poolusers

        var oQualDiv = oRow.appendChild(document.createElement("th")); // qualifiers
        oQualDiv.className = "row";

        var arrPoulePlaceGroupes = getQualificationGroup( oRound );

        for ( var nJ = 0 ; nJ < arrPoulePlaceGroupes.length ; nJ++ )
        {
            var oQualDivGroup = oQualDiv.appendChild(document.createElement("div"));
            var sClassName = "col-xs-12";
            if ( arrPoulePlaceGroupes.length == m_nMaxVisibleXS )
                sClassName += " col-sm-6 col-md-3";
            else if ( arrPoulePlaceGroupes.length == ( m_nMaxVisibleXS / 2 ) )
                sClassName += " col-sm-6 ";

            oQualDivGroup.className = sClassName;
            var oQualDivGroupRow = oQualDivGroup.appendChild(document.createElement("div"));
            oQualDivGroupRow.className = "row";

            var arrPoulePlaces = arrPoulePlaceGroupes[nJ];
            for ( var nK = 0 ; nK < arrPoulePlaces.length ; nK++ )
            {
                var oPoulePlace = arrPoulePlaces[nK];

                var oDiv = oQualDivGroupRow.appendChild(document.createElement("div"));
                oDiv.className = "col-xs-3";
                VoetbalOog_Control_Factory().appendPoulePlace( oDiv, oPoulePlace, false, true);
            }
        }

        if (oRound.getPrevious() != null) {
            if (m_oNow > oRound.getPrevious().getStartDateTime()) {
                oCell = oRow.appendChild(document.createElement("th"));
                oCell.style.textAlign = 'right';
                var oSpan = oCell.appendChild( document.createElement("span") );
                oSpan.className = "hidden-xs";
                oSpan.appendChild( document.createTextNode("pnt") );
            }
        }

        for (var nI in oPoolUsers) {
            if (!( oPoolUsers.hasOwnProperty(nI) ))
                continue;

            var oPoolUser = oPoolUsers[nI];
            var oBets = oPoolUser.getBets(oBetConfigQualify);

            var oRow = oTable.insertRow(oTable.rows.length);

            oCell = oRow.insertCell(oRow.cells.length);
            oCell.style.textAlign = "right";
            oCell.appendChild(document.createTextNode(oPoolUser.getRanking()));

            oCell = oRow.insertCell(oRow.cells.length);
            oCell.className = "betview-poolusername-xs"
            oCell.appendChild(document.createTextNode(oPoolUser.getUser().getName())); // pooluser

            var oCell = document.createElement("td");
            oCell.style.padding = '0';
            var oQualDiv = oRow.appendChild( oCell ); // qualifiers
            oQualDiv.className = "row";

            var nPoints = 0; var nCount = 0;
            for ( var nJ = 0 ; nJ < arrPoulePlaceGroupes.length ; nJ++ )
            {
                var oQualDivGroup = oQualDiv.appendChild(document.createElement("div"));
                var sClassName = "col-xs-12";
                if ( arrPoulePlaceGroupes.length == m_nMaxVisibleXS )
                    sClassName += " col-sm-6 col-md-3";
                else if ( arrPoulePlaceGroupes.length == ( m_nMaxVisibleXS / 2 ) )
                    sClassName += " col-sm-6 ";

                oQualDivGroup.className = sClassName;
                var oQualDivGroupRow = oQualDivGroup.appendChild(document.createElement("div"));
                oQualDivGroupRow.className = "row";

                var arrPoulePlaces = arrPoulePlaceGroupes[nJ];
                for ( var nK = 0 ; nK < arrPoulePlaces.length ; nK++ )
                {
                    var oPoulePlace = arrPoulePlaces[nK];
                    var oDiv = oQualDivGroupRow.appendChild(document.createElement("div"));
                    sClassName = "col-xs-3";
                    var oBet = oBets[oPoulePlace.getId()];
                    if (oBet != null && oBet.getTeam() != null) {
                        if (oBet.getCorrect() == true) {
                            sClassName += " bg-success";
                            nPoints += oBetConfigQualify.getPoints();
                        }
                        else if (oTeamsInTheRace != null && oTeamsInTheRace[oBet.getTeam().getId()] == null)
                            sClassName += " bg-danger";
                        else if (everyoneHasTeam(oBetConfigQualify, oBet.getTeam()))
                            sClassName += " everyonebetted";

                        oDiv.setAttribute("data-teamid", oBet.getTeam().getId() );
                        VoetbalOog_Control_Factory().appendTeam( oDiv, oBet.getTeam(), false, true, false, true);
                    }
                    else
                        oDiv.appendChild(document.createTextNode("-"));

                    oDiv.className = sClassName;
                }
            }

            if (oRound.getPrevious() != null && m_oNow > oRound.getPrevious().getStartDateTime()) {
                oCell = oRow.appendChild(document.createElement("td"));
                oCell.style.textAlign = 'right';
                oCell.appendChild(document.createTextNode(nPoints));
            }
        }
    }

    /*
     16 	vier groepen van 4	per groep xs-12, sm-6, md-3
     8 	twee groepen van 4 	per groep xs-12, sm-12
     4 	1 groepen van 4 	per groep xs-12
     2 	1 groepen van 2 	per groep xs-12
     1 	1 groepen van 1 	per groep xs-12
     */
    function getQualificationGroup( oRound )
    {
        var arrRowGroups = new Array();
        {
            var arrRowGroup = new Array();
            var oPoules = oRound.getPoules();
            for (var nJ in oPoules) {
                if (!( oPoules.hasOwnProperty(nJ) ))
                    continue;

                var oPoule = oPoules[nJ];

                var oPoulePlaces = oPoule.getPlaces();
                for (var nK in oPoulePlaces) {
                    if (!( oPoulePlaces.hasOwnProperty(nK) ))
                        continue;

                    var oPoulePlace = oPoulePlaces[nK];
                    if (oPoulePlace.getFromQualifyRule() == null)
                        continue;

                    arrRowGroup.push( oPoulePlace );
                    if ( arrRowGroup.length == m_nMaxVisibleXS)
                    {
                        arrRowGroups.push( arrRowGroup );
                        arrRowGroup = new Array();
                    }

                }
            }
            if ( arrRowGroups.length == 0 ) {
                arrRowGroups.push( arrRowGroup );
            }
        }
        return arrRowGroups;
    }

    function everyoneHasTeam(oBetConfigQualify, oBettedTeam) {
        if (typeof oBetConfigQualify.getSameTeams != 'function')
            return false;

        var oTeams = oBetConfigQualify.getSameTeams();
        if (oTeams != null && oTeams[oBettedTeam.getId()] != null)
            return true;
        return false;
    }


    this.navigate = function( jQuery, nBetViewRoundNr )
    {
        var sContainerId = null; var bCollapse = true;
        if ( nBetViewRoundNr == null )
        {
            if ( m_jsonNavigateTo != null )
                sContainerId = m_jsonNavigateTo.containerid;
        }
        else
        {
            if ( m_jsonNavigateTo != null && nBetViewRoundNr == m_jsonNavigateTo.roundnr )
                sContainerId = m_jsonNavigateTo.containerid;
            else {
                sContainerId = "betsview-roundnr-" + nBetViewRoundNr;
                bCollapse = false;
            }
        }
        if ( sContainerId == null)
            return;

        var nTop = 0;
        if ( bCollapse == true ) {
            sContainerId = sContainerId.replace("panel", "accordion");
            nTop -= 40;
            jQuery("#"+sContainerId).collapse('show');
        }
        nTop += jQuery("#"+sContainerId).offset().top;

        jQuery('html, body').animate({
            scrollTop: nTop
        }, 1000);
    };



    function selectGameSelect() {
        var oGameSelect = document.getElementById("gameid");
        var oRounds = g_oPool.getCompetitionSeason().getRounds();
        var oBetConfigQualifyFallBack = null;
        for (var nI in oRounds) {
            if (!( oRounds.hasOwnProperty(nI) ))
                continue;

            var oRound = oRounds[nI];

            var nBetTimes = g_oPool.getBetTimes(oRound);



            var bRoundInProgress = ( g_oNowPlusHalfHour > oRound.getStartDateTime() && g_oNowMinHalfHour < oRound.getEndDateTime() );

            var nSelectedBetQualifyId = null;
            {
                var oBetConfigs = g_oPool.getBetConfigs(oRound);
                var oBetConfigQualify = oBetConfigs[VoetbalOog_Bet_Qualify.nId];
                if (oBetConfigQualify != undefined) {
                    oBetConfigQualifyFallBack = oBetConfigQualify;
                    if (g_oNow < oRound.getStartDateTime())
                        nSelectedBetQualifyId = oBetConfigQualify.getId();
                }
            }

            var nSelectedGameId = null;
            var nSelectedSubGameId = null;
            {
                var oBetConfigScore = oBetConfigs[VoetbalOog_Bet_Score.nId];
                var oBetConfigResult = oBetConfigs[VoetbalOog_Bet_Result.nId];
                if (oBetConfigScore != undefined || oBetConfigResult != undefined) {
                    var nNrOfMinutesGame = oRound.getCompetitionSeason().getNrOfMinutesGame();
                    var oBetConfig = ( oBetConfigScore != undefined ? oBetConfigScore : oBetConfigResult );
                    var arrGames = oRound.getGamesByDate();
                    for (var nJ = 0; nJ < arrGames.length; nJ++) {
                        var oGame = arrGames[nJ];

                        if (g_oNow < oBetConfig.getDeadLine(oGame))
                            continue;

                        if (!bRoundInProgress)
                            continue;

                        var oGameEndDateTime = new Date(oGame.getStartDateTime().getTime());
                        oGameEndDateTime.add("m", nNrOfMinutesGame);

                        if (nSelectedGameId == null && g_oNowPlusHalfHour > oGame.getStartDateTime() && g_oNowMinHalfHour < oGameEndDateTime)
                            nSelectedGameId = oGame.getId();
                        else if (nSelectedGameId != null && g_oNow > oGame.getStartDateTime() && g_oNowMinHalfHour < oGameEndDateTime)
                            nSelectedGameId = oGame.getId();

                        if (nSelectedSubGameId == null && g_oNow < oGame.getStartDateTime())
                            nSelectedSubGameId = oGame.getId();
                    }
                }
            }
            if (nSelectedGameId != null) {
                oGameSelect.value = nSelectedGameId;
                return;
            }
            else if (showQualifyingImportant(oRound, g_oNow)) {
                if (nSelectedBetQualifyId != null) {
                    oGameSelect.value = nSelectedBetQualifyId;
                    return;
                }
                else if (bRoundInProgress) {
                    var oBetConfigs = g_oPool.getBetConfigs(oRound.getNext());
                    var oBetConfigQualify = oBetConfigs[VoetbalOog_Bet_Qualify.nId];
                    if (oBetConfigQualify != undefined) {
                        oGameSelect.value = oBetConfigQualify.getId();
                        //$("#gameid option[value='" + oBetConfigQualify.getId() + "']").attr("selected", "selected");
                        return;
                    }
                }
            }
            else if (nSelectedSubGameId != null) {
                oGameSelect.value = nSelectedSubGameId;
                return;
            }
            else if (nSelectedBetQualifyId != null) {
                //$("#gameid option[value='" + nSelectedBetQualifyId + "']").attr("selected", "selected");
                oGameSelect.value = nSelectedBetQualifyId;
                return;
            }
            oLastRound = oRound;
        }

        if (oBetConfigQualifyFallBack != undefined) {
            //$("#gameid option[value='" + oBetConfigQualifyFallBack.getId() + "']").attr("selected", "selected");
            oGameSelect.value = oBetConfigQualifyFallBack.getId();
        }
    }

    function showQualifyingImportant(oRound, oNow) {
        if (!oRound.poulesNeedRanking())
            return true;

        var arrGames = oRound.getGamesByDate();

        var nNrOfTeams = Object_Factory().count(oRound.getTeams());
        if (( nNrOfTeams % 2 ) == 1)
            nNrOfTeams--;

        var nNrOfGamesPerTime = nNrOfTeams / 2;

        var nLastNormalGame = arrGames.length - ( nNrOfGamesPerTime + 1 );

        var oGame = arrGames[nLastNormalGame];

        var oLastGamesDateTime = new Date(oGame.getStartDateTime().getTime());
        oLastGamesDateTime.add("m", oRound.getCompetitionSeason().getNrOfMinutesGame());

        if (oNow > oLastGamesDateTime)
            return true;

        return false;
    }
}
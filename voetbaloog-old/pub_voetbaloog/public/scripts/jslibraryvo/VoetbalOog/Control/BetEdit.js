function Ctrl_BetEdit( oPoolUser, tsNow, sDivId ) {
    var m_sDivId = sDivId;
    var m_oPoolUser = oPoolUser;
    var m_sControlPrefix = '_control_id_';
    var m_sPouleRankingPrefix = '-poulerank-id-';
    var m_sPouleRankingParentPrefix = '-poulerank-parent-id-';
    var m_oNow = new Date(tsNow);
    var m_sTableClassName = 'table table-striped';
    var m_arrColumnsGames = new Array("datum", "tijd", "thuisteam", "uitslag", "uitteam");
    var m_jsonColumnGameId = {header: 'Id'};
    var m_oBetHelper = null;
    var m_bPreviousCompetitionSeasonsSet = false;
    var m_vtOldValue = null;
    var m_nNrOfBetsDone = 0;
    var m_bPrintEnabled = true;
    var m_oJQuery = null;

    this.putJQuery = function (oJQuery) {
        m_oJQuery = oJQuery;
    };
    this.disablePrint = function () {
        m_bPrintEnabled = false;
    };

    function getDiv() {
        return document.getElementById(m_sDivId);
    }

    this.show = function () {
        var oDiv = getDiv();
        while (oDiv.hasChildNodes())
            oDiv.removeChild(oDiv.lastChild);

        var oPool = m_oPoolUser.getPool();
        var oCompetitionSeason = oPool.getCompetitionSeason();
        var oRounds = oCompetitionSeason.getRounds();

        for (var nI in oRounds) {
            if (!( oRounds.hasOwnProperty(nI) ))
                continue;

            var oRound = oRounds[nI];

            var oPoolEndDateTime = oPool.getRoundEndDateTime(oRound);
            if ( oPoolEndDateTime == null || m_oNow > oPoolEndDateTime )
                continue;

            showRound(oDiv, oRound);
        }

        if ( Object_Factory().count( oRounds ) > 0 ) {
            appendHeader(oDiv, true, oPool);
            appendHeader(oDiv, false, oPool);
            updateBetsDoneTotals();
        }
    };

    function appendHeader(oContainer, bTop, oPool) {
        var oRow = document.createElement("div");
        oRow.className = 'row';
        oRow.style.margin = '0';

        var oSaveDiv = oRow.appendChild(document.createElement("div"));
        {
            oSaveDiv.className = 'col-xs-12';
            oSaveDiv.style.textAlign = 'center';

            var oSaveWrapper = oSaveDiv.appendChild(document.createElement("div"));
            oSaveWrapper.style.textAlign = 'center';

            var oSaveButton = oSaveWrapper.appendChild(document.createElement("input"));
            {
                oSaveButton.type = 'submit';
                oSaveButton.style.fontWeight = "bold";
                oSaveButton.id = 'btnsavebets';
                oSaveButton.name = 'btnsavebets';
                oSaveButton.value = "alles opslaan";
                oSaveButton.className = 'btn btn-default';
                oSaveButton.role = "button";
            }

            var oToDoBtn = oSaveWrapper.appendChild(document.createElement("input"));
            {
                oToDoBtn.type = 'button';
                oToDoBtn.className = 'btn btn-default';
                oToDoBtn.role = "button";
                oToDoBtn.style.fontWeight = 'bold';
                oToDoBtn.disabled = true;
                if (bTop == true)
                    oToDoBtn.id = 'todobetstop';
                else
                    oToDoBtn.id = 'todobetsbottom';
            }

            var oClearDiv = oSaveWrapper.appendChild(document.createElement("div"));
            oClearDiv.style.clear = 'both';
        }

        if (bTop == true)
            oContainer.insertBefore(oRow, oContainer.firstChild);
        else
            oContainer.appendChild(oRow);
    }

    function showRound(oContainer, oRound) {

        // add header
        {
            var oRoundHeader = oContainer.appendChild(document.createElement("h5"));
            var span = document.createElement('span');
            span.innerHTML = VoetbalOog_Round_Factory().getName(oRound);
            oRoundHeader.appendChild(span);
            oRoundHeader.style.textAlign = "center";
            oRoundHeader.style.fontWeight = "bold";
        }

        var oRoundBetConfigs = m_oPoolUser.getPool().getBetConfigs(oRound);

        // if round is not readonly and previous has ranking and this one has qualify
        if (oRoundBetConfigs[VoetbalOog_Bet_Qualify.nId] != undefined && m_oNow <= oRoundBetConfigs[VoetbalOog_Bet_Qualify.nId].getDeadLine( null ) ) {
            var oPreviousRound = oRound.getPrevious();
            if (oPreviousRound != undefined && oPreviousRound.poulesNeedRanking() == true) {
                var oAutoInputDiv = oContainer.appendChild(document.createElement('div'));
                oAutoInputDiv.style.textAlign = 'center';
                oAutoInputDiv.style.marginBottom = '3px';

                var oAutoInputLink = oAutoInputDiv.appendChild(document.createElement('a'));
                oAutoInputLink.setAttribute('href', '#');
                oAutoInputLink.className = "btn btn-xs btn-default";
                oAutoInputLink.role = "button";
                oAutoInputLink.onclick = function () {
                    autoInputQualifiers(oRound.getId());
                    return false;
                };
                oAutoInputLink.appendChild(document.createTextNode('vul automatisch de gekwalificeerden in'));
                oAutoInputLink.appendChild(document.createElement('br'));
                oAutoInputLink.appendChild(document.createTextNode('o.b.v. de poulestanden van de vorige ronde'));
            }
        }

        var oPoules = oRound.getPoules();
        var oTable = null;
        for (var nJ in oPoules) {
            var oPoule = oPoules[nJ];

            // per round set
            var nNrOfPoulePlaces = Object_Factory().count(oPoule.getPlaces());
            var bShowGames = nNrOfPoulePlaces > 1;

            if (oPoule.needsRanking() == true) {
                // add extra fieldset
                var oPouleContainer = oContainer.appendChild(document.createElement("div"));
                oPouleContainer.style.marginTop = '5px';
                oPouleContainer.style.textAlign = 'center';
                var span = oPouleContainer.appendChild(document.createElement('span'));
                span.style.fontWeight = 'bold';
                span.innerHTML = VoetbalOog_Poule_Factory().getName(oPoule, true) + " ";
                var oLink = oPouleContainer.appendChild(document.createElement('a'));
                oLink.style.display = 'inline-block';
                oLink.role = 'button';
                oLink.className = 'btn btn-default btn-sm visible-xs btn-show-poulerank';
                oLink.appendChild(document.createElement('span')).className = 'glyphicon glyphicon-th-list';
                oLink.appendChild(document.createTextNode(" stand"));
                oLink.href = "#collapseRanking" + oPoule.getId();
                oLink.setAttribute("data-toggle", "collapse");
                oLink.setAttribute("data-pouleid", oPoule.getId());
                oLink.setAttribute("aria-expanded", "false");
                oLink.setAttribute("aria-controls", "collapseRanking" + oPoule.getId());
                oLink.onclick = function () { // move ranking to well
                    var nPouleId = this.getAttribute("data-pouleid");
                    var oWell = document.getElementById("collapseRankingWell" + nPouleId);
                    if (oWell.hasChildNodes()) { return; }
                    oWell.appendChild( document.getElementById( getPouleRankingDivId( nPouleId ) ) );
                };

                var oRankingDiv = oPouleContainer.appendChild(document.createElement('div'));
                oRankingDiv.className = "collapse";
                oRankingDiv.id = "collapseRanking" + oPoule.getId();
                var oRankingWell = oRankingDiv.appendChild(document.createElement('div'));
                oRankingWell.id = "collapseRankingWell" + oPoule.getId();
                oRankingWell.className = "well visible-xs";
                oRankingWell.setAttribute("data-pouleid", oPoule.getId());

                showPoule(oContainer, oPoule, oRoundBetConfigs);

                oTable = null;
            }
            else {
                if (oTable == null) {
                    //var oDivTable = oContainer.appendChild( document.createElement("div") );
                    //oDivTable.className = "table-responsive";

                    oTable = oContainer.appendChild(document.createElement("table"));
                    oTable.className = m_sTableClassName;

                    if (bShowGames == true)
                        showPouleGamesHeaders(oTable, oPoule);
                }
                if (bShowGames == true)
                    showPouleGames(oTable, oPoule, oRoundBetConfigs);
                else {
                    showWinner(oTable, oPoule, oRoundBetConfigs);
                    oTable.style.textAlign = 'center';
                }
            }
        }

        if ( oRoundBetConfigs[VoetbalOog_Bet_Score.nId] != null
            ||  oRoundBetConfigs[VoetbalOog_Bet_Result.nId] != null
        )
            addBetInfo( oRound, oRoundBetConfigs, oContainer );
    }

    function showPoule(oContainer, oPoule, oRoundBetConfigs) {
        var oPouleContainer = oContainer.appendChild(document.createElement('div'));
        oPouleContainer.className = "row";
        oPouleContainer.style.margin = "0px";

        var oGamesDiv = oPouleContainer.appendChild(document.createElement('div'));
        {
            oGamesDiv.className = 'col-xs-12 col-sm-8 col-md-6';
            oGamesDiv.style.paddingLeft = "0px";
            oGamesDiv.style.paddingRight = "0px";

            var oTable = document.createElement("table");
            oTable.className = m_sTableClassName;
            oGamesDiv.appendChild(oTable);

            showPouleGamesHeaders(oTable, oPoule);
            showPouleGames(oTable, oPoule, oRoundBetConfigs);
        }

        var oRankAndInfoDiv = oPouleContainer.appendChild(document.createElement('div'));
        {
            oRankAndInfoDiv.id = getPouleRankingParentDivId(oPoule);
            oRankAndInfoDiv.className = 'hidden-xs col-sm-4 col-md-offset-3 col-md-3';
            oRankAndInfoDiv.setAttribute("data-pouleid", oPoule.getId());
            oRankAndInfoDiv.style.paddingLeft = "30px";
            oRankAndInfoDiv.style.paddingRight = "0px";

            var oRankDiv = oRankAndInfoDiv.appendChild(document.createElement('div'));
            oRankDiv.id = getPouleRankingDivId(oPoule);
            oRankDiv.className = 'poule-ranking-div';

            var oRoundBetConfig = oRoundBetConfigs[VoetbalOog_Bet_Score.nId];
            if (oRoundBetConfig == undefined)
                oRoundBetConfig = oRoundBetConfigs[VoetbalOog_Bet_Result.nId];
            updatePouleStandings(oPoule, oRoundBetConfig);
        }

        oContainer.appendChild(document.createElement('div')).style.clear = 'both';
    }

    function addBetInfo( oRound, oRoundBetConfigs, oContainer ) {

        var oRoundBetConfig = oRoundBetConfigs[VoetbalOog_Bet_Score.nId];
        if (oRoundBetConfig == undefined)
            oRoundBetConfig = oRoundBetConfigs[VoetbalOog_Bet_Result.nId];

        // show only for roundnumber 0
        if (m_oBetHelper != null && oRoundBetConfig != undefined && oRoundBetConfig.getRound().getNumber() == 0 ) {

            var oInfoDiv = document.createElement('div');
            oInfoDiv.className = 'col-xs-12';
            oInfoDiv.style.textAlign = 'center';
            oInfoDiv.style.marginBottom = '15px';

            if (m_oBetHelper != null)
                m_oBetHelper.refresh(oRoundBetConfig, m_oPoolUser, null);

            oContainer.appendChild(oInfoDiv);
            {
                var oStatsLink = document.createElement('a');
                oStatsLink.id = 'btntogglebetconfig';
                oStatsLink.className = 'btn btn-md btn-info';
                oStatsLink.innerHTML = 'voorspellingeninformatie <span class="glyphicon glyphicon-info-sign"></span>';
                oStatsLink.onclick = function () {
                    m_oBetHelper.showModal(m_oJQuery);
                    if (m_bPreviousCompetitionSeasonsSet == false) {
                        var oCompetitionSeasons = new Object();
                        // @TODO fill competitionid from certain competition, e.g. EC/WC
                        m_oJQuery.ajax({
                            url: g_sPubMap + 'competitionseason/ajax/',
                            data: {ajaxaction: "getprevious", maxamount: 3, competitionid : null },
                            dataType: "json",
                            async: true
                        }).done(function (jsonCS) {
                            oCompetitionSeasons = VoetbalOog_CompetitionSeason_Factory().createObjectsFromJSON(jsonCS.data);

                            m_oBetHelper.putPreviousCompetitionSeasons(oCompetitionSeasons);

                            m_oBetHelper.refresh(oRoundBetConfig, m_oPoolUser);
                            m_bPreviousCompetitionSeasonsSet = true;
                            m_oBetHelper.show();
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus);
                            console.log(jqXHR.responseText);
                        });
                    }
                    else {
                        m_oBetHelper.show();
                    }
                };
                oInfoDiv.appendChild(oStatsLink);
            }
        }
    }

    function getPouleRankingDivId( vtPoule ) {
        var sPouleId = ( typeof vtPoule == "object") ? vtPoule.getId() : vtPoule;
        return m_sDivId + m_sPouleRankingPrefix + sPouleId;
    }

    function getPouleRankingParentDivId(oPoule) {
        return m_sDivId + m_sPouleRankingParentPrefix + oPoule.getId();
    }

    this.getPouleRankingParentDivId = function ( nPouleId ) {
        return m_sDivId + m_sPouleRankingParentPrefix + nPouleId;
    };

    function getControlId( oRoundBetConfig, oObject, sPostfix )
    {
        var sRetval = m_sDivId + m_sControlPrefix + oRoundBetConfig.getId() + "_" + oObject.getId();
        if ( sPostfix != null )
            sRetval += sPostfix;
        return sRetval;
    }

    function getRoundBetConfigFromControlId( oControl )
    {
        var sPrefix = m_sDivId + m_sControlPrefix;
        var sControlId = oControl.id.substr( sPrefix.length );
        var sRoundBetConfigId = sControlId.substr( 0, sControlId.indexOf("_") );
        return VoetbalOog_Round_BetConfig_Factory().createObjectFromDatabase( sRoundBetConfigId );
    }

    function getObjectIdFromControlId( oControl )
    {
        var sPrefix = m_sDivId + m_sControlPrefix;
        var sId = oControl.id.substr( sPrefix.length );
        sId = sId.substr( sId.indexOf("_") + 1 );

        var nEndPos = undefined;
        var nPostfix = sId.indexOf("_");
        // var sPostfix = null;
        if ( nPostfix >= 0 )
        {
            nEndPos = nPostfix;
            // sPostfix = sId.substr( nPostfix );
        }
        return sId.substr( 0, nEndPos );
    }

    function showPouleGamesHeaders( oTable, oPoule )
    {
        var oRowHeader = oTable.insertRow( oTable.rows.length );
        oRowHeader.className = "tableheader";

        var arrColumns = getGamesColumns();
        for ( var nI = 0 ; nI < arrColumns.length ; nI++ )
        {
            var sHeader = arrColumns[nI];

            var oCell = oRowHeader.appendChild( document.createElement("th") );
            if ( sHeader == "uitslag" )
                oCell.style.textAlign = 'center';
            else if ( sHeader == "thuisteam" )
                oCell.style.textAlign = 'right';
            else if ( sHeader == "datum" || sHeader == "tijd" )
                oCell.className = 'hidden-xs';
            oCell.innerHTML = sHeader;
        }

        if ( oPoule.needsRanking() == false )
        {
            var oCell = oRowHeader.appendChild( document.createElement("th") );
            oCell.style.textAlign = 'center';
            oCell.innerHTML = m_jsonColumnGameId.header;
        }
    }

    function showPouleGames( oTable, oPoule, oRoundBetConfigs )
    {
        var arrGames = oPoule.getGamesByDate();

        for ( var nJ = 0 ; nJ < arrGames.length ; nJ++ )
        {
            var oGame = arrGames[nJ];

            var oRow = oTable.insertRow( oTable.rows.length );

            var oCell = oRow.insertCell( oRow.cells.length );
            oCell.innerHTML = dateFormat( oGame.getStartDateTime(), 'dd mmm');
            oCell.className = 'hidden-xs';

            oCell = oRow.insertCell( oRow.cells.length );
            oCell.innerHTML = dateFormat( oGame.getStartDateTime(), 'HH:MM');
            oCell.className = 'hidden-xs';

            oCell = oRow.insertCell( oRow.cells.length );
            oCell.align = "right";
            createPoulePlaceControl( oCell, oGame.getHomePoulePlace(), oRoundBetConfigs, true );

            oCell = oRow.insertCell( oRow.cells.length );
            oCell.align = "center";
            oCell.noWrap = "true";
            createResultControl( oCell, oGame, oRoundBetConfigs );

            oCell = oRow.insertCell( oRow.cells.length );
            createPoulePlaceControl( oCell, oGame.getAwayPoulePlace(), oRoundBetConfigs, false );

            if ( oPoule.needsRanking() == false )
            {
                oCell = oRow.insertCell( oRow.cells.length );
                oCell.align = "center";
                oCell.innerHTML = VoetbalOog_Poule_Factory().getName( oPoule, false );
            }
        }
    }

    function showWinner( oTable, oPoule, oRoundBetConfigs )
    {
        var oPoulePlaces = oPoule.getPlaces();

        for ( var nI in oPoulePlaces )
        {
            if ( !( oPoulePlaces.hasOwnProperty( nI ) ) )
                continue;

            var oPoulePlace = oPoulePlaces[nI];

            var oRow = oTable.insertRow( oTable.rows.length );

            var oCell = oRow.insertCell( oRow.cells.length );

            createPoulePlaceControl( oCell, oPoulePlace, oRoundBetConfigs, false );
        }
    }

    function getGamesColumns()
    {
        return m_arrColumnsGames;
    }

    function autoInputQualifiers( nRoundId )
    {
        var oPool = m_oPoolUser.getPool();
        var oCompetitionSeason = oPool.getCompetitionSeason();
        var oRounds = oCompetitionSeason.getRounds();
        var oRound = oRounds[nRoundId];

        var oRoundBetConfigs = m_oPoolUser.getPool().getBetConfigs( oRound );
        var oRoundBetConfig = oRoundBetConfigs[ VoetbalOog_Bet_Qualify.nId ];
        if ( oRoundBetConfig == undefined )
            return;

        var oPreviousRound = oRound.getPrevious();
        if ( oPreviousRound == null )
            return;

        var oPreviousRoundBetConfigs = m_oPoolUser.getPool().getBetConfigs( oPreviousRound );
        var oPreviousRoundBetConfig = oPreviousRoundBetConfigs[ VoetbalOog_Bet_Score.nId ];
        if ( oPreviousRoundBetConfig == null )
            oPreviousRoundBetConfig = oPreviousRoundBetConfigs[ VoetbalOog_Bet_Result.nId ];
        if ( oPreviousRoundBetConfig == undefined )
            return;

        var oRanking = new VoetbalOog_Ranking( oCompetitionSeason.getPromotionRule() );

        var oPoules = oRound.getPoules();

        var oClonedGamesPerPoule = new Object();
        {
            var oFromPoules = oPreviousRound.getPoules();
            for ( var nI in oFromPoules ) {
                if (!( oFromPoules.hasOwnProperty(nI) ))
                    continue;
                var oFromPoule = oFromPoules[nI];
                oClonedGamesPerPoule[ oFromPoule.getId() ] = getClonedGames( oFromPoule, oPreviousRoundBetConfig);
            }
        }

        for ( var nI in oPoules )
        {
            if ( !( oPoules.hasOwnProperty( nI ) ) )
                continue;

            var oPoule = oPoules[nI];

            var oPoulePlaces = oPoule.getPlaces();
            for ( var nJ in oPoulePlaces )
            {
                if ( !( oPoulePlaces.hasOwnProperty( nJ ) ) )
                    continue;

                var oPoulePlace = oPoulePlaces[nJ];

                var oSelect = document.getElementById( getControlId( oRoundBetConfig, oPoulePlace, null ) );
                if ( oSelect == undefined /*|| oSelect.type != 'select'*/ )
                    continue;

                var oQualifiedTeam = getTeamFromQualifyRule( oPoulePlace, oClonedGamesPerPoule, oRanking );
                if ( oQualifiedTeam == null )
                    continue;

                // normal onfocus
                m_vtOldValue = oSelect.options[oSelect.selectedIndex].value;

                var nOldValue = oSelect.value;
                for ( var i = 0 ; i < oSelect.options.length; i++ )
                {
                    if ( oSelect.options[i].value == oQualifiedTeam.getId() )
                    {
                        oSelect.selectedIndex = i;
                        m_oJQuery( oSelect ).trigger( "change" );
                        break;
                    }
                }
            }
        }
    }

    function createPoulePlaceControl( oTableCell, oPoulePlace, oRoundBetConfigs, bReverse )
    {
        var oRoundBetConfig = oRoundBetConfigs[ VoetbalOog_Bet_Qualify.nId ];
        if ( oRoundBetConfig != undefined )
        {
            var oTeamBetted = null;
            {
                var oBets = m_oPoolUser.getBets( oRoundBetConfig );
                var oBet = oBets[ oPoulePlace.getId() ];
                if ( oBet != null && oBet.getTeam() != null )
                    oTeamBetted = oBet.getTeam();
            }

            var oDeadLine = oRoundBetConfig.getDeadLine( null );
            if ( m_oNow <= oDeadLine ) // not passed deadline
            {
                // create select here, get also team from pouleplace if is in database
                var oSelect = document.createElement("select");
                oSelect.className = "beteditinput";
                oSelect.id = getControlId( oRoundBetConfig, oPoulePlace, null );
                oSelect.name = oSelect.id;
                oSelect.className = "form-control";
                oSelect.style.display = "inline-block";
                oSelect.style.minWidth = "75px";
                oSelect.style.maxWidth = "120px";
                oSelect.onchange = function(){
                    updateBetQualify( this );
                    m_vtOldValue = this.options[this.selectedIndex].value;
                };
                oSelect.onfocus = function(){ m_vtOldValue = this.options[this.selectedIndex].value; };

                addOptionsToSelect( oSelect, oPoulePlace, oTeamBetted );

                if ( bReverse == true )
                {
                    oTableCell.appendChild( oSelect );
                    VoetbalOog_Control_Factory().appendPoulePlace( oTableCell, oPoulePlace, bReverse, false );
                    oSelect.style.marginRight = "3px";
                }
                else
                {
                    VoetbalOog_Control_Factory().appendPoulePlace( oTableCell, oPoulePlace, bReverse, false );
                    oTableCell.appendChild( oSelect );
                    oSelect.style.marginLeft = "3px";
                }

                var nDelta = ( oTeamBetted != null ) ? 1 : null;
                updateBetsToDo( nDelta, oSelect.parentNode, false );
            }
            else // passed limit show team betted
            {
                if ( oTeamBetted == null )
                    oTableCell.innerHTML = "niets ingevuld";
                else
                    VoetbalOog_Control_Factory().appendTeam( oTableCell, oTeamBetted, bReverse, false, false, false );
            }
        }
        else
        {
            VoetbalOog_Control_Factory().appendPoulePlace( oTableCell, oPoulePlace, bReverse, false );
        }
    }

    function updateBetQualify( oControl )
    {
        var oRoundBetConfig = getRoundBetConfigFromControlId( oControl );

        var sPoulePlaceId = getObjectIdFromControlId( oControl );
        var oPoulePlace = VoetbalOog_PoulePlace_Factory().createObjectFromDatabase( sPoulePlaceId );

        var oBets = m_oPoolUser.getBets( oRoundBetConfig );
        var oBet = oBets[ sPoulePlaceId ];
        if ( oBet == undefined )
        {
            var nBetType = oRoundBetConfig.getBetType();
            oBet = VoetbalOog_Bet_Factory().createObject( nBetType );
            oBet.putId( "__NEW__" + oPoulePlace.getId() );
            oBet.putPoolUser( m_oPoolUser );
            oBet.putRoundBetConfig( oRoundBetConfig );
            oBet.putPoulePlace( oPoulePlace );

            oBets[ sPoulePlaceId ] = oBet;
        }
        var vtTeamId = parseInt( oControl.value, 10 );
        if ( vtTeamId == -1 )
            vtTeamId = null;
        oBet.putTeam( vtTeamId );

        // start : update bets to do(done)
        var nDelta = 0;
        if ( m_vtOldValue < 0 && oControl.value >= 0  )
            nDelta = 1;
        else if ( m_vtOldValue >= 0 && oControl.value < 0 )
            nDelta = -1;
        updateBetsToDo( nDelta, oControl.parentNode, true );
        // end : update bets to do(done)

        // start : update next qualifying
		var oToQualifyRules = oPoulePlace.getPoule().getRound().getToQualifyRules();
		refreshOptionsForQualifying( oToQualifyRules, oPoulePlace.getPoule() );
        // end : update next qualifying
    }

    function createResultControl( oTableCell, oGame, oRoundBetConfigs )
    {
        if ( oRoundBetConfigs[ VoetbalOog_Bet_Score.nId ] != undefined )
        {
            var oRoundBetConfig = oRoundBetConfigs[ VoetbalOog_Bet_Score.nId ];
            var oDeadLine = oRoundBetConfig.getDeadLine( oGame );
            if ( m_oNow <= oDeadLine ) // not passed deadline
            {
                appendScoreSelect( oTableCell, oRoundBetConfigs, oGame, '_homegoals' );
                oTableCell.appendChild( document.createTextNode(" - ") );
                appendScoreSelect( oTableCell, oRoundBetConfigs, oGame, '_awaygoals' );
            }
            else
            {
                var oBets = m_oPoolUser.getBets( oRoundBetConfig );
                var oBet = oBets[ oGame.getId() ];
                if ( oBet == null )
                    oTableCell.innerHTML = " - ";
                else
                    oTableCell.appendChild( document.createTextNode( oBet.getHomeGoals() + " - " + oBet.getAwayGoals() ) );
            }
        }
        else if ( oRoundBetConfigs[ VoetbalOog_Bet_Result.nId ] != undefined )
        {
            var oRoundBetConfig = oRoundBetConfigs[ VoetbalOog_Bet_Result.nId ];
            var oDeadLine = oRoundBetConfig.getDeadLine( oGame );
            if ( m_oNow <= oDeadLine ) // not passed deadline
            {
                appendResultSelect( oTableCell, oRoundBetConfigs, oGame );
            }
            else
            {
                var nResultBetted = getResultBetted( oRoundBetConfig, oGame );
                var sDescription = VoetbalOog_Bet_Factory().getResultDescription( nResultBetted );
                oTableCell.appendChild( document.createTextNode( sDescription ) );
            }
        }
        else
        {
            oTableCell.appendChild( document.createTextNode(" - ") );
        }
    }

    function appendScoreSelect( oContainer, oRoundBetConfigs, oGame, sPostfix )
    {
        var oRoundBetConfig = oRoundBetConfigs[ VoetbalOog_Bet_Score.nId ];

        var nGoalsBetted = getGoalsBetted( sPostfix, oRoundBetConfig, oGame );

        var oSelect = oContainer.appendChild( document.createElement("select") );
        oSelect.id = getControlId( oRoundBetConfig, oGame, sPostfix );
        oSelect.name = oSelect.id;
        oSelect.className = "form-control";
        oSelect.style.display = "inline-block";
        oSelect.style.width = "auto";
        oSelect.onchange = function(){
            updateBetScore( this, sPostfix );
            m_vtOldValue = this.options[this.selectedIndex].value;
        };
        oSelect.onfocus = function(){ m_vtOldValue = this.options[this.selectedIndex].value; };

        oSelect.options.add( new Option( '', -1 ) );

        for ( var nI = 0 ; nI < 10 ; nI++ )
        {
            var oOption = new Option( nI, nI );
            if ( nGoalsBetted != null && nGoalsBetted == nI )
                oOption.selected = true;
            oSelect.options.add( oOption );
        }

        if ( sPostfix == '_awaygoals' )
        {
            var sControlIdHomeGoals = oSelect.id.replace( sPostfix, '_homegoals' );
            var oControlHomeGoals = document.getElementById( sControlIdHomeGoals );
            var nGoalsBettedHome = oControlHomeGoals.options[ oControlHomeGoals.selectedIndex].value;

            var nDelta = ( nGoalsBetted >= 0 && nGoalsBettedHome >= 0 ) ? 1 : null;
            updateBetsToDo( nDelta, oSelect.parentNode, false );
        }

        return oSelect;
    }

    function updateBetScore( oControl, sPostfix )
    {
        var oRoundBetConfig = getRoundBetConfigFromControlId( oControl );

        var sGameId = getObjectIdFromControlId( oControl );
        var oGame = VoetbalOog_Game_Factory().createObjectFromDatabase( sGameId );

        var oBets = m_oPoolUser.getBets( oRoundBetConfig );
        var oBet = oBets[ sGameId ];
        if ( oBet == undefined )
        {
            var nBetType = oRoundBetConfig.getBetType();
            oBet = VoetbalOog_Bet_Factory().createObject( nBetType );
            oBet.putId( "__NEW__" + oGame.getId() );
            oBet.putPoolUser( m_oPoolUser );
            oBet.putRoundBetConfig( oRoundBetConfig );
            oBet.putGame( oGame );
            oBet.putHomeGoals( -1 );
            oBet.putAwayGoals( -1 );

            oBets[ sGameId ] = oBet;
        }

        var sPostfixOther = null;
        if ( sPostfix == "_homegoals" ) {
            oBet.putHomeGoals( parseInt( oControl.value, 10 ) );
            sPostfixOther = "_awaygoals";
        }
        else if ( sPostfix == "_awaygoals" ) {
            oBet.putAwayGoals( parseInt( oControl.value, 10 ) );
            sPostfixOther = "_homegoals";
        }

        // update bets to do(done)
        {
            var sControlIdOther = oControl.id.replace( sPostfix, sPostfixOther );
            var oControlOther = document.getElementById( sControlIdOther );
            var vtOtherValue = oControlOther.options[ oControlOther.selectedIndex].value;

            var nDelta = 0;
            if ( m_vtOldValue < 0 && oControl.value >= 0 && vtOtherValue >= 0 )
                nDelta = 1;
            else if ( m_vtOldValue >= 0 && oControl.value < 0 && vtOtherValue >= 0 )
                nDelta = -1;

            // var oRoundBetConfigs = m_oPoolUser.getPool().getBetConfigs( oRoundBetConfig.getRound() );
            updateBetsToDo( nDelta, oControl.parentNode, true );
        }

        updatePouleStandings( oGame.getHomePoulePlace().getPoule(), oRoundBetConfig );
        if ( m_oBetHelper != null )
            m_oBetHelper.refresh( oRoundBetConfig, m_oPoolUser, null );
    }

    function getGoalsBetted( sHomeAway, oRoundBetConfig, oGame )
    {
        var nGoalsBetted = -1;

        var oBets = m_oPoolUser.getBets( oRoundBetConfig );
        var oBet = oBets[ oGame.getId() ];
        if ( oBet != null )
        {
            if ( sHomeAway == "_homegoals" )
                nGoalsBetted = oBet.getHomeGoals();
            else if ( sHomeAway == "_awaygoals" )
                nGoalsBetted = oBet.getAwayGoals();
        }
        return nGoalsBetted;
    }

    function appendResultSelect( oContainer, oRoundBetConfigs, oGame )
    {
        var oRoundBetConfig = oRoundBetConfigs[ VoetbalOog_Bet_Result.nId ];

        var nResultBetted = getResultBetted( oRoundBetConfig, oGame );

        var oSelect = oContainer.appendChild( document.createElement("select") );
        oSelect.id = getControlId( oRoundBetConfig, oGame, null );
        oSelect.name = oSelect.id;
        oSelect.className = "form-control";
        oSelect.onchange = function(){
            updateBetResult( this );
            m_vtOldValue = this.options[this.selectedIndex].value;
        };
        oSelect.onfocus = function(){ m_vtOldValue = this.options[this.selectedIndex].value; };

        oSelect.options.add( new Option( '', -2 ) );

        var oOption = new Option( 'Thuis wint', 1 );
        if ( nResultBetted != null && nResultBetted == 1 )
            oOption.selected = true;
        oSelect.options.add( oOption );

        var oOption = new Option( 'Gelijk', 0 );
        if ( nResultBetted != null && nResultBetted == 0 )
            oOption.selected = true;
        oSelect.options.add( oOption );

        var oOption = new Option( 'Uit wint', -1 );
        if ( nResultBetted != null && nResultBetted == -1 )
            oOption.selected = true;
        oSelect.options.add( oOption );

        var nDelta = ( nResultBetted != -2 ) ? 1 : null;
        updateBetsToDo( nDelta, oSelect.parentNode, false );

        return oSelect;
    }

    function updateBetResult( oControl )
    {
        var oRoundBetConfig = getRoundBetConfigFromControlId( oControl );

        var sGameId = getObjectIdFromControlId( oControl );
        var oGame = VoetbalOog_Game_Factory().createObjectFromDatabase( sGameId );

        var oBets = m_oPoolUser.getBets( oRoundBetConfig );
        var oBet = oBets[ sGameId ];
        if ( oBet == undefined )
        {
            var nBetType = oRoundBetConfig.getBetType();
            oBet = VoetbalOog_Bet_Factory().createObject( nBetType );
            oBet.putId( "__NEW__" + oGame.getId() );
            oBet.putPoolUser( m_oPoolUser );
            oBet.putRoundBetConfig( oRoundBetConfig );
            oBet.putGame( oGame );
            // oBet.putResult( -2 );

            oBets[ sGameId ] = oBet;
        }
        oBet.putResult( parseInt( oControl.value, 10 ) );

        // update bets to do(done)
        var nDelta = 0;
        if ( m_vtOldValue == -2 && oControl.value != -2 )
            nDelta = 1;
        else if ( m_vtOldValue != -2 && oControl.value == -2 )
            nDelta = -1;
        updateBetsToDo( nDelta, oControl.parentNode, true );

        updatePouleStandings( oGame.getHomePoulePlace().getPoule(), oRoundBetConfig );

        if ( m_oBetHelper != null )
            m_oBetHelper.refresh( oRoundBetConfig, m_oPoolUser, null );
    }

    function getResultBetted( oRoundBetConfig, oGame )
    {
        var nResultBetted = -2;
        {
            var oBets = m_oPoolUser.getBets( oRoundBetConfig );
            var oBet = oBets[ oGame.getId() ];
            if ( oBet != null )
                nResultBetted = oBet.getResult();
        }

        return nResultBetted;
    }

    function updatePouleStandings( oPoule, oRoundBetConfig )
    {
        var oContainer = document.getElementById( getPouleRankingDivId( oPoule ) );
        if ( oContainer == null )
            return;

        var oClonedGames = getClonedGames( oPoule, oRoundBetConfig );

        Ctrl_RankView().putQualifierLines( oPoule.getQualifierLines() );
        Ctrl_RankView().show( oContainer, oClonedGames, oPoule.getRound().getCompetitionSeason().getPromotionRule(), {} );
    }

    function updateBetsToDo( nDelta, oDiv, bUpdateTotals )
    {
        if ( nDelta != null )
            m_nNrOfBetsDone += nDelta;

        if ( oDiv == undefined )
            return;

        if ( nDelta == null || nDelta < 0 ) {
            oDiv.className = "alert alert-danger";
            oDiv.style.color = "black";
        }
        else if ( nDelta > 0 ) {
            oDiv.className = "";
        }

        if ( bUpdateTotals == true )
            updateBetsDoneTotals();
    }

    function updateBetsDoneTotals()
    {
        function updateBetsDoneTotalsHelper( sId )
        {
            var oDivToDoBets = document.getElementById( sId );
            if ( oDivToDoBets != undefined )
            {
                var nAvailableBets = m_oPoolUser.getPool().getNrOfAvailableBets();
                oDivToDoBets.value = m_nNrOfBetsDone + "/" + nAvailableBets;
                if ( m_nNrOfBetsDone == nAvailableBets )
                    oDivToDoBets.className = 'btn btn-success';
                else
                    oDivToDoBets.className = 'btn btn-danger';
            }
        }
        updateBetsDoneTotalsHelper( 'todobetstop' );
        updateBetsDoneTotalsHelper( 'todobetsbottom' );
    }

    function getClonedGames( oPoule, oRoundBetConfig )
    {
        var oClonedGames = new Object();
        var oGames = oPoule.getGames();

        for ( var nI in oGames )
        {
            if ( !( oGames.hasOwnProperty( nI ) ) )
                continue;

            var oGame = oGames[nI];
            var oClonedGame = VoetbalOog_Game_Factory().clone( oGame );

            var nHomeGoals = oGame.getHomeGoals();
            var nAwayGoals = oGame.getAwayGoals();
            var nState = g_jsonVoetbal.nState_Scheduled;

            if ( oRoundBetConfig.getBetType() == VoetbalOog_Bet_Score.nId )
            {
                nHomeGoals = getGoalsBetted( "_homegoals", oRoundBetConfig, oGame );
                nAwayGoals = getGoalsBetted( "_awaygoals", oRoundBetConfig, oGame );
                if ( nHomeGoals >= 0 && nAwayGoals >= 0 )
                    nState = g_jsonVoetbal.nState_Played;
            }
            else if ( oRoundBetConfig.getBetType() == VoetbalOog_Bet_Result.nId )
            {
                var nResult = getResultBetted( oRoundBetConfig, oGame );
                if ( nResult == 1 )
                {
                    nHomeGoals = 1;
                    nAwayGoals = 0;
                    nState = g_jsonVoetbal.nState_Played;
                }
                else if ( nResult == 0 )
                {
                    nHomeGoals = 0;
                    nAwayGoals = 0;
                    nState = g_jsonVoetbal.nState_Played;
                }
                else if ( nResult == -1 )
                {
                    nHomeGoals = 0;
                    nAwayGoals = 1;
                    nState = g_jsonVoetbal.nState_Played;
                }
            }
            else
                continue;

            oClonedGame.putHomeGoals( nHomeGoals );
            oClonedGame.putAwayGoals( nAwayGoals );
            oClonedGame.putState( nState );

            oClonedGames[ oClonedGame.getId() ] = oClonedGame;
        }
        return oClonedGames;
    }

    function getTeamsForQualify( oPoulePlaceParam, oTeams )
    {
        if ( oPoulePlaceParam == null )
            return;

        var oFromQualifyRule = oPoulePlaceParam.getFromQualifyRule();
        if ( oFromQualifyRule == null )
            return;

        var oFromPoules = oFromQualifyRule.getFromPoulesByPoulePlace( oPoulePlaceParam );
        if ( Object_Factory().count( oFromPoules ) == 0 )
            return;

        var oRoundBetConfigs = m_oPoolUser.getPool().getBetConfigs( oFromQualifyRule.getFromRound() );
        var oRBCQualify = oRoundBetConfigs[ VoetbalOog_Bet_Qualify.nId ];


        for ( var nI in oFromPoules ) {
            if (!( oFromPoules.hasOwnProperty(nI) ))
                continue;

            var oFromPoule = oFromPoules[nI];

            // if( oFromPoule.getId() === 399 ) {
            //     console.log(
            //         oFromPoules[nI].getId(),
            //         VoetbalOog_Poule_Factory().getName( oFromPoules[nI], true )
            //     );
            // }

            var oPoulePlaces = oFromPoule.getPlaces();
            for (var nJ in oPoulePlaces) {
                if (!( oPoulePlaces.hasOwnProperty(nJ) ))
                    continue;

                var oPoulePlace = oPoulePlaces[nJ];
                var oTeam = oPoulePlace.getTeam();

				var oTeamBetted = null;
				{
					if (oRBCQualify != undefined) {
						var oBets = m_oPoolUser.getBets(oRBCQualify);
						var oBet = oBets[oPoulePlace.getId()];
						if (oBet != null && oBet.getTeam() != null)
						{
							oTeamBetted = oBet.getTeam();
						}
					}
				}

				if (oTeamBetted != null)
					oTeams[oTeamBetted.getId()] = oTeamBetted;
				else if (oTeam != null)
					oTeams[oTeam.getId()] = oTeam;
				else
					getTeamsForQualify(oPoulePlace, oTeams);
            }
        }
    }

    // 1 refill all the next qualifyng select-boxes from oToQualifyRules and only where Poule is oFromPoule
    // 2 if the selected value is not in the selectable-values than deselect the select-box
    function refreshOptionsForQualifying( oToQualifyRules, oFromPoule )
    {
		for ( var nI in oToQualifyRules ) {
			if (!( oToQualifyRules.hasOwnProperty(nI) ))
				continue;

			var oToQualifyRule = oToQualifyRules[nI];

			// var bFromPouleInQualifyRule
			var oFromPoules = oToQualifyRule.getFromPoules();
			if ( oFromPoules[ oFromPoule.getId() ] == null)
				continue;

			var oRoundBetConfigs = m_oPoolUser.getPool().getBetConfigs(oToQualifyRule.getToRound());
			var oRBCQualify = oRoundBetConfigs[VoetbalOog_Bet_Qualify.nId];

			var arrToPoulePlaces = oToQualifyRule.getToPoulePlaces();
			for ( var nJ = 0 ; nJ < arrToPoulePlaces.length ; nJ++ ) {
				var oToPoulePlace = arrToPoulePlaces[nJ];

				if ( oRBCQualify != null )
				{
					// get selectbox
					var sSelectId = getControlId(oRBCQualify, oToPoulePlace, null);
					var oSelect = document.getElementById(sSelectId);
					if (oSelect != null) {
						// save selected value
						var nSelectedId = parseInt(oSelect.options[oSelect.selectedIndex].value, 10);

						var oTeamSelected = null;
						if (nSelectedId >= 0) {
							oTeamSelected = VoetbalOog_Team_Factory().createObjectFromDatabase(nSelectedId);
						}

						// refill select box and select previously saved value, if possible
						m_oJQuery(oSelect).empty();
						var bSelected = addOptionsToSelect(oSelect, oToPoulePlace, oTeamSelected);

						// when no reselect is possible, nrofbets-todo should be changed and betted team should be set to null
						if (oTeamSelected != null && bSelected == false) {
							var oBets = m_oPoolUser.getBets(oRBCQualify);
							var oBet = oBets[oToPoulePlace.getId()];
							if (oBet != null)
								oBet.putTeam(null);
							updateBetsToDo(-1, oSelect.parentNode, true);
						}
					}
				}

				var oNextToQualifyRules = oToPoulePlace.getPoule().getRound().getToQualifyRules();
				refreshOptionsForQualifying( oNextToQualifyRules, oToPoulePlace.getPoule() );
			}
		}
    }

    function addOptionsToSelect( oSelect, oPoulePlace, oTeamBetted )
    {
        var bSelected = false;

        oSelect.options.add( new Option( '', -1 ) );



        var oTeams = new Object();

        getTeamsForQualify( oPoulePlace, oTeams );

        for ( var nId in oTeams )
        {
            if ( !( oTeams.hasOwnProperty( nId ) ) )
                continue;

            var oTeam = oTeams[nId];


            var oOption = new Option( oTeam.getName(), oTeam.getId() );

            /*
             var oContainerTmp = document.createElement('div');

             var oDivTmp = oContainerTmp.appendChild( document.createElement('div') );
             oDivTmp.className = " spriteteam-16 sprite-" + oTeam.getImageName() + "-16";
             oDivTmp.style.paddingLeft = '18px';

             var oSpan = oDivTmp.appendChild( document.createElement("span") );
             oSpan.className = "hidden-xs";
             oSpan.appendChild( document.createTextNode( oTeam.getName() ) );

             var oSpan = oDivTmp.appendChild( document.createElement("span") );
             oSpan.className = "visible-xs";
             oSpan.appendChild( document.createTextNode( oTeam.getAbbreviation().toUpperCase() ) );

             oOption.setAttribute( "data-content", oContainerTmp.innerHTML );
             */
            if ( oTeamBetted != null && oTeamBetted == oTeam ) {
				oOption.selected = true;
                bSelected = true;
            }
            oSelect.options.add( oOption );
        }
        /*
         // sort
         {
         // var nSelected = oSelect.options[oSelect.selectedIndex].value;
         var oOptions = m_oJQuery(oSelect.options);
         oOptions.sort(function(a,b) {
         if (a.text > b.text) return 1;
         else if (a.text < b.text) return -1;
         else return 0;
         })
         m_oJQuery( oSelect ).empty().append( oOptions );
         // m_oJQuery( oSelect ).val( nSelected );
         }
         */
        return bSelected;
    }


    /* @TODO oPreviousRoundBetConfig should not be here, check how to remove this */
    function getTeamFromQualifyRule( oPoulePlace, oClonedGamesPerPoule, oRanking )
    {
        var oQualifyRule = oPoulePlace.getFromQualifyRule();
        if ( oQualifyRule == null )
            return null;

        var arrFromPlaces = oQualifyRule.getFromPoulePlaces();
        if ( oQualifyRule.isSingle() )
        {
            var oFromPoulePlace = arrFromPlaces[0];

            var oClonedGames = oClonedGamesPerPoule[ oFromPoulePlace.getPoule().getId() ];
            oRanking.updatePoulePlaceRankings( oClonedGames, null );
            var arrFromPoulePlacesByRank = oRanking.getPoulePlacesByRanking( oClonedGames, null );

            var oFromPoulePlaceByRank = arrFromPoulePlacesByRank[ oFromPoulePlace.getNumber() ];
            if ( oFromPoulePlaceByRank == null || oFromPoulePlaceByRank.getTeam() == null )
                return null;

            return oFromPoulePlaceByRank.getTeam();
        }

        // Multiple
        var oRankedFromPlaces = new Object();
        {
            for( var nI = 0 ; nI < arrFromPlaces.length ; nI++ )
            {
                var oFromPoulePlace = arrFromPlaces[nI];

                var oClonedGames = oClonedGamesPerPoule[ oFromPoulePlace.getPoule().getId() ];
                oRanking.updatePoulePlaceRankings( oClonedGames, null );
                var arrFromPoulePlacesByRank = oRanking.getPoulePlacesByRanking( oClonedGames, null );

                var oFromPoulePlaceByRank = arrFromPoulePlacesByRank[ oFromPoulePlace.getNumber() ];
                if ( oFromPoulePlaceByRank == null )
                    continue;

                oRankedFromPlaces[ oFromPoulePlaceByRank.getId() ] = oFromPoulePlaceByRank;
            }
        }

        var oClonedGamesRound = new Object();
        {
            for( var nI in oClonedGamesPerPoule ) {
                for( var nJ in oClonedGamesPerPoule[nI] ) {
                    var oClonedGame = oClonedGamesPerPoule[nI][nJ];
                    oClonedGamesRound[ oClonedGame.getId() ] = oClonedGame;
                }
            }
        }
        oRanking.updatePoulePlaceRankings( oClonedGamesRound, oRankedFromPlaces );

        var arrFromQualifiedPoulePlacesByRank = oRanking.getPoulePlacesByRanking( null, oRankedFromPlaces );

        // for( var nI = 0 ; nI < arrFromQualifiedPoulePlacesByRank.length ; nI++ ) {
        // console.log( arrFromQualifiedPoulePlacesByRank[nI].Ranking + ' : ' + arrFromQualifiedPoulePlacesByRank[nI].getTeam().getName() );
        // }

        var arrToPlaces = oQualifyRule.getToPoulePlaces();
        var nNrOfToPlaces = arrToPlaces.length;
        var oConfigs = oQualifyRule.getConfig();

        var nTotalRank = 0; nCount = 1;
        for( var nI = 0 ; nI < arrFromQualifiedPoulePlacesByRank.length ; nI++ ) {
            if ( nCount++ > nNrOfToPlaces) { break; }
            nTotalRank += Math.pow( 2, arrFromQualifiedPoulePlacesByRank[nI].getPoule().getNumber() );
        }

        var arrConfig = oConfigs[ nTotalRank ];
        // console.log( nTotalRank );
        // console.log( arrConfig );
        var nCount = 1;
        for( var nI = 0 ; nI < arrFromQualifiedPoulePlacesByRank.length ; nI++ ) {
            if ( nCount++ > nNrOfToPlaces ) { break; }
            var nIndex = arrConfig.indexOf( Math.pow( 2, arrFromQualifiedPoulePlacesByRank[nI].getPoule().getNumber() ) );

            var nJ = 0;
            for( var nK = 0 ; nK < arrToPlaces.length ; nK++ )
            {
                if ( nJ++ == nIndex && oPoulePlace == arrToPlaces[nK]) {
                    return arrFromQualifiedPoulePlacesByRank[nI].getTeam();
                }
            }
        }
        return null;
    }

    this.putHelper = function( oBetHelper )
    {
        m_oBetHelper = oBetHelper;
    };
}

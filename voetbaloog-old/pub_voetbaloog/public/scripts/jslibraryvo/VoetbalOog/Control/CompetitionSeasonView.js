function Ctrl_CompetitionSeasonView( oCompetitionSeason, tsNow, sDivId, jsonOptions )
{
	var m_sDivId = sDivId;
	var m_oNow = new Date( tsNow );
	var m_oCompetitionSeason = oCompetitionSeason;
	var m_sPouleRankingPrefix = '-poulerank-id-';
	var m_sPouleRankingParentPrefix = '-poulerank-parent-id-';
	var m_sTableClassName = 'table table-striped';
	var m_arrColumnsGames = null;
	var m_jsonColumnGameId = { header : null };
	var m_jsonOptions = jsonOptions;
	var m_oRoundInProgress = null;

	function getDiv()
	{
		return document.getElementById( m_sDivId );
	}

	this.getRoundInProgress = function() { return m_oRoundInProgress; }

	this.show = function()
	{
		var oRounds = m_oCompetitionSeason.getRounds();

		for ( var nI in oRounds )
		{
			if ( !( oRounds.hasOwnProperty( nI ) ) )
				continue;

			var oRound = oRounds[nI];

			var oRoundDiv = getDiv().appendChild( document.createElement("div") );
			oRoundDiv.id = 'cs-rounddivid-' + oRound.getId();
			oRoundDiv.className = ""
			showRound( oRoundDiv, oRound );

			if ( m_oRoundInProgress == null && m_oNow < oRound.getEndDateTime() ) {
				m_oRoundInProgress = oRound;
			}
		}
	};

	function showRound( oContainer, oRound )
	{
		var oHeader = oContainer.appendChild(document.createElement("h5"));
		var span = document.createElement('span');
		span.innerHTML = VoetbalOog_Round_Factory().getName(oRound);
		oHeader.appendChild(span);
		oHeader.style.textAlign = "center";
		oHeader.style.fontWeight = "bold";

    var arrPoules = oRound.getPoulesAsArray();
    var oneGamePerPoule = poulesHaveMaximumOneGame(arrPoules);
    if( oneGamePerPoule ) {
      arrPoules.sort(function (oPouleA, oPouleB) {
        if( oPouleA.getStartDateTime() === null || oPouleB.getStartDateTime() === null ) {
          return 0;
        }
        return (oPouleA.getStartDateTime().getTime() < oPouleB.getStartDateTime().getTime() ? -1 : 1);
      });
    }

		var oTable = null;
    var oPoule = null;
		while( oPoule = arrPoules.shift() )
		{
			if ( oPoule.needsRanking() == true )
			{
				showPoule( oContainer, oPoule );
				oTable = null;
			}
			else
			{
				var nNrOfPoulePlaces = Object_Factory().count( oPoule.getPlaces() );
				var canHaveGames = nNrOfPoulePlaces > 1;

				if ( oTable == null )
				{
					oTable = document.createElement("table");
					oTable.className = m_sTableClassName;
					oContainer.appendChild( oTable );
					if ( canHaveGames )
						showPouleGamesHeaders( oTable, oPoule );
				}
				if ( canHaveGames )
					showPouleGames( oTable, oPoule );
				else
				{
					showWinner( oTable, oPoule );
					oTable.style.textAlign = 'center';
				}
			}
		}
	}

  function poulesHaveMaximumOneGame( asarrPoules )
  {
    for ( var nI in asarrPoules.Items) {
      if (!( asarrPoules.Items.hasOwnProperty(nI) ))
        continue;

      var oPoule = asarrPoules.Items[nI];
      if ( oPoule.getGames().count() > 1) {
        return false;
      }
    }
    return true;
  }

	function showPoule( oContainer, oPoule )
	{
		var oHeader = oContainer.appendChild(document.createElement("h5"));
		oHeader.style.marginTop = '5px';
		oHeader.style.fontWeight = 'bold';
		oHeader.style.textAlign = 'center';

		var span = oHeader.appendChild( document.createElement('span') );
		span.innerHTML = VoetbalOog_Poule_Factory().getName( oPoule, true ) + ' ';

		var oLink = oHeader.appendChild(document.createElement('a'));
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

		var oRankingXSDiv = oContainer.appendChild(document.createElement('div'));
		oRankingXSDiv.className = "collapse";
		oRankingXSDiv.id = "collapseRanking" + oPoule.getId();
		var oRankingWell = oRankingXSDiv.appendChild(document.createElement('div'));
		oRankingWell.id = "collapseRankingWell" + oPoule.getId();
		oRankingWell.className = "well visible-xs";
		oRankingWell.setAttribute("data-pouleid", oPoule.getId());

		var oGamesDiv = oContainer.appendChild( document.createElement( 'div' ) );
		{
			oGamesDiv.className = 'col-xs-12 col-sm-8 col-md-6';
			oGamesDiv.style.paddingLeft = "0px";
			oGamesDiv.style.paddingRight = "0px";

			var oTable = document.createElement("table");
			oTable.className = m_sTableClassName;
			oGamesDiv.appendChild( oTable );

			showPouleGamesHeaders( oTable, oPoule );
			showPouleGames( oTable, oPoule );

			if ( m_jsonOptions["maxheightpoulegames"] > 0 )
			{
				oGamesDiv.style.maxHeight = m_jsonOptions["maxheightpoulegames"] + 'px';
				oGamesDiv.style.overflow = 'auto';
			}
		}

		var oRankParentDiv = oContainer.appendChild( document.createElement('div') );
		{
			oRankParentDiv.id = getPouleRankingParentDivId( oPoule );
			oRankParentDiv.className = 'hidden-xs col-sm-4 col-md-offset-3 col-md-3';
			oRankParentDiv.setAttribute("data-pouleid", oPoule.getId());
			oRankParentDiv.style.paddingLeft = "30px";
			oRankParentDiv.style.paddingRight = "0px";

			var oRankDiv = oRankParentDiv.appendChild( document.createElement( 'div' ) );
			{
				oRankDiv.id = getPouleRankingDivId( oPoule );
				oRankDiv.className = 'poule-ranking-div';
			}
		}

		oContainer.appendChild(document.createElement('div')).style.clear = 'both';

		updatePouleRank( oPoule );
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

	function showPouleGamesHeaders( oTable, oPoule )
	{
		var oRowHeader = oTable.insertRow( oTable.rows.length );
		oRowHeader.className = 'tableheader';

		var arrColumns = getGamesColumns();
		for ( var nI = 0 ; nI < arrColumns.length ; nI++ )
		{
			var sHeader = arrColumns[nI];

			var oCell = oRowHeader.appendChild( document.createElement("th") );
			if ( sHeader == 'uitslag' ) {
				oCell.style.textAlign = "center";
				if ( oPoule.needsRanking() == false )
					oCell.colSpan = 2;
			}
			else if ( sHeader == 'plaats' )
				oCell.className = "hidden-xs";
			else if ( sHeader == "thuisteam" )
				oCell.style.textAlign = 'right';
			oCell.innerHTML = sHeader;
		}

		if ( oPoule.needsRanking() == false )
		{
			var oCell = oRowHeader.appendChild( document.createElement("th") );
			oCell.style.textAlign = 'center';
			oCell.innerHTML = m_jsonColumnGameId.header;
		}
	}

	function showPouleGames( oTable, oPoule )
	{
		var arrGames = oPoule.getGamesByDate();

		for ( var nJ = 0 ; nJ < arrGames.length ; nJ++ )
		{
			var oGame = arrGames[nJ];

			var oRow = oTable.insertRow( oTable.rows.length );

			var oCell = oRow.insertCell( oRow.cells.length );
			oCell.innerHTML = dateFormat( oGame.getStartDateTime(), 'dd mmm');

			oCell = oRow.insertCell( oRow.cells.length );
			oCell.innerHTML = dateFormat( oGame.getStartDateTime(), 'HH:MM');

			if ( m_jsonOptions["showCity"] == true )
			{
				oCell = oRow.insertCell( oRow.cells.length );
				oCell.className = "hidden-xs";
				oCell.innerHTML = oGame.getCity();
			}

			oCell = oRow.insertCell( oRow.cells.length );
			oCell.style.textAlign = 'right';
			createPoulePlaceControl( oCell, oGame.getHomePoulePlace(), true );

			oCell = oRow.insertCell( oRow.cells.length );
			oCell.align = "center";

			var oCellDescr = null;
			if ( oPoule.needsRanking() == false ) {
				oCell.align = "right";
				oCellDescr = oRow.insertCell( oRow.cells.length );
			}

			createResultControl( oCell, oCellDescr, oGame );

			oCell = oRow.insertCell( oRow.cells.length );
			createPoulePlaceControl( oCell, oGame.getAwayPoulePlace(), false );

			if ( oPoule.needsRanking() == false )
			{
				oCell = oRow.insertCell( oRow.cells.length );
				oCell.align = "center";
				oCell.innerHTML = VoetbalOog_Poule_Factory().getName( oPoule, false );
			}
		}
	}

	function showWinner( oTable, oPoule )
	{
		var oPoulePlaces = oPoule.getPlaces();

		for ( var nI in oPoulePlaces )
		{
			if ( !( oPoulePlaces.hasOwnProperty( nI ) ) )
				continue;

			var oPoulePlace = oPoulePlaces[nI];

			var oRow = oTable.insertRow( oTable.rows.length );

			var oCell = oRow.insertCell( oRow.cells.length );
			oCell.align = "center";
			createPoulePlaceControl( oCell, oPoulePlace, null );
		}
	}

	function getGamesColumns()
	{
		if ( m_arrColumnsGames == null )
		{
			m_arrColumnsGames = new Array( "datum", "tijd" );
			if ( m_jsonOptions["showCity"] == true )
				m_arrColumnsGames.push( "plaats" );
			m_arrColumnsGames.push( "thuisteam" );
			m_arrColumnsGames.push( "uitslag" );
			m_arrColumnsGames.push( "uitteam" );
			// m_arrColumnsGames["Id"] = 20; // alleen als er een volgende ronde is en minder dan 3 ploegen
		}
		return m_arrColumnsGames;
	}

	function createPoulePlaceControl( oTableCell, oPoulePlace, bReverse )
	{
		VoetbalOog_Control_Factory().appendPoulePlace( oTableCell, oPoulePlace, bReverse );
	}

	function createResultControl( oContainer, oContainerDescr, oGame )
	{
		var sTextNode = null, sTextNodeDescr = null;
		if ( oGame.getState() == g_jsonVoetbal.nState_Played )
		{
			if ( oGame.getHomeGoalsPenalty() > -1 ) {
				sTextNode = oGame.getHomeGoalsPenalty() + " - " + oGame.getAwayGoalsPenalty();
				sTextNodeDescr = "p";
			}
			else if ( oGame.getHomeGoalsExtraTime() > -1 ) {
				sTextNode = oGame.getHomeGoalsExtraTime() + " - " + oGame.getAwayGoalsExtraTime();
				sTextNodeDescr = "nv";
			}
			else if ( oGame.getHomeGoals() > -1 ) {
				sTextNode = oGame.getHomeGoals() + " - " + oGame.getAwayGoals();
			}
		}
		else
		{
			sTextNode = " - ";
		}
		oContainer.appendChild( document.createTextNode( sTextNode ) );
		if ( sTextNodeDescr != null && oContainerDescr != null )
			oContainerDescr.appendChild( document.createTextNode( sTextNodeDescr ) );
	}

	function updatePouleRank( oPoule )
	{
		var oContainer = document.getElementById( getPouleRankingDivId( oPoule ) );
		if ( oContainer == null )
			return;

		var jsonViewOptions = {};
		if ( m_jsonOptions["rankAbbreviation"] == true )
			jsonViewOptions["showAbbreviation"] = true;

        Ctrl_RankView().putQualifierLines( oPoule.getQualifierLines() );
        Ctrl_RankView().show( oContainer, oPoule.getGames(), oPoule.getRound().getCompetitionSeason().getPromotionRule(), jsonViewOptions );
	}
}

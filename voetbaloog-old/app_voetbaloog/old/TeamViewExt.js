function Ctrl_TeamViewExt( sDivId, oCompetitionSeason, oTeam, jsonOptions )
{
	var m_sDivId = sDivId;
	var m_oCompetitionSeason = oCompetitionSeason;
	// var m_oTeam = oTeam;
	var m_arrScheduledGames = null;
	var m_arrResultGames = null;
	var m_arrTopscorers = null;
	// var m_jsonTopScorers = null;
	var m_jsonOptions = jsonOptions;
	var m_nWidth = getDiv().offsetWidth;

	var m_bPoulesWithRankingNeeded = null;
	// var m_sPouleStandingsPrefix = '_standings_pouleid_';
	// var m_arrColumnsGames = null;
	// var m_arrColumnsStandings = null;

	function getDiv()
	{
		return document.getElementById( m_sDivId );
	}

	this.show = function()
	{
		var oDiv = getDiv();

		showSchedule( oDiv );

		oDiv.appendChild( document.createElement( 'hr' ) );

		// 		als de poules van de ranks zijn afgelopen dan rank en results omdraaien
		if ( poulesWithRankingNeeded() == true )
		{
			showResults( oDiv );
			oDiv.appendChild( document.createElement( 'hr' ) );
			showRanks( oDiv );
		}
		else
		{
			showRanks( oDiv );
			oDiv.appendChild( document.createElement( 'hr' ) );
			showResults( oDiv );
		}

		oDiv.appendChild( document.createElement( 'hr' ) );
		showTopscorers( oDiv );
	};

	function showSchedule( oContainer )
	{
		var arrScheduledGames = getScheduledGames();
		if ( arrScheduledGames.length == 0 )
			return;

		var oDivHeader = oContainer.appendChild( document.createElement( 'div' ) );
		oDivHeader.style.textAlign = 'center';
		oDivHeader.className = 'teamviewextheader';
		oDivHeader.style.width = m_nWidth + 'px';
		oDivHeader.appendChild( document.createTextNode( 'Programma' ) );

		var oDivSchedule = oContainer.appendChild( document.createElement( 'div' ) );

		oDivSchedule.style.height = '150px';
		oDivSchedule.style.overflowY = 'auto';
		// oDivSchedule.style.overflowX = 'hidden';
		oDivSchedule.style.marginBottom = '5px';
		oDivSchedule.style.fontSize = '10px';
		var sClassName = null;

		var sDate = null;
		for ( var nI in arrScheduledGames )
		{
			if ( !( arrScheduledGames.hasOwnProperty( nI ) ) )
				continue;

			sClassName = 'scheduledgamenonfirst';
			var oGame = arrScheduledGames[ nI ];

			var sGameDate = dateFormat( oGame.getStartDateTime(), 'niceDate' );
			// console.log( sGameDate );
			if ( oGame.getStartDateTime() == null )
				continue;

			if ( sDate != sGameDate )
			{
				var oDiv = oDivSchedule.appendChild( document.createElement( 'div' ) );
				oDiv.style.textAlign = 'center';
				oDiv.style.backgroundColor = 'lightgray';
				oDiv.style.width = ( m_nWidth - 20 ) + 'px';
				oDiv.appendChild( document.createTextNode( sGameDate ) );

				sClassName = 'scheduledgame';
			}

			VoetbalOog_Control_Factory().appendGame( oDivSchedule, oGame, sClassName  );

			sDate = sGameDate;
		}
	}

	// show ranks(alleen als ze er zijn )
	function showRanks( oContainer )
	{
		var oDivHeader = oContainer.appendChild( document.createElement( 'div' ) );
		oDivHeader.style.textAlign = 'center';
		oDivHeader.className = 'teamviewextheader';
		oDivHeader.style.width = m_nWidth + 'px';
		oDivHeader.style.marginBottom = '3px';
		oDivHeader.appendChild( document.createTextNode( 'Stand' ) );

		var nPromotionRule = m_oCompetitionSeason.getPromotionRule();

		var bShowAbreviation = false;
		if ( m_jsonOptions["rankAbbreviation"] == true )
			bShowAbreviation = true;
		var jsonViewOptions = { header : null, showAbbreviation : bShowAbreviation };

		var oRounds = m_oCompetitionSeason.getRounds();
		for ( var nI in oRounds )
		{
			if ( !( oRounds.hasOwnProperty( nI ) ) )
				continue;

			var oPoules = oRounds[nI].getPoules();
			for ( var nJ in oPoules )
			{
				if ( !( oPoules.hasOwnProperty( nJ ) ) )
					continue;

				var oPoule = oPoules[nJ];
				if ( oPoule.needsRanking() === false )
					continue;

				var oDiv = document.createElement( 'div' );
				oDiv.style.fontSize = '10px';
				oDiv.style.marginBottom = '3px';
				oContainer.appendChild( oDiv );

				var sPouleName = VoetbalOog_Poule_Factory().getName( oPoule, true );
				jsonViewOptions["header"] = sPouleName;

				Ctrl_RankView().show( oDiv, oPoule.getGames(), nPromotionRule, jsonViewOptions );
			}
		}
	}

	// show results( alleen als er al een gespeelde wedstrijd is )
	function showResults( oContainer )
	{
		var arrResultGames = getResultGames();
		if ( arrResultGames.length == 0 )
			return;

		var oDivHeader = oContainer.appendChild( document.createElement( 'div' ) );
		oDivHeader.style.textAlign = 'center';
		oDivHeader.className = 'teamviewextheader';
		oDivHeader.style.width = m_nWidth + 'px';
		oDivHeader.appendChild( document.createTextNode( 'Uitslagen' ) );

		var oDivResult = oContainer.appendChild( document.createElement( 'div' ) );

		oDivResult.style.overflowY = 'auto';
		oDivResult.style.marginBottom = '5px';
		oDivResult.style.fontSize = '10px';

		nHeight = 0;
		var sDate = null;
		for ( var nI in arrResultGames )
		{
			if ( !( arrResultGames.hasOwnProperty( nI ) ) )
				continue;

			var oGame = arrResultGames[ nI ];

			var sGameDate = dateFormat( oGame.getStartDateTime(), 'niceDate' );
			if ( sDate != sGameDate )
			{
				var oDiv = oDivResult.appendChild( document.createElement( 'div' ) );
				oDiv.style.textAlign = 'center';
				oDiv.style.backgroundColor = 'lightgray';
				oDiv.style.width = ( m_nWidth - 20 ) + 'px';
				oDiv.appendChild( document.createTextNode( sGameDate ) );
				nHeight += 16;
			}

			var sClassName = 'scheduledgame';

			var oGameDiv = oDivResult.appendChild( document.createElement( 'div' ) );
			VoetbalOog_Control_Factory().appendGame( oGameDiv, oGame, sClassName  );

			// should be using jquery/bootstrap here @TODO
			/*
			m_oPopup.connectDiv( oGameDiv, function( json )
				{
					while ( json.popup.body.hasChildNodes() )
						json.popup.body.removeChild( json.popup.body.lastChild );
					Ctrl_GameView().show( json.popup.body, json.game );
				},
				{ game : oGame, popup : m_oPopup }
			);
			*/

			nHeight += 16;

			sDate = sGameDate;
		}

		if ( nHeight > 100 )
			nHeight = 100;
		oDivResult.style.height = nHeight + 'px';
	}

	// show topscorers( alleen als er al een goal bekend is van een speler )
	function showTopscorers( oContainer )
	{
		var arrTopscorers = getSortedTopscorers();
		if ( arrTopscorers == null || arrTopscorers.length == 0 )
			return;

		var oDivHeader = oContainer.appendChild( document.createElement( 'div' ) );
		oDivHeader.style.textAlign = 'center';
		oDivHeader.className = 'teamviewextheader';
		oDivHeader.style.width = m_nWidth + 'px';
		oDivHeader.appendChild( document.createTextNode( 'Topscorers' ) );

		var nPlace = 1;
		var nPreviousGoals = -1;
		for ( var nI in arrTopscorers )
		{
			if ( !( arrTopscorers.hasOwnProperty( nI ) ) )
				continue;

			var oTopscorer = arrTopscorers[ nI ];
			if ( nPreviousGoals > oTopscorer.getNrOfGoalsTmp() )
				nPlace++;

			var oLineDiv = oContainer.appendChild( document.createElement( 'div' ) );
			oLineDiv.style.fontSize = '10px';
			oLineDiv.style.marginTop = '1px';
			showTopscorer( oLineDiv, oTopscorer, nPlace );

			nPreviousGoals = oTopscorer.getNrOfGoalsTmp();
		}
	}

	function showTopscorer( oContainer, oTopscorer, nPlace )
	{
		var oDiv = oContainer.appendChild( document.createElement( 'div' ) );
		oDiv.className = 'floatleft';
		oDiv.style.width = '20px';
		oDiv.style.backgroundColor = '#F0F0F0';
		oDiv.style.textAlign = 'right';
		oDiv.innerHTML = "" + nPlace;

		oDiv = oContainer.appendChild( document.createElement( 'div' ) );
		oDiv.style.width = ( m_nWidth - ( 20 + 1 + 21 ) ) + 'px';
		oDiv.className = 'floatleft';
		oDiv.style.backgroundColor = '#F0F0F0';
		oDiv.style.marginLeft = '1px';
		oDiv.innerHTML = oTopscorer.getFullName();

		oDiv = oContainer.appendChild( document.createElement( 'div' ) );
		oDiv.className = 'floatleft';
		oDiv.style.width = '20px';
		oDiv.style.backgroundColor = '#F0F0F0';
		oDiv.style.textAlign = 'right';
		oDiv.style.marginLeft = '1px';
		oDiv.innerHTML = oTopscorer.getNrOfGoalsTmp();

		oDiv = oContainer.appendChild( document.createElement( 'div' ) );
		oDiv.style.clear = 'both';
	}

	// loop door de poules en kijk als er poules zijn met meer dan 2 teams
	function poulesWithRankingNeeded()
	{
		if ( m_bPoulesWithRankingNeeded == null )
		{
			m_bPoulesWithRankingNeeded = false;

			var oRounds = m_oCompetitionSeason.getRounds();

			for ( var nI in oRounds )
			{
				if ( !( oRounds.hasOwnProperty( nI ) ) )
					continue;

				var oRound = oRounds[nI];

				if ( oRound.poulesNeedRanking() == true )
				{
					m_bPoulesWithRankingNeeded = true;
					return m_bPoulesWithRankingNeeded;
				}
			}
		}
		return m_bPoulesWithRankingNeeded;
	}

	function getScheduledGames()
	{
		return getSortedGames( true );
	}

	function getResultGames()
	{
		return getSortedGames( false );
	}

	function getSortedGames( bScheduled )
	{
		if ( m_arrScheduledGames == null && m_arrResultGames == null )
		{
			m_arrScheduledGames = new Array();
			m_arrResultGames = new Array();

			var oGames = m_oCompetitionSeason.getGames();
			for ( var nI in oGames )
			{
				if ( !( oGames.hasOwnProperty( nI ) ) )
					continue;

				var oGame = oGames[nI];
				if ( oGame.getState() == g_jsonVoetbal.nState_Played )
					m_arrResultGames.push( oGame );
				else
					m_arrScheduledGames.push( oGame );
			}

			function myCustomSort( oGameA, oGameB )
			{
				if ( oGameA.getState() === g_jsonVoetbal.nState_Played && oGameB.getState() == g_jsonVoetbal.nState_Played )
				{
					if ( ( oGameA.getStartDateTime() < oGameB.getStartDateTime() ) )
						return 1;
					else if ( ( oGameA.getStartDateTime() > oGameB.getStartDateTime() ) )
						return -1;
					else
						return ( oGameA.getViewOrder() < oGameB.getViewOrder() ) ? -1 : 1;
				}
				else
				{
					if ( ( oGameA.getStartDateTime() > oGameB.getStartDateTime() ) )
						return 1;
					else if ( ( oGameA.getStartDateTime() < oGameB.getStartDateTime() ) )
						return -1;
					else
						return ( oGameA.getViewOrder() < oGameB.getViewOrder() ) ? -1 : 1;
				}
			}

			m_arrScheduledGames.sort( myCustomSort );
			m_arrResultGames.sort( myCustomSort );
		}
		if ( bScheduled == true )
			return m_arrScheduledGames;
		return m_arrResultGames;
	}

	function getSortedTopscorers()
	{
		if ( m_arrTopscorers == null )
		{
			m_arrTopscorers = new Array();

			var oTopscorers = g_oCompetitionSeason.getTopscorers();

			for ( var nI in oTopscorers )
			{
				if ( !( oTopscorers.hasOwnProperty( nI ) ) )
					continue;


				m_arrTopscorers.push( oTopscorers[nI] );
			}

			m_arrTopscorers.sort(
				function ( oPersonA, oPersonB )
				{
					return ( oPersonA.getNrOfGoalsTmp() < oPersonB.getNrOfGoalsTmp() );
				}
			);
		}
		return m_arrTopscorers;
	}
}
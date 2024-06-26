function Ctrl_BetHelper( sDivId, sModalId )
{
	var m_sDivId = sDivId;
	var m_sModalId = sModalId;

	var m_oPreviousCompetitionSeasons = new Object();
	var m_oDivResults = null;
	var m_oResultsTable = null;
	var m_sTableClassName = 'table table-striped';
	var m_bHomeAdvantage = false;
	var m_arrPreviousResults = new Object();
	// var m_arrUniqueResults = new Object();

	this.getDiv = function()
	{
		return document.getElementById( m_sDivId );
	};

	this.showModal = function( oJQuery )
	{
		oJQuery('#' + m_sModalId ).modal( {} );
	};

	this.show = function()
	{
		this.initShow();
		this.getDiv().style.display = 'block';
	};

	this.initShow = function()
	{
		if ( m_oDivResults != null )
			return true;

		var oDiv = this.getDiv();

		m_oDivResults = DivHelper.create( oDiv.id, null, null, null, null );

		// create table
		{
			m_oResultsTable = document.createElement("table");
			m_oResultsTable.className = m_sTableClassName;
			m_oDivResults.appendChild( m_oResultsTable );
		}

		return true;
	};

	/*
	function getWidth( oContainer )
	{
		var nWidth = ( document.documentElement.clientWidth - oContainer.offsetWidth ) / 2;
		return nWidth;
	}*/

	/**
	 * Maak voor de previouscompetitions per previous comp an array
	 * dus [ WK2010 ] [0 - 0 ] = 12(%)
	 * 				  [0 - 1 ] = 11(%)
	 *
	 */
	function getPreviousResults( oRoundBetConfig )
	{
		var oRound = oRoundBetConfig.getRound();
		var nRoundNumber = oRound.getNumber();
		var nBetType = oRoundBetConfig.getBetType();

		// check existance or initialise array
		{
			if ( m_arrPreviousResults[ nRoundNumber ] == undefined )
				m_arrPreviousResults[ nRoundNumber ] = new Object();

			if ( m_arrPreviousResults[ nRoundNumber ][ nBetType ] == undefined )
				m_arrPreviousResults[ nRoundNumber ][ nBetType ] = new Object();
			else
				return m_arrPreviousResults[ nRoundNumber ][ nBetType ];
		}

		// fill previous results
		var arrPrevResults = m_arrPreviousResults[ nRoundNumber ][ nBetType ];
		arrPrevResults.unique = new Object();

		var oPreviousCompetitionSeasons = getPreviousCompetitionSeasons();
		for ( var nCSId in oPreviousCompetitionSeasons )
		{
			if ( !( oPreviousCompetitionSeasons.hasOwnProperty( nCSId ) ) )
				continue;

			arrPrevResults[nCSId] = new Object();
			var arrCSResults = arrPrevResults[nCSId];

			var oPreviousCompetitionSeason = oPreviousCompetitionSeasons[nCSId];

			var oRounds = oPreviousCompetitionSeason.getRounds();
			for ( var nRoundId in oRounds )
			{
				if ( !( oRounds.hasOwnProperty( nRoundId ) ) )
					continue;

				var oRoundIt = oRounds[nRoundId];
				if ( nRoundNumber != oRoundIt.getNumber() )
					continue;

				var oGames = oRoundIt.getGames();
				var nNrOfGames = 0;

				for ( var nGameId in oGames )
				{
					if ( !( oGames.hasOwnProperty( nGameId ) ) )
						continue;

					var oGame = oGames[nGameId];
					var sScoreId = getScoreId( oRoundBetConfig, oGame );
					if ( arrCSResults[sScoreId] == undefined )
						arrCSResults[sScoreId] = 0;

					arrCSResults[sScoreId]++;

					if ( arrPrevResults.unique[sScoreId] == undefined )
						arrPrevResults.unique[sScoreId] = new Ctrl_BetHelper_Range();

					nNrOfGames++;
				}

				if ( nNrOfGames == 0 )
					continue;

				// set percentages and low/high
				for ( var sScoreId in arrCSResults )
				{
					if ( !( arrCSResults.hasOwnProperty( sScoreId ) ) )
						continue;

					var sPercentage = arrCSResults[sScoreId] / nNrOfGames * 100;
					arrCSResults[sScoreId] = sPercentage;

					// check if sPercentage should be set as low and/or high
					var oUniquePreviousResult = arrPrevResults.unique[sScoreId];
					oUniquePreviousResult.total += sPercentage;
					if ( oUniquePreviousResult.low == null )
					{
						oUniquePreviousResult.low = sPercentage;
						oUniquePreviousResult.high = sPercentage;
					}
					else if ( sPercentage < oUniquePreviousResult.low )
						oUniquePreviousResult.low = sPercentage;
					else if ( sPercentage > oUniquePreviousResult.high )
						oUniquePreviousResult.high = sPercentage;
				}
			}
		}

		return getPreviousResults( oRoundBetConfig );
	}

	function getScoreId( oRoundBetConfig, oGameBet )
	{
		var nBetType = oRoundBetConfig.getBetType();
		if ( nBetType == VoetbalOog_Bet_Score.nId )
		{
			if ( m_bHomeAdvantage == false && oGameBet.getAwayGoals() > oGameBet.getHomeGoals() )
				return oGameBet.getAwayGoals() + '-' + oGameBet.getHomeGoals();

			return oGameBet.getHomeGoals() + '-' + oGameBet.getAwayGoals();
		}
		else if ( nBetType == VoetbalOog_Bet_Result.nId )
		{
			if ( oGameBet.getResult() == 0 )
				return 'Gelijk';

			if ( m_bHomeAdvantage == true )
			{
				if ( oGameBet.getResult() == -1 )
					return 'Uit';
				if ( oGameBet.getResult() == 1 )
					return 'Thuis';
			}
			else if ( oGameBet.getResult() == -1 || oGameBet.getResult() == 1 )
				return 'Win/Ver';
		}
		throw "no bettype";
	}

	function getUniquePreviousResults( oRoundBetConfig )
	{
		return getPreviousResults( oRoundBetConfig ).unique;
	}

	function getPoolUserResults( oRoundBetConfig, oPoolUser, arrUniqueResults )
	{
		var oPoolUserResults = new Object();

		var oBets = oPoolUser.getBets( oRoundBetConfig );
		for ( var nId in oBets )
		{
			if ( !( oBets.hasOwnProperty( nId ) ) )
				continue;

			var oBet = oBets[nId];
			if ( oBet != null )
			{
				var bIsBetCorrect = isBetCorrect( oRoundBetConfig, oBet );
				if ( !bIsBetCorrect )
					continue;

				var sScoreId = getScoreId( oRoundBetConfig, oBet );

				if ( oPoolUserResults[sScoreId] == undefined )
					oPoolUserResults[sScoreId] = 0;

				oPoolUserResults[sScoreId]++;

				if ( arrUniqueResults[sScoreId] == undefined )
					arrUniqueResults[sScoreId] = new Ctrl_BetHelper_Range();
			}
		}

		var nNrOfGames = oRoundBetConfig.getRound().getGamesByDate().length;
		if ( nNrOfGames > 0 )
		{
			// set percentages and low/high
			for ( var sScoreId in oPoolUserResults )
			{
				if ( !( oPoolUserResults.hasOwnProperty( sScoreId ) ) )
					continue;

				var sPercentage = oPoolUserResults[sScoreId] / nNrOfGames * 100;
				oPoolUserResults[sScoreId] = sPercentage;

				var oUniquePreviousResult = arrUniqueResults[sScoreId];
				oUniquePreviousResult.value = sPercentage;
			}
		}

		// return oPoolUserResults;
	}

	function isBetCorrect( oRoundBetConfig, oBet )
	{
		if ( oRoundBetConfig.getBetType() == VoetbalOog_Bet_Score.nId )
		{
			if ( oBet.getHomeGoals() >= 0 && oBet.getAwayGoals() >= 0 )
				return true;
		}
		else if ( oRoundBetConfig.getBetType() == VoetbalOog_Bet_Result.nId )
		{
			if ( oBet.getResult() >= -1 && oBet.getResult() <= 1 )
				return true;
		}
		return false;
	}

	function getPreviousCompetitionSeasons()
	{
		return m_oPreviousCompetitionSeasons;
	}

	this.putPreviousCompetitionSeasons = function( oPreviousCompetitionSeasons )
	{
		m_oPreviousCompetitionSeasons = oPreviousCompetitionSeasons;

		m_arrPreviousResults = new Object();
	};

	function setUniqueResults( oRoundBetConfig, arrUniqueResults )
	{
		var arrUniquePreviousResults = getUniquePreviousResults( oRoundBetConfig );
		for ( var sScoreId in arrUniquePreviousResults )
		{
			if ( !( arrUniquePreviousResults.hasOwnProperty( sScoreId ) ) )
				continue;

			if ( arrUniqueResults[sScoreId] == undefined )
				arrUniqueResults[sScoreId] = arrUniquePreviousResults[sScoreId];
			else
			{
				arrUniqueResults[sScoreId].low = arrUniquePreviousResults[sScoreId].low;
				arrUniqueResults[sScoreId].high = arrUniquePreviousResults[sScoreId].high;
			}
		}
	}

	this.refresh = function( oRoundBetConfig, oPoolUser, oPoint )
	{
		if ( this.initShow() == false )
			return;

		while ( m_oResultsTable.rows.length > 0 )
			m_oResultsTable.deleteRow( m_oResultsTable.rows.length - 1 );

		var oPreviousCompetitionSeasons = getPreviousCompetitionSeasons();

		addTableHeaders( oPreviousCompetitionSeasons );

		if ( oPoint != null )
		{
			this.getDiv().style.left = oPoint.x + 'px';
			this.getDiv().style.top = oPoint.y + 'px';
		}

		// var nMargin = 5;
		var arrPreviousResults = getPreviousResults( oRoundBetConfig );

		var arrUniqueResults = new Object();
		getPoolUserResults( oRoundBetConfig, oPoolUser, arrUniqueResults );
		setUniqueResults( oRoundBetConfig, arrUniqueResults );
		var arrOrderedUniqueResults = getOrderedUniqueResults( arrUniqueResults );

		for ( var nI = 0 ; nI < arrOrderedUniqueResults.length ; nI++ )
		{
			var sScoreId = arrOrderedUniqueResults[ nI ].score;
			var oUniqueResult = arrUniqueResults[sScoreId];

			var nInRange = oUniqueResult.inRange();

			var oRow = m_oResultsTable.insertRow( m_oResultsTable.rows.length );

			var oCell = oRow.insertCell( oRow.cells.length );
			oCell.style.textAlign = "center";
			oCell.style.fontWeight = "bold";
			oCell.innerHTML = sScoreId;

			oCell = oRow.insertCell( oRow.cells.length );
			oCell.style.textAlign = "right";
			oCell.noWrap = true;
			if ( nInRange != 0 )
				oCell.className = 'danger';
			else
				oCell.className = 'success';

			if ( nInRange != 0 )
			{
				var oSpan = document.createElement( "span" );
				if ( nInRange == 1 )
					oSpan.className = "glyphicon glyphicon-circle-arrow-down";
				else // == -1
					oSpan.className = "glyphicon glyphicon-circle-arrow-up";

				oCell.appendChild( oSpan );
				oCell.appendChild( document.createTextNode( "  " ) );
			}
			oCell.appendChild( document.createTextNode( Math.round( oUniqueResult.value ) + "%" ) );

			for ( var nId in oPreviousCompetitionSeasons )
			{
				if ( !( oPreviousCompetitionSeasons.hasOwnProperty( nId ) ) )
					continue;

				var oPreviousCompetitionSeason = oPreviousCompetitionSeasons[nId];

				var arrCSResults = arrPreviousResults[ oPreviousCompetitionSeason.getId() ];

				var nValue = arrCSResults[sScoreId];
				if ( nValue == undefined )
					nValue = 0;

				oCell = oRow.insertCell( oRow.cells.length );
				oCell.style.textAlign = "right";
				oCell.appendChild( document.createTextNode( Math.round( nValue ) + "%" ) );
			}
		}
		// this.show();
	};

	function addTableHeaders( oPreviousCompetitionSeasons )
	{
		// create header
		{
			var oRow = m_oResultsTable.insertRow( m_oResultsTable.rows.length );
			oRow.className = 'tableheader';
			oRow.style.fontWeight = "bold";

			var oCell = oRow.appendChild( document.createElement("th") );
			oCell.style.textAlign = "center";
			oCell.innerHTML = 'uitslag';

			oCell = oRow.appendChild( document.createElement("th") );
			oCell.style.textAlign = "right";
			oCell.appendChild( document.createTextNode( 'mijn' ) );

			var oPreviousCompetitionSeasons = getPreviousCompetitionSeasons();
			for ( var nI in oPreviousCompetitionSeasons )
			{
				if ( !( oPreviousCompetitionSeasons.hasOwnProperty( nI ) ) )
					continue;

				var oPreviousCompetitionSeason = oPreviousCompetitionSeasons[nI];
				var sAbbreviation = oPreviousCompetitionSeason.getAbbreviation();
				oCell = oRow.appendChild( document.createElement("th") );
				oCell.style.textAlign = "right";

				if ( sAbbreviation != undefined )
					oCell.innerHTML = oPreviousCompetitionSeason.getAbbreviation().toLowerCase();
			}
		}
	}

	function getOrderedUniqueResults( arrUniqueResults )
	{
		var arrOrderedUniqueResults = new Array();

		for ( var sScoreId in arrUniqueResults )
		{
			if ( !( arrUniqueResults.hasOwnProperty( sScoreId ) ) )
				continue;

			var oUniqueResult = arrUniqueResults[sScoreId];
			arrOrderedUniqueResults.push( { score : sScoreId, total : oUniqueResult.total } );
		}

		arrOrderedUniqueResults.sort(
			function ( jsonResultA, jsonResultB )
			{
				if ( ( jsonResultA.total > jsonResultB.total ) )
					return 1;
				return -1;
			}
		);

		return arrOrderedUniqueResults;
	}
}

function Ctrl_BetHelper_Range()
{
	this.value = 0;
	this.total = 0;
	this.low = null;
	this.high = null;

	var m_nRange = 5;

	/**
	 * Wanneer value meer als 5% onder de laagste uitslag zit of
	 * value meer als 5% boven de hoogste uitslag zit ( -1, 0, 1 )
	 */
	this.inRange = function()
	{
		var nLow = this.low;
		if ( this.low == null )
			nLow = this.low;

		var nHigh = this.high;
		if ( this.high == null )
			nHigh = 0;

		if ( this.value < ( nLow - m_nRange ) )
			return -1;

		if ( this.value > ( nHigh + m_nRange ) )
			return 1;

		return 0;
	};
}

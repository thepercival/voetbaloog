function Ctrl_RankView()
{
	var m_sTableClassName = 'table table-striped';

	var instance = (function()
	{
		var m_arrColumns = new Array( "nr", "team", "g", "p", "v", "t" );
		var m_jsonQualifierLines = null;

		function getColumns()
		{
			return m_arrColumns;
		}

		function showHeaders( oTable, sHeaderParam )
		{
			var arrColumns = getColumns();

			if ( sHeaderParam != null )
			{
				var oRowHeader = oTable.insertRow( oTable.rows.length );
				oRowHeader.className = 'tableheader';
				var oCell = document.createElement("th");
				oCell.colSpan = arrColumns.length;
				oCell.innerHTML = sHeaderParam;
				oRowHeader.appendChild( oCell );
			}

			var oRowHeader = oTable.insertRow( oTable.rows.length );
			oRowHeader.className = 'tableheader';
			for ( var nI = 0 ; nI < arrColumns.length ; nI++ )
			{
				var sHeader = arrColumns[nI];

				var oCell = document.createElement("th");
				if ( sHeader != "team") { oCell.style.textAlign = 'right'; }
				else { oCell.style.textAlign = 'left'; }
				oCell.innerHTML = sHeader;
				oRowHeader.appendChild( oCell );
			}
		}

		return {
			show: function ( oContainer, oGames, nPromotionRule, jsonViewOptions ) {
				if (oContainer == null)
					return;

				while (oContainer.hasChildNodes())
					oContainer.removeChild(oContainer.lastChild);

				var oTable = document.createElement("table");
				oTable.className = m_sTableClassName;

				var bAbbreviation = false;
				if (jsonViewOptions["showAbbreviation"] != undefined)
					bAbbreviation = jsonViewOptions["showAbbreviation"];

				var sHeader = jsonViewOptions["header"];
				showHeaders(oTable, sHeader);

				var nNrOfHeadCols = 1;
				if (sHeader != undefined)
					nNrOfHeadCols++;

				var jsonQualifierLines = this.getQualifierLines();

				// get games from form and show the places!!!!
				var oRanking = new VoetbalOog_Ranking( nPromotionRule );
				oRanking.updatePoulePlaceRankings( oGames, null );
				var arrRankedPoulePlaces = oRanking.getPoulePlacesByRanking( oGames, null );
				//console.log(arrRankedPoulePlaces);

				var bQualifySingle = false; var bQualifyMulti = false;
				var bPreviousQualifySingle = false; var bPreviousQualifyMulti = false;
				var oRow = null;
				var nNr = 1;
				var sPenaltyPoints = "", nNrOfPenPoints = 0;
				for ( var nI in arrRankedPoulePlaces )
				{
					if ( !( arrRankedPoulePlaces.hasOwnProperty( nI ) ) )
						continue;

					var oRankedPoulePlace = arrRankedPoulePlaces[nI];

					oRow = oTable.insertRow( oTable.rows.length );

					if ( jsonQualifierLines != null && nNr == ( jsonQualifierLines.single + 1 ) ) // + 1 because of top style instead of bottom style
						oRow.className = "qualifyline-single";
					else if ( jsonQualifierLines != null && nNr == ( jsonQualifierLines.multi + 1 ) ) // + 1 because of top style instead of bottom style
						oRow.className = "qualifyline-multi";

					if ( bAbbreviation == true )
					{
						// shoul be using bootstrap/jquery here @TODO
						/*
						m_oPopup.connectDiv( oRow, function( json )
							{
								while ( json.popup.body.hasChildNodes() )
									json.popup.body.removeChild( json.popup.body.lastChild );
								json.popup.body.appendChild( document.createTextNode( json.pouleplace.getTeam().getName() ) );
							},
							{ pouleplace : oRankedPoulePlace, popup : m_oPopup }
						);
						*/
					}

					var oCell = oRow.insertCell( oRow.cells.length );
					oCell.align = 'right';
					oCell.innerHTML = nNr++;

					oCell = oRow.insertCell( oRow.cells.length );
					oCell.align = 'left';
					VoetbalOog_Control_Factory().appendPoulePlace( oCell, oRankedPoulePlace, false, bAbbreviation );
					if ( oRankedPoulePlace.getPenaltyPoints() > 0 )
					{
						nNrOfPenPoints++;

						if ( sPenaltyPoints.length > 0 )
							sPenaltyPoints += ", ";

						var sPenPointsTmp = "";
						for ( var nJ = 0 ; nJ < nNrOfPenPoints ; nJ++ )
							sPenPointsTmp += "*";
						sPenaltyPoints += sPenPointsTmp;

						sPenaltyPoints += " = -" + oRankedPoulePlace.getPenaltyPoints() + "p";

						oCell.appendChild( document.createTextNode( sPenPointsTmp ) );
					}

					oCell = oRow.insertCell( oRow.cells.length );
					oCell.align = 'right';
					oCell.innerHTML = oRankedPoulePlace.getNrOfPlayedGames( oGames );

					oCell = oRow.insertCell( oRow.cells.length );
					oCell.align = 'right';
					oCell.innerHTML = oRankedPoulePlace.getPoints( oGames ) - oRankedPoulePlace.getPenaltyPoints();

					oCell = oRow.insertCell( oRow.cells.length );
					oCell.align = 'right';
					oCell.innerHTML = oRankedPoulePlace.getNrOfGoalsScored( oGames );

					oCell = oRow.insertCell( oRow.cells.length );
					oCell.align = 'right';
					oCell.innerHTML = oRankedPoulePlace.getNrOfGoalsReceived( oGames );
				}

				if ( sPenaltyPoints.length > 0 )
				{
					oRow = oTable.insertRow( oTable.rows.length );

					var oCell = oRow.insertCell( oRow.cells.length );
					oCell.colSpan = getColumns().length;
					oCell.innerHTML = sPenaltyPoints;
				}

				oContainer.appendChild( oTable );
			}
			,
			getQualifierLines : function() {
				return m_jsonQualifierLines;
			}
			,
			putQualifierLines : function( jsonQualifierLines ) {
				m_jsonQualifierLines = jsonQualifierLines;
			}
		};
	})();

	Ctrl_RankView = function ()
	{
		// re-define the function for subsequent calls
		return instance;
	};

	return Ctrl_RankView(); // call the new function
}

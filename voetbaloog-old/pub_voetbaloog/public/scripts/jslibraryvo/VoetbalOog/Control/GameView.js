function Ctrl_GameView()
{
	var instance = (function()
	{
		// var m_nTeamWidth = 180;

		function showHeader( oTable, oGame )
		{
			var oRow = oTable.insertRow( oTable.rows.length );
			oRow.noWrap = true;

			var oCell = oRow.appendChild( document.createElement("td") );
			oCell.colSpan = 3;
			oCell.style.textAlign = 'right';
			oCell.innerHTML = oGame.getCity() + ", " + dateFormat( oGame.getStartDateTime(), 'niceDate' );

			oRow = oTable.insertRow( oTable.rows.length );
			oRow.noWrap = true;

			oCell = oRow.appendChild( document.createElement("td") );
			oCell.noWrap = true;
			VoetbalOog_Control_Factory().appendPoulePlace( oCell, oGame.getHomePoulePlace(), true, false );

			oCell = oRow.appendChild( document.createElement("td") );
			oCell.noWrap = true;
			oCell.innerHTML = oGame.getHomeGoalsCalc( true ) + ' - ' + oGame.getAwayGoalsCalc( true );

			oCell = oRow.appendChild( document.createElement("td") );
			oCell.noWrap = true;
			VoetbalOog_Control_Factory().appendPoulePlace( oCell, oGame.getAwayPoulePlace(), false, false );
		}

		function showGoals( oTable, oGame )
		{
			var oGoals = oGame.getGoals();
			if ( Object_Factory().count( oGoals ) == 0 )
				return;

			var oHomeTeam = oGame.getHomePoulePlace().getTeam();
			var oAwayTeam = oGame.getAwayPoulePlace().getTeam();
			if ( oHomeTeam == null || oAwayTeam == null )
				return;

			var oRow = oTable.insertRow( oTable.rows.length );
			oCell = oRow.appendChild( document.createElement("td") );
			oCell.colSpan = 3;
			oCell.appendChild( document.createElement("hr") );

			var arrGoals = new Array();
			for ( var nI in oGoals )
			{
				if ( !( oGoals.hasOwnProperty( nI ) ) )
					continue;

				arrGoals.push( oGoals[nI] );
			}

			var nInsertPoint = oTable.rows.length;
			var nHomeGoal = 0; var nAwayGoal = 0;
			for ( var nI in arrGoals )
			{
				if ( !( arrGoals.hasOwnProperty( nI ) ) )
					continue;

				var oGoal = arrGoals[nI];

				// do stuff
				oRow = oTable.insertRow( nInsertPoint );
				oRow.noWrap = true;
/*
				var nHomeGoalIt = oGoal.getHomeGoal();
				var nAwayGoalIt = oGoal.getAwayGoal();

				if ( nHomeGoalIt == ( nHomeGoal + 1 ) && nAwayGoalIt == nAwayGoal ) // home goal
				{
					oCell = oRow.appendChild( document.createElement("td") );
				    var oTeamMembershipPlayer = oGoal.getTeamMembershipPlayer();
					oCell.style.textAlign = 'right';
					var sFullName = '';
					if ( oTeamMembershipPlayer != null && oTeamMembershipPlayer.getClient() != null )
						sFullName = oTeamMembershipPlayer.getClient().getFullName();
					oCell.innerHTML = sFullName + ' ' + oGoal.getMinute() + '\'';

					oCell = oRow.appendChild( document.createElement("td") );
					oCell.appendChild( VoetbalOog_Control_Factory().createGoalImage( oGoal ) );

					oCell = oRow.appendChild( document.createElement("td") );

					nHomeGoal++;
				}
				else if ( nHomeGoalIt == nHomeGoal && nAwayGoalIt == ( nAwayGoal + 1 ) ) // away goal
				{
					oCell = oRow.appendChild( document.createElement("td") );

					oCell = oRow.appendChild( document.createElement("td") );
					var oImg = VoetbalOog_Control_Factory().createGoalImage( oGoal );
					oImg.align = 'right';
					oCell.appendChild( oImg );

					oCell = oRow.appendChild( document.createElement("td") );
				    var oTeamMembershipPlayer = oGoal.getTeamMembershipPlayer();
					var sFullName = '';
					if ( oTeamMembershipPlayer != null && oTeamMembershipPlayer.getClient() != null )
						sFullName = oTeamMembershipPlayer.getClient().getFullName();
					oCell.innerHTML = oGoal.getMinute() + '\' ' + sFullName;

					nAwayGoal++;
				}
				*/
			}
		}

		function showParticipations( oTable, oGame )
		{
			var oHomeTeam = oGame.getHomePoulePlace().getTeam();
			var oAwayTeam = oGame.getAwayPoulePlace().getTeam();
			if ( oHomeTeam == null || oAwayTeam == null )
				return;

			var oHomeParticipations = oGame.getParticipations( oHomeTeam );
			var oAwayParticipations = oGame.getParticipations( oAwayTeam );
			if ( Object_Factory().count( oHomeParticipations ) == 0 || Object_Factory().count( oAwayParticipations ) == 0 )
				return;

			var oRow = oTable.insertRow( oTable.rows.length );
			oCell = oRow.appendChild( document.createElement("td") );
			oCell.colSpan = 3;
			oCell.appendChild( document.createElement("hr") );

			var arrPartTmpHome = new Array();
			var arrPartTmpAway = new Array();

			for ( var nI in oHomeParticipations )
			{
				if ( !( oHomeParticipations.hasOwnProperty( nI ) ) )
					continue;

				arrPartTmpHome.push( oHomeParticipations[nI] );
			}
			for ( var nI in oAwayParticipations )
			{
				if ( !( oAwayParticipations.hasOwnProperty( nI ) ) )
					continue;

				arrPartTmpAway.push( oAwayParticipations[nI] );
			}

			var nI = 0;
			while( arrPartTmpHome[nI] != undefined || arrPartTmpAway[nI] != undefined )
			{
				var oHomeParticipation = arrPartTmpHome[nI];
				var oAwayParticipation = arrPartTmpAway[nI];

				// do stuff
				oRow = oTable.insertRow( oTable.rows.length );
				oRow.noWrap = true;

				oCell = oRow.appendChild( document.createElement("td") );
			    if ( oHomeParticipation != undefined )
				{
					var oTeamMembershipPlayer = oHomeParticipation.getTeamMembershipPlayer();
					oCell.style.textAlign = 'right';
					oCell.innerHTML = oTeamMembershipPlayer.getClient().getFullName() + ' ' + oTeamMembershipPlayer.getBackNumber();
				}

				oCell = oRow.appendChild( document.createElement("td") );

				oCell = oRow.appendChild( document.createElement("td") );
			    if ( oAwayParticipation != undefined )
				{
			    	var oTeamMembershipPlayer = oAwayParticipation.getTeamMembershipPlayer();
					oCell.innerHTML = oTeamMembershipPlayer.getBackNumber() + ' ' + oTeamMembershipPlayer.getClient().getFullName();
				}

				nI++;
			}
		}

		return {
			show: function ( oContainer, oGame )
			{
				if ( oContainer == null || oGame.getState() != g_jsonVoetbal.nState_Played )
					return;

				while ( oContainer.hasChildNodes() )
					oContainer.removeChild( oContainer.lastChild );

				var oTable = document.createElement("table");
				oTable.className = "tableGame";

				showHeader( oTable, oGame );

				showGoals( oTable, oGame );

				showParticipations( oTable, oGame );

				oContainer.appendChild( oTable );
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

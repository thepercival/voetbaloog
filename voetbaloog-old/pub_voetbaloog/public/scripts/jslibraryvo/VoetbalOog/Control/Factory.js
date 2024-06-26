function VoetbalOog_Control_Factory()
{
	var instance = (function()
	{
		return {
			createImage: function ( oPoolUser )
			{
				var sSrc = null;
				{
					if ( oPoolUser.getRanking() == 1 )
						sSrc = g_sPubMap + "public/images/ranking/rank_1.png";
					else if ( oPoolUser.getRanking() == 2 )
						sSrc = g_sPubMap + "public/images/ranking/rank_2.png";
					else if ( oPoolUser.getRanking() == 3 )
						sSrc = g_sPubMap + "public/images/ranking/rank_3.png";
					else if ( oPoolUser.getRanking() == oPoolUser.getPool().getLastRanking() )
						sSrc = g_sPubMap + "public/images/ranking/rank_0.png";
				}

				var oImg = null;
				if ( sSrc != null )
				{
					oImg = document.createElement( "img" );
					oImg.src = sSrc;
					oImg.height = "9";
					oImg.width = "9";
				}
				return oImg;
			},

			createGoalImage: function ( oGoal )
			{
				var sSrc = null;
				{
					if ( oGoal.getPenalty() == true )
						sSrc = g_sPubMap + "public/images/gamedetail/penalty.gif";
					else if ( oGoal.getOwnGoal() == true )
						sSrc = g_sPubMap + "public/images/gamedetail/owngoal.gif";
					else
						sSrc = g_sPubMap + "public/images/gamedetail/goal.gif";
				}

				var oImg = null;
				if ( sSrc != null )
				{
					oImg = document.createElement( "img" );
					oImg.src = sSrc;
					// oImg.height = "9";
					// oImg.width = "9";
				}
				return oImg;
			},

			appendPoulePlace: function ( oContainer, oPoulePlace, bReverse, bAbbreviation )
			{
				var oTeam = oPoulePlace.getTeam();
				if ( oTeam != null )
				{
					this.appendTeam( oContainer, oTeam, bReverse, bAbbreviation, false, false );
				}
				else
				{
					var oFromQualifyRule = oPoulePlace.getFromQualifyRule();
					if ( oFromQualifyRule != null )
					{
						oContainer.style.lineHeight = '16px';
						oContainer.appendChild( document.createTextNode( VoetbalOog_PoulePlace_Factory().getName( oPoulePlace ) ) );
					}
				}
			},

			appendTeam: function ( oContainer, oTeam, bReverse, bAbbreviation, bLineThrough, bLineBreak )
			{
				if ( oTeam != null )
				{
					var sTeam = null;
					if ( bAbbreviation == true )
						sTeam = oTeam.getAbbreviation().toUpperCase();
					else
						sTeam = oTeam.getName();

					var oSpan = oContainer.appendChild( document.createElement('span') );
					oSpan.style.lineHeight = '16px';
					oSpan.style.height = '16px';

					var oSpanImg = document.createElement('span');
					oSpanImg.style.display = "inline-block";
					oSpanImg.className = "spriteteam-16 sprite-" + oTeam.getImageName() + "-16";
					oSpanImg.innerHTML = "&nbsp;";

					var oSpanText = oSpan.appendChild( document.createElement('span') );
					if ( bLineThrough == true )
						oSpanText.style.textDecoration = 'line-through';
					oSpanText.style.display = "inline-block";
					oSpanText.appendChild( document.createTextNode( sTeam ) );

					if ( bReverse == true )
					{
						oSpan.appendChild( oSpanText );
						if ( bLineBreak == true )
							oSpan.appendChild( document.createElement("br") );
						oSpan.appendChild( oSpanImg );
					}
					else
					{
						oSpan.appendChild( oSpanImg );
						if ( bLineBreak == true )
							oSpan.appendChild( document.createElement("br") );
						oSpan.appendChild( oSpanText );
					}
				}
			},

			appendGame: function ( oContainer, oGame, sClassName )
			{
				if ( oGame != null )
				{
					var oDiv = oContainer.appendChild( document.createElement( 'div' ) );
					if ( oGame.getState() != g_jsonVoetbal.nState_Played )
					{
						oDiv.className = sClassName;
						oDiv.style.width = '40px';
						oDiv.style.lineHeight = '16px';
						oDiv.appendChild( document.createTextNode( dateFormat( oGame.getStartDateTime(), 'shortTime' ) ) );

						var oDiv = oContainer.appendChild( document.createElement( 'div' ) );
						oDiv.className = sClassName;
						oDiv.style.width = '60px';
						oDiv.style.textAlign = 'right';
						VoetbalOog_Control_Factory().appendPoulePlace( oDiv, oGame.getHomePoulePlace(), true, true );

						oDiv = oContainer.appendChild( document.createElement( 'div' ) );
						oDiv.className = sClassName;
						oDiv.style.lineHeight = '16px';
						oDiv.innerHTML = '&nbsp;-&nbsp;';

						oDiv = oContainer.appendChild( document.createElement( 'div' ) );
						oDiv.className = sClassName;
						oDiv.style.width = '60px';
						oDiv.style.textAlign = 'left';
						VoetbalOog_Control_Factory().appendPoulePlace( oDiv, oGame.getAwayPoulePlace(), false, true );
					}
					else
					{
						oDiv.className = sClassName;
						oDiv.style.width = '60px';
						oDiv.style.textAlign = 'right';
						VoetbalOog_Control_Factory().appendPoulePlace( oDiv, oGame.getHomePoulePlace(), true, true );

						oDiv = oContainer.appendChild( document.createElement( 'div' ) );
						oDiv.className = sClassName;
						oDiv.style.lineHeight = '16px';
						VoetbalOog_Control_Factory().appendScore( oDiv, oGame );
						/*
						oDiv.style.width = '20px';
						oDiv.style.lineHeight = '16px';
						oDiv.appendChild( document.createTextNode( dateFormat( oGame.getStartDateTime(), 'shortTime' ) ) );
						 */

						oDiv = oContainer.appendChild( document.createElement( 'div' ) );
						oDiv.className = sClassName;
						oDiv.style.width = '60px';
						oDiv.style.textAlign = 'left';
						VoetbalOog_Control_Factory().appendPoulePlace( oDiv, oGame.getAwayPoulePlace(), false, true );
					}
					oDiv = oContainer.appendChild( document.createElement( 'div' ) );
					oDiv.style.clear = 'both';
				}
			},

			appendScore: function ( oContainer, oGame )
			{
				var sTextNode = null;
				if ( oGame.getState() == g_jsonVoetbal.nState_Played )
				{
					if ( oGame.getHomeGoalsPenalty() > -1 )
						sTextNode = oGame.getHomeGoalsPenalty() + " - " + oGame.getAwayGoalsPenalty() + " (pn)";
					else if ( oGame.getHomeGoalsExtraTime() > -1 )
						sTextNode = oGame.getHomeGoalsExtraTime() + " - " + oGame.getAwayGoalsExtraTime() + " (nv)";
					else if ( oGame.getHomeGoals() > -1 )
						sTextNode = oGame.getHomeGoals() + " - " + oGame.getAwayGoals();
				}
				else
				{
					sTextNode = " - ";
				}
				oContainer.appendChild( document.createTextNode( sTextNode ) );
			}
		};
	})();

	VoetbalOog_Control_Factory = function ()
	{
		// re-define the function for subsequent calls
		return instance;
	};

	return VoetbalOog_Control_Factory(); // call the new function
}
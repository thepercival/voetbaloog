function Ctrl_BetView_Matrix_Games( oParent ) 
{	
	var m_oParent = oParent;
	var m_oDiv = null;
	var that = this;
	var m_sControlPrefix = 'ctrl_betview_matrix_games_';
	
	var m_nWidthId = 20;
	var m_nWidthHome = 55;
	var m_nWidthAway = 55;
	var m_nWidthScore = 45;
	
	function getDiv()
	{
		return m_oDiv;
	}
	
	this.getParent = function()
	{
		return m_oParent;
	};
	
	this.getWidth =function()
	{
		var nUnitMargin = m_oParent.getUnitMargin();
		return nUnitMargin + m_nWidthId + nUnitMargin + m_nWidthHome 
			+ nUnitMargin + m_nWidthAway + nUnitMargin + m_nWidthScore;
	};
	
	this.show = function()
	{
		if ( m_oDiv == null )
		{
			m_oDiv = DivHelper.create( m_oParent.getDiv(), null, 'floatleft', null, null );			
			
			showHeaders( m_oDiv );
			
			var oNow = new Date();
			var oPool = m_oParent.getParent().getPool();
			var oCompetitionSeason = oPool.getCompetitionSeason();
			var oRounds = oCompetitionSeason.getRounds();
			for ( var nI in oRounds )
			{
				if ( !( oRounds.hasOwnProperty( nI ) ) )
					continue;
				
				var oRound = oRounds[nI];
				var oRoundBetConfigs = oPool.getBetConfigs( oRound );
				
				var oRoundBetConfigResultExtra = null;
				{
					var oRoundBetConfigScore = oRoundBetConfigs[ VoetbalOog_Bet_Score.nId ];
					if ( oRoundBetConfigScore != null )
						oRoundBetConfigResultExtra = oRoundBetConfigs[ VoetbalOog_Bet_Result.nId ];
				}
				
				for ( var nJ in oRoundBetConfigs )
				{
					if ( !( oRoundBetConfigs.hasOwnProperty( nJ ) ) )
						continue;
					
					var oRoundBetConfig = oRoundBetConfigs[nJ];
					
					if ( oRoundBetConfigResultExtra == oRoundBetConfig )
			    		continue;
			    	
					var oRoundBetConfigDiv = DivHelper.create( m_oDiv, null, null, null, null );
					oRoundBetConfigDiv.id = this.getRoundBetConfigDivId( oRoundBetConfig, null );
					oRoundBetConfigDiv.style.backgroundColor = 'white';
					oRoundBetConfigDiv.style.borderBottom = '1px solid #135113';
					
					showRoundBetConfig( oRoundBetConfigDiv, oRoundBetConfig, oRoundBetConfigResultExtra );
					
					// Wanneer de roundbetconfig in totaal al geweest is, moet deze dichtgeklapt zijn
					var bExpand = true;
					if ( oRoundBetConfig.getRound().getEndDateTime() < oNow )
						bExpand = false;
					that.refreshRoundBetConfig( oRoundBetConfig, bExpand );
				}			
			}			
		}
	};

	function showHeaders( oParentDiv )
	{
		var oHeadersDiv = DivHelper.create( m_oDiv, null, null, null, null );
		oHeadersDiv.style.backgroundColor = 'white';	
		
		var nUnitMargin = m_oParent.getUnitMargin();
		
		var oDiv = DivHelper.create( oHeadersDiv, null, 'headerAllBets', null, null );
		oDiv.style.width = m_nWidthId + nUnitMargin + m_nWidthHome + nUnitMargin 
						+ m_nWidthAway + nUnitMargin + m_nWidthScore + 'px';
		oDiv.style.marginLeft = nUnitMargin + 'px';
		oDiv.style.paddingLeft = nUnitMargin + 'px';
		oDiv.style.height = ( m_oParent.getRoundBetConfigHeaderHeight() * 2 ) + 'px';
		oDiv.style.lineHeight = ( m_oParent.getRoundBetConfigHeaderHeight() * 2 ) + 'px';
		oDiv.style.marginBottom = m_oParent.getHeaderMargin() + 'px';
		oDiv.appendChild( document.createTextNode( 'Wedstrijden' ) );
		
			
		oDiv = DivHelper.create( oHeadersDiv, null, null, null, null );
		oDiv.style.clear = 'both';
	}
	
	function showRoundBetConfig( oParentDiv, oRoundBetConfig, oRoundBetConfigResultExtra )
	{		
		var oDiv = DivHelper.create( oParentDiv, null, null, null, null );
		var sRowHeight = m_oParent.getRoundBetConfigHeaderHeight() + 'px';
		oDiv.style.height = sRowHeight;
		oDiv.style.lineHeight = sRowHeight;
		oDiv.style.backgroundColor = 'white';
		
		var oImage = document.createElement( "img" );
		{
			oImage.style.cursor = 'pointer';
			oImage.id = that.getRoundBetConfigDivId( oRoundBetConfig, "image" );
			oImage.onclick = function(){ that.expandCollapse( oImage ); };
			oImage.style.backgroundColor = 'white';
			oImage.className = 'floatleft';
			oImage.style.height = sRowHeight;
			oDiv.appendChild( oImage );
		}
		
		var oTextDiv = DivHelper.create( oDiv, null, 'floatleft', null, null );
		{
			oTextDiv.style.height = sRowHeight;
			oTextDiv.style.lineHeight = sRowHeight;
			oTextDiv.style.backgroundColor = 'white';
			
			var sDescription = oRoundBetConfig.getRound().getName();			
			oTextDiv.appendChild( document.createTextNode( sDescription ) );
		}
	}
		
	// if bExpand is true expand div, else collapse div!!
	//
	// Id, 	Thuis, 	Uit			Uitslag
	//
	// Uitgeklapt
	// ========
	// Groepsronde ( Score + Uitslag )
	// A,	PIC NED,PIC DTS		2 - 1 
	// Totaal
	// ========
	//
	// Ingeklapt
	// ========
	// Achtste finales ( Gekwal. teams )
	// ========
	this.refreshRoundBetConfig = function( oRoundBetConfig, bExpand )
	{
		var sDivId = this.getRoundBetConfigDivId( oRoundBetConfig, null );
		var oDiv = document.getElementById( sDivId );
		if ( oDiv == null )
			return;
		
		var sGamesDivId = this.getRoundBetConfigDivId( oRoundBetConfig, "games" );
		var oDivGames = document.getElementById( sGamesDivId );
		
		if ( bExpand == true )
		{
			var sImageId = this.getRoundBetConfigDivId( oRoundBetConfig, "image" );
			var oImage = document.getElementById( sImageId );
			oImage.src = g_sPubMap + "public/images/collapse.png";				
			
			if ( oDivGames == null )
			{
				oDivGames = DivHelper.create( oDiv, null, null, null, null );
				oDivGames.id = sGamesDivId;
				
				var arrGames = oRoundBetConfig.getRound().getGamesByDate();
				if ( arrGames.length > 0 )
				{				
					for ( var nI in arrGames )
					{
						if ( !( arrGames.hasOwnProperty( nI ) ) )
							continue;
						
						var oGame = arrGames[nI];
						this.createGame( oDivGames, oGame, oRoundBetConfig );
					}
				}
				else // winner
				{
					var oPoules = oRoundBetConfig.getRound().getPoules();
					for ( var nI in oPoules )
					{
						if ( !( oPoules.hasOwnProperty( nI ) ) )
							continue;
						
						var oPoulePlaces = oPoules[nI].getPlaces();
						for ( var nJ in oPoulePlaces )
						{
							if ( !( oPoulePlaces.hasOwnProperty( nJ ) ) )
								continue;
							
							var oPoulePlace = oPoulePlaces[nJ];
							this.createQualify( oDivGames, oPoulePlace, oRoundBetConfig );
							
							// break;
						}
						// break;
					}
				}
			}
			
			oDivGames.style.display = oDiv.style.display;
		}
		else
		{
			var sImageId = this.getRoundBetConfigDivId( oRoundBetConfig, "image" );
			var oImage = document.getElementById( sImageId );
			oImage.src = g_sPubMap + "public/images/expand.png";
			
			if ( oDivGames != null )
				oDivGames.style.display = 'none';
		}
	};
	
	// A,	PIC NED,PIC DTS		2 - 1 
	this.createGame = function( oParentDiv, oGame, oRoundBetConfig )
	{
		var nUnitMargin = m_oParent.getUnitMargin();
		
		var sRowHeight = m_oParent.getRoundBetConfigRowHeight( oRoundBetConfig ) + 'px';
		
		// Wanneer geen rechtstreekse knockout dan toon poolnaam
		var oDiv = DivHelper.create( oParentDiv, null, 'contentAllBets', null, null );
		oDiv.style.width = m_nWidthId + 'px';
		oDiv.style.height = sRowHeight;
		oDiv.style.textAlign = 'center';
		oDiv.style.lineHeight = sRowHeight;
		oDiv.style.marginLeft = nUnitMargin + 'px';
		oDiv.style.marginTop = nUnitMargin + 'px';		
		var oPoule = oGame.getHomePoulePlace().getPoule();
		this.addPouleDescriptionToDiv( oDiv, oPoule );
		
		oDiv.style.backgroundColor = 'white';
			
		var nPaddingTop = 0;
		var nRowHeight = parseInt( sRowHeight, 10 );
		if ( oRoundBetConfig.getBetType() == VoetbalOog_Bet_Qualify.nId )
		{
			var nPaddingTopTmp = nRowHeight - m_oParent.getRoundBetConfigRowHeightBase();
			if ( nPaddingTopTmp > 0 )
			{
				nPaddingTop = nPaddingTopTmp / 2;
				nRowHeight = m_oParent.getRoundBetConfigRowHeightBase();
			}
		}	
		oDiv = DivHelper.create( oParentDiv, null, 'contentAllBets', null, null );
		oDiv.style.width = m_nWidthHome + 'px';
		oDiv.style.height = nRowHeight + 'px';
		oDiv.style.lineHeight = nRowHeight + 'px';
		oDiv.style.marginLeft = nUnitMargin + 'px';
		oDiv.style.marginTop = nUnitMargin + 'px';
		oDiv.style.paddingTop = nPaddingTop + 'px';
		oDiv.style.backgroundColor = 'white';
		oDiv.style.paddingLeft = nUnitMargin + 'px';
		VoetbalOog_Control_Factory().appendPoulePlace( oDiv, oGame.getHomePoulePlace(), false, true );
		
		oDiv = DivHelper.create( oParentDiv, null, 'contentAllBets', null, null );
		oDiv.style.width = m_nWidthAway + 'px';
		oDiv.style.height = nRowHeight + 'px';
		oDiv.style.lineHeight = nRowHeight + 'px';
		oDiv.style.marginLeft = nUnitMargin + 'px';
		oDiv.style.marginTop = nUnitMargin + 'px';
		oDiv.style.paddingTop = nPaddingTop + 'px';
		oDiv.style.backgroundColor = 'white';
		oDiv.style.paddingLeft = nUnitMargin + 'px';
		VoetbalOog_Control_Factory().appendPoulePlace( oDiv, oGame.getAwayPoulePlace(), false, true );
		
		oDiv = DivHelper.create( oParentDiv, null, 'contentAllBets', null, null );
		oDiv.style.width = m_nWidthScore + 'px';
		oDiv.style.height = sRowHeight;
		oDiv.style.textAlign = 'center';
		oDiv.style.lineHeight = sRowHeight;
		oDiv.style.marginLeft = nUnitMargin + 'px';
		oDiv.style.marginTop = nUnitMargin + 'px';
		oDiv.style.backgroundColor = 'white';
		this.addScoreToDiv( oDiv, oGame );
		
		oDiv = DivHelper.create( oParentDiv, null, null, null, null );
		oDiv.style.clear = 'both';
	};
	
	// WINNER 
	this.createQualify = function( oParentDiv, oPoulePlace, oRoundBetConfig )
	{
		var nUnitMargin = m_oParent.getUnitMargin();
		
		var sRowHeight = m_oParent.getRoundBetConfigRowHeight( oRoundBetConfig ) + 'px';
		// Wanneer geen rechtstreekse knockout dan toon poolnaam
		var oDiv = DivHelper.create( oParentDiv, null, 'contentAllBets', null, null );
		oDiv.style.width = this.getWidth() + 'px';
		oDiv.style.height = sRowHeight;
		oDiv.style.textAlign = 'center';
		oDiv.style.marginLeft = nUnitMargin + 'px';
		oDiv.style.marginTop = nUnitMargin + 'px';

		VoetbalOog_Control_Factory().appendPoulePlace( oDiv, oPoulePlace, false, true );
		
		oDiv = DivHelper.create( oParentDiv, null, null, null, null );
		oDiv.style.clear = 'both';
	};
	
	this.getRoundBetConfigDivId = function( oRoundBetConfig, sPostFix )
	{
		var sId = m_sControlPrefix + 'roundbetconfig_' + oRoundBetConfig.getId(); 
		if ( sPostFix != null )
			sId += sPostFix; 
		return sId; 
	};
	
	this.getRoundBetConfig = function( sRoundBetConfigDivId )
	{
		var sId = sRoundBetConfigDivId.substr( sRoundBetConfigDivId.lastIndexOf("_") + 1 );
		var nId = parseInt( sId, 10 );
		return VoetbalOog_Round_BetConfig_Factory().createObjectFromDatabase( nId );
	};
	
	this.expandCollapse = function( oImage )
	{
		// var oImage = DivHelper.getTargetDiv( e );
		 
		var oRoundBetConfig = that.getRoundBetConfig( oImage.parentNode.parentNode.id );
		
		// var sImageId = that.getRoundBetConfigDivId( oRoundBetConfig, "image" );
		// var oImage = document.getElementById( sImageId );
			
		var bExpanded = ( oImage.src == g_sPubMap + "public/images/expand.png" );
		
		that.refreshRoundBetConfig( oRoundBetConfig, bExpanded );
		
		that.getParent().refreshPoolUsersRoundBetConfig( oRoundBetConfig, bExpanded );
	};
	
	this.isRoundBetConfigExpanded = function( oRoundBetConfig )
	{
		var sImageId = this.getRoundBetConfigDivId( oRoundBetConfig, "image" );
		var oImage = document.getElementById( sImageId );
		if ( oImage.src == g_sPubMap + "public/images/collapse.png" )
			return true;
		
		return false;
	};
	
	this.addScoreToDiv = function( oDiv, oGame )
	{	
		var sScore = ' - ';
		
		if ( oGame.getState() == g_jsonVoetbal.nState_Played )
			sScore = oGame.getHomeGoals() + ' - ' + oGame.getAwayGoals();
				
		oDiv.appendChild( document.createTextNode( sScore ) );
	};
	
	this.addPouleDescriptionToDiv = function( oDiv, oPoule )
	{			
		var sName = VoetbalOog_Poule_Factory().getName( oPoule, true );
		oDiv.appendChild( document.createTextNode( sName ) );
	};
}
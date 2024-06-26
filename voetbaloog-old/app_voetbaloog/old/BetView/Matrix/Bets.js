function Ctrl_BetView_Matrix_Bets( oParent ) 
{	
	var m_oParent = oParent;
	// var m_nPoolUserDivWidth = 65; // 55 is standaard
	var m_nPoolUserBetWidth = 48; // 40 is standaard
	var m_nPoolUserPointsWidth = 17; // 15 is standaard , borders included
	var m_oDiv = null;
	var m_oContentDiv = null;
	var m_sControlPrefix = 'ctrl_betview_matrix_poolusers_';
	var m_sLastDivId = m_sControlPrefix + 'lastdiv'; 
	
	var m_nWidthMarginLeftRight = 10;
	
	function getDiv()
	{
		return m_oDiv;
	}
	
	this.show = function( nWidth )
	{
		if ( m_oDiv == null )
		{
			m_oDiv = DivHelper.create( m_oParent.getDiv(), null, 'floatleft', null, null );
			m_oDiv.style.backgroundColor = 'white';
			m_oDiv.style.marginLeft = m_nWidthMarginLeftRight + 'px';
			//m_oDiv.appendChild( document.createTextNode( 'rechts' ) );
	
			m_oDiv.style.width = ( m_oParent.getBetsWidth() - 2 ) + 'px';
			m_oDiv.style.overflowX = 'auto';
			// m_oDiv.style.overflowX = 'none';
			// m_oContentDiv.style.overflow = 'none';
			
			m_oContentDiv = m_oDiv.appendChild( document.createElement('div') );
			var nTotalWidth = m_oParent.getNrOfPoolUsers() * ( this.getPoolUserDivWidth() + m_oParent.getUnitMargin() );
			m_oContentDiv.style.width = nTotalWidth + 'px';
			
			var oDiv = DivHelper.create( m_oParent.getDiv(), m_sLastDivId, null, null, null );
			oDiv.style.clear = 'both';
		}
	};
	
	this.getRoundBetConfigDivId = function( oPoolUser, oRoundBetConfig, sPostFix )
	{
		var sId = m_sControlPrefix + 'pooluser_' + oPoolUser.getId();
		if ( oRoundBetConfig != null )
			sId += '_roundbetconfig_' + oRoundBetConfig.getId();
		if ( sPostFix != null )
			sId += sPostFix;
		return sId;
	};
	
	/**
	 * Mogelijke acties:
	 * Ctrl_BetView.nMovePoolUserForwards = 1;
	 * Ctrl_BetView.nMovePoolUserBackwards = 2;
	 * Ctrl_BetView.nRemovePoolUser = 3;
	 * Ctrl_BetView.nAppendPoolUser = 4;
	 */
	this.refresh = function( oPoolUserSrc, oPoolUserDest, nAction )
	{
		if ( nAction == Ctrl_BetView.nAppendPoolUser )
			this.appendPoolUser( oPoolUserSrc, oPoolUserDest );
		else if ( nAction == Ctrl_BetView.nRemovePoolUser )
			this.hidePoolUser( oPoolUserSrc );
		else if ( nAction == Ctrl_BetView.nMovePoolUserForwards )
			this.shiftPoolUser( oPoolUserSrc, oPoolUserDest, true );
		else if ( nAction == Ctrl_BetView.nMovePoolUserBackwards )
			this.shiftPoolUser( oPoolUserSrc, oPoolUserDest, false );
		else if ( nAction == Ctrl_BetView.nRemovePoolUser )
			this.hidePoolUser( oPoolUserSrc );
		else
			throw "action not implemented yet!";
		
		// console.log( m_oContentDiv.children.length ); 
		//console.log( 'before showing control get bets from users shown with ajax sync' );		
		//console.log( 'then get the rest of the bets with ajax async, should work fine, check performance' );
		
		// gedeelte 1 van ontabclick
		// gedeelte 2 
		
		// Id, 	Thuis, 	Uit			Uitslag, 	PoolUserX	, PoolUserY
		// Groepsronde (i)
		// A,	PIC NED,PIC DTS		2 - 1 [4], 	2 - 2	[2]	, 1 - 1  [1]
		// Totaal						  44 ,			11	.		 12
		// ronde 2(i)
		// 49,	PIC NED,PIC DTS		PIC NED [4], PIC NED [4], PIC NED [4]
		// 							PIC DTS [4], PIC DTS [4], PIC DTS [4]
		// Totaal						  44 ,			11	.		 12		
	};
	
	this.refreshBackgrounds = function()
	{
		var oChildren = m_oContentDiv.children;
		for ( var nI = 0; nI < oChildren.length; nI++ ) 
		{
			if ( !( oChildren.hasOwnProperty( nI ) ) )
				continue;
			
			sBGColor = null;
			if ( nI % 2 == 1 )
				sBGColor = '#F0F0F0';
			else
				sBGColor = 'white';
				
			oChildren[nI].style.backgroundColor = sBGColor;
		}
	};
	
	this.appendPoolUser = function( oPoolUserSrc, oPoolUserDest )
	{
		var oPoolUserSrcDiv = document.getElementById( this.getRoundBetConfigDivId( oPoolUserSrc, null, null ) );
		var bCreate = ( oPoolUserSrcDiv == null );
			
		if ( bCreate == true )
			oPoolUserSrcDiv = this.createPoolUser( oPoolUserSrc );
		
		var oPoolUserDestDiv = null;
		if ( oPoolUserDest != null )
			oPoolUserDestDiv = document.getElementById( this.getRoundBetConfigDivId( oPoolUserDest, null, null ) );
			
		if ( oPoolUserDestDiv == undefined )
			m_oContentDiv.appendChild( oPoolUserSrcDiv );
		else
			m_oContentDiv.insertBefore( oPoolUserSrcDiv, oPoolUserDestDiv );
		
		if ( bCreate == true )
			this.addContentToPoolUserDiv( oPoolUserSrc );
		// als dest ongelijk is aan null dan insertbefore
		// this.createPoolUser( oPoolUserSrc );
		
		oPoolUserSrcDiv = document.getElementById( this.getRoundBetConfigDivId( oPoolUserSrc, null, null ) );
		oPoolUserSrcDiv.style.display = 'inline';
		
		// console.log( m_oParent.getMaxNrOfPoolUsers( parseInt( m_oContentDiv.style.width, 10 ) ) );
		// console.log( m_oParent.getNrOfPoolUsers() );
		//if ( nrofpoolusers > max )
			//m_oDiv.style.overflowX = 'auto';
		//else
			//m_oDiv.style.overflowX = 'none';		
	};
	
	this.createPoolUser = function( oPoolUser )
	{	
		var oPoolUserDiv = document.createElement( 'div' );
		// var oPoolUserDiv = DivHelper.create( getDiv(), null, 'floatleft', null, null );
		oPoolUserDiv.id = this.getRoundBetConfigDivId( oPoolUser, null, null );
		// console.log( this.getRoundBetConfigDivId( oPoolUser, null, null ) );
		oPoolUserDiv.className = 'floatleft';
		oPoolUserDiv.style.backgroundColor = 'white';
		// oPoolUserDiv.style.width = '100px';
		//getDiv().appendChild( oPoolUserDiv/*, document.getElementById( m_sLastDivId )*/ );		
		return oPoolUserDiv;
	};
	
	this.addContentToPoolUserDiv = function( oPoolUser )
	{	
		var oPoolUserDiv = document.getElementById( this.getRoundBetConfigDivId( oPoolUser, null, null ) );
				
		var oHeaderDiv = DivHelper.create( oPoolUserDiv, null, 'headerAllBets', null, null );
		{
			oHeaderDiv.style.width = this.getPoolUserDivWidth() + 'px';
			oHeaderDiv.style.height = m_oParent.getRoundBetConfigHeaderHeight() + 'px';
			oHeaderDiv.style.marginLeft = m_oParent.getUnitMargin() + 'px';			
			oHeaderDiv.appendChild( document.createTextNode( oPoolUser.getUser().getId() ) );
			oHeaderDiv.style.clear = 'both';
			
			var oDivClear = DivHelper.create( oPoolUserDiv, null, null, null, null );
			oDivClear.style.clear = 'both';
			
			////////////////////////////
			var jsonResult = { Points : 0, Correct : true };
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
					
					var oRoundBetConfigDiv = DivHelper.create( oPoolUserDiv, null, null, null, null );
					oRoundBetConfigDiv.id = this.getRoundBetConfigDivId( oPoolUser, oRoundBetConfig, null );
					oRoundBetConfigDiv.style.borderBottom = '1px solid #135113';
					// oRoundBetConfigDiv.style.backgroundColor = 'white';				
					
					this.initRoundBetConfig( oPoolUser, oRoundBetConfig, oRoundBetConfigResultExtra, jsonResult );
					
					var bExpanded = m_oParent.isRoundBetConfigExpanded( oRoundBetConfig );
					this.refreshRoundBetConfig( oPoolUser, oRoundBetConfig, bExpanded );
				}
			}			
			/////////////////////////////
			
			// add totalpoints div
			//var sDivId = this.getRoundBetConfigDivId( oPoolUser, oRoundBetConfig, "points" );
			var oDivPlace = document.createElement( "div" );			
			oDivPlace.style.height = m_oParent.getRoundBetConfigHeaderHeight() + 'px';
			var nPaddingLeft = 8;
			oDivPlace.style.width = ( parseInt( this.getPoolUserDivWidth() / 2, 10 ) - nPaddingLeft ) + 'px';
			oDivPlace.style.marginLeft = m_oParent.getUnitMargin() + 'px';
			oDivPlace.className = 'floatleft';
			oDivPlace.style.fontWeight = 'bold';
			oDivPlace.style.paddingLeft = nPaddingLeft + 'px';
			oDivPlace.style.marginBottom = m_oParent.getHeaderMargin() + 'px';
			var nRanking = oPoolUser.getRanking();
			oDivPlace.appendChild( document.createTextNode( nRanking ) );
			var oImg = VoetbalOog_Control_Factory().createImage( oPoolUser );
			if ( oImg != null )
				oDivPlace.appendChild( oImg );
			oPoolUserDiv.insertBefore( oDivPlace, oHeaderDiv.nextSibling.nextSibling );
			
			var oDivPoints = document.createElement( "div" );			
			oDivPoints.style.height = m_oParent.getRoundBetConfigHeaderHeight() + 'px';
			oDivPoints.style.width = parseInt( this.getPoolUserDivWidth() / 2, 10 ) + 'px';
			oDivPoints.style.marginBottom = m_oParent.getHeaderMargin() + 'px';
			oDivPoints.className = 'floatleft';
			{	
				this.addTotalPointsDiv( oDivPoints, jsonResult );
				
				var oDivClear = DivHelper.create( oDivPoints, null, null, null, null );
				oDivClear.style.clear = 'both';
			}
			oPoolUserDiv.insertBefore( oDivPoints, oDivPlace.nextSibling );			
		}
	};
	
	this.hidePoolUser = function( oPoolUser )
	{
		var oPoolUserDiv = document.getElementById( this.getRoundBetConfigDivId( oPoolUser, null, null ) );
		if( oPoolUserDiv == null )
		{
			this.appendPoolUser( oPoolUser );
			oPoolUserDiv = document.getElementById( this.getRoundBetConfigDivId( oPoolUser, null, null ) );
		}
		
		oPoolUserDiv.style.display = 'none';		
	};
	
	// div header
	// div games
	// div totalpoints	
	this.initRoundBetConfig = function( oPoolUser, oRoundBetConfig, oRoundBetConfigResultExtra, jsonTotalResult )
	{
		var sDivId = this.getRoundBetConfigDivId( oPoolUser, oRoundBetConfig, null );
		var oDiv = document.getElementById( sDivId );
		if ( oDiv == null )
			return;
		
		// add totalpoints div
		var sDivId = this.getRoundBetConfigDivId( oPoolUser, oRoundBetConfig, "points" );
		var oDivPoints = DivHelper.create( oDiv, sDivId, null, null, null );
		oDivPoints.style.height = m_oParent.getRoundBetConfigHeaderHeight() + 'px';
		oDivPoints.style.clear = 'both';
		
		var jsonResult = { Points : 0, Correct : true };
		
		// add games div
		var sDivId = this.getRoundBetConfigDivId( oPoolUser, oRoundBetConfig, "games" );
		oDivGames = DivHelper.create( oDiv, sDivId, null, null, null );		
		{
			var arrGames = oRoundBetConfig.getRound().getGamesByDate();
			var oBets = oPoolUser.getBets( oRoundBetConfig );
			var oBetsResultExtra = null;
			if ( oRoundBetConfigResultExtra != null )
				oBetsResultExtra = oPoolUser.getBets( oRoundBetConfigResultExtra );
			var nHeight = m_oParent.getRoundBetConfigRowHeight( oRoundBetConfig );
					
			if ( arrGames.length > 0 )
			{
				for ( var nI in arrGames )
				{
					if ( !( arrGames.hasOwnProperty( nI ) ) )
						continue;
					
					//var jsonResult = null;
					var oGame = arrGames[nI];
									
					if ( oRoundBetConfig.getBetType() == VoetbalOog_Bet_Qualify.nId )
					{	
						var nUnitMargin = m_oParent.getUnitMargin();		
						var oMainDiv = DivHelper.create( oDivGames, null, 'contentAllBetsClear', null, null );
						oMainDiv.style.marginTop = nUnitMargin + 'px';
						oMainDiv.style.marginLeft = nUnitMargin + 'px';
						oMainDiv.style.height = nHeight + 'px';
						oMainDiv.style.width = this.getPoolUserDivWidth() + 'px';
						
						var oBetHome = oBets[ oGame.getHomePoulePlace().getId() ];
						var nRealHeight = ( nHeight - 1 ) / 2; // 1 is the bottom border
						this.createBetQualify( oMainDiv, oGame.getHomePoulePlace().getTeam(), oBetHome, nRealHeight, jsonResult );
						var oBetAway = oBets[ oGame.getAwayPoulePlace().getId() ];
						this.createBetQualify( oMainDiv, oGame.getAwayPoulePlace().getTeam(), oBetAway, nRealHeight, jsonResult );
					}
					else
					{
						var oBet = oBets[ oGame.getId() ];
						var oBetResultExtra = null;
						if ( oRoundBetConfigResultExtra != null )
							oBetResultExtra = oBetsResultExtra[ oGame.getId() ]; 
						this.createBetScoreResult( oDivGames, oGame, oBet, oBetResultExtra, nHeight, jsonResult );
					}				
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
						
						var nUnitMargin = m_oParent.getUnitMargin();		
						var oMainDiv = DivHelper.create( oDivGames, null, 'contentAllBetsClear', null, null );
						oMainDiv.style.marginTop = nUnitMargin + 'px';
						oMainDiv.style.marginLeft = nUnitMargin + 'px';
						oMainDiv.style.height = nHeight + 'px';
						oMainDiv.style.width = this.getPoolUserDivWidth() + 'px';
						
						var oPoulePlace = oPoulePlaces[nJ];
						var oBet = oBets[ oPoulePlace.getId() ];
						this.createBetQualify( oMainDiv, oPoulePlace.getTeam(), oBet, nHeight, jsonResult );
					}
				}
			}
		}
		
		jsonTotalResult.Points += jsonResult.Points; 
		jsonTotalResult.Correct = ( jsonTotalResult.Correct && jsonResult.Correct );
		
		// set oDivPoints
		{	
			// var nUnitMargin = m_oParent.getUnitMargin();
			
			// Wanneer geen rechtstreekse knockout dan toon poolnaam
			/*
			var oDivTmp = DivHelper.create( oDivPoints, null, 'contentAllBets', null, null );
			{
				oDivTmp.style.marginTop = nUnitMargin + 'px';
				oDivTmp.style.marginBottom = nUnitMargin + 'px';
				oDivTmp.style.height = ( m_oParent.getRoundBetConfigHeaderHeight() - ( 2 * nUnitMargin ) ) + 'px';
				oDivTmp.style.width = ( this.getPoolUserBetWidth() ) + 'px';  
				oDivTmp.style.marginLeft = ( nUnitMargin + nUnitMargin ) + 'px';
				
				oDivTmp.style.textAlign = 'center';
				oDivTmp.style.backgroundColor = 'orange';
			}
			*/
			this.addTotalPointsDiv( oDivPoints, jsonResult );
			
			var oDivClear = DivHelper.create( oDivPoints, null, null, null, null );
			oDivClear.style.clear = 'both';
		}
	};
	
	// if bExpand is true expand div :::::: div has total
	// div header
	// div games
	// div totalpoints
	//
	// else bExpand is false collapse div :::::: div has header, games, total
	// div totalpoints
	// 
	// show total points, if expanded show header 
	this.refreshRoundBetConfig = function( oPoolUser, oRoundBetConfig, bExpand )
	{
		var sDivId = this.getRoundBetConfigDivId( oPoolUser, oRoundBetConfig, null );
		var oDiv = document.getElementById( sDivId );
		if ( oDiv == null )
			return;
		
		// add header div
		var sDivId = this.getRoundBetConfigDivId( oPoolUser, oRoundBetConfig, "games" );
		var oDivGames = document.getElementById( sDivId );
		
		if ( bExpand == true )
		{
			oDivGames.style.display = oDiv.style.display;
		}
		else
		{
			if ( oDivGames != null )
				oDivGames.style.display = 'none';
		}
	};
	
	this.createBetScoreResult = function( oParentDiv, oGame, oBet, oBetResultExtra, nHeight, jsonResult )
	{	
		var nUnitMargin = m_oParent.getUnitMargin();
		
		var oMainDiv = DivHelper.create( oParentDiv, null, 'contentAllBetsClear', null, null );
		oMainDiv.style.marginTop = nUnitMargin + 'px';
		oMainDiv.style.marginLeft = nUnitMargin + 'px';
		oMainDiv.style.width = this.getPoolUserDivWidth() + 'px';
		oMainDiv.style.height = nHeight + 'px';
		//oMainDiv.style.backgroundColor = 'blue';
		
		// Wanneer geen rechtstreekse knockout dan toon poolnaam
		var oDivScoreResult = DivHelper.create( oMainDiv, null, 'contentAllBets', null, null );
		{
			oDivScoreResult.style.marginTop = nUnitMargin + 'px';
			oDivScoreResult.style.marginBottom = nUnitMargin + 'px';
			oDivScoreResult.style.height = ( nHeight - ( 2 * nUnitMargin ) ) + 'px';
			oDivScoreResult.style.width = ( this.getPoolUserBetWidth() ) + 'px';  
			oDivScoreResult.style.marginLeft = nUnitMargin + 'px';
			oDivScoreResult.style.textAlign = 'center';
			// oDivScoreResult.style.backgroundColor = 'white';
			
			this.addScoreToDiv( oDivScoreResult, oBet );
		}
		
		var bBottomMargin = true;		
		var nPoints = this.addPointsDiv( oMainDiv, nHeight, bBottomMargin, oGame.getState() == g_jsonVoetbal.nState_Played, oBet, oBetResultExtra );
		jsonResult.Correct = ( oGame.getState() == g_jsonVoetbal.nState_Played && jsonResult.Correct && ( nPoints != 0 ) );
		jsonResult.Points += nPoints;
		
		var oDivClear = DivHelper.create( oParentDiv, null, null, null, null );
		oDivClear.style.clear = 'both';
	};
	
	this.addPointsDiv = function( oParentDiv, nHeight, bBottomMargin, bBetCanBeChecked, oBet, oBetResultExtra )
	{
		var nUnitMargin = m_oParent.getUnitMargin();
		
		var nPoints = null;
		var sClassName = 'contentAllBetsPointsNotCorrect';
		{
			var bCorrect = false;
			if ( bBetCanBeChecked == true )
			{
				nPoints = 0;
						
				if ( oBet != null )
				{	
					if ( oBet.getCorrect() == true )
					{
						nPoints = oBet.getRoundBetConfig().getPoints();
						bCorrect = true;
					}
					
					if ( oBetResultExtra != null )
					{
						if ( oBetResultExtra.getCorrect() == true )
							nPoints += oBetResultExtra.getRoundBetConfig().getPoints();						
						else
							bCorrect = false;
					}
				}
			}
			if ( bCorrect == true )
				sClassName = 'contentAllBetsPointsCorrect';
		}
		
		var oDivPoints = DivHelper.create( oParentDiv, null, sClassName, null, null );
				
		var nBorderWidth = 1;
		
		oDivPoints.style.marginTop = nUnitMargin + 'px';
		
		if ( bBottomMargin == true )
		{
			oDivPoints.style.marginBottom = nUnitMargin + 'px';
			oDivPoints.style.height = ( nHeight - (  nBorderWidth + ( 2 * nUnitMargin ) ) ) + 'px';
		}
		else
		{
			oDivPoints.style.height = ( nHeight - (  2 * nUnitMargin ) ) + 'px';
		}
		
		oDivPoints.style.marginRight = nUnitMargin + 'px';
		oDivPoints.style.width = ( this.getPoolUserPointsWidth() - ( 2 * nBorderWidth ) ) + 'px';   
		oDivPoints.style.textAlign = 'right';
		oDivPoints.innerHTML = nPoints;
		
		return nPoints;
	};
	
	this.createBetQualify = function( oParentDiv, oTeamQualified, oBet, nHeight, jsonResult )
	{	
		var nUnitMargin = m_oParent.getUnitMargin();
		var bBottomMargin = false;		
		var oDivQualify= DivHelper.create( oParentDiv, null, 'contentAllBets', null, null );
		{
			oDivQualify.style.marginLeft = nUnitMargin + 'px';
			oDivQualify.style.marginTop = nUnitMargin + 'px';
			oDivQualify.style.height = ( nHeight - nUnitMargin ) + 'px';
			oDivQualify.style.width = ( this.getPoolUserBetWidth() ) + 'px'; 
			// oDivQualify.style.backgroundColor = 'white';
			if ( oBet != null )
			{
				this.addTeamToDiv( oDivQualify, oBet.getTeam() );
			}
			//else
				//oDivQualify.appendChild( document.createTextNode( 'unk' ) );
		}
		
		var bTeamQualified = ( oTeamQualified != null );
		var nPoints = this.addPointsDiv( oParentDiv, nHeight, bBottomMargin, bTeamQualified, oBet, null );
		jsonResult.Correct = ( bTeamQualified && jsonResult.Correct && ( nPoints != 0 ) );
		jsonResult.Points += nPoints;
		
		var oDivClear = DivHelper.create( oParentDiv, null, null, null, null );
		oDivClear.style.clear = 'both';	
	};
	
	this.addTotalPointsDiv = function( oParentDiv, jsonResult )
	{
		var nUnitMargin = m_oParent.getUnitMargin();
		
		var sClassName = 'contentAllBetsTotalPointsNotCorrect';
		if ( jsonResult.Correct == true )
			sClassName = 'contentAllBetsTotalPointsCorrect';	
				
		var oDivPoints = DivHelper.create( oParentDiv, null, sClassName, null, null );
				
		var nBorderWidth = 1;
		
		oDivPoints.style.marginTop = nUnitMargin + 'px';
		
		var nHeight =  m_oParent.getRoundBetConfigHeaderHeight();
		oDivPoints.style.marginBottom = nUnitMargin + 'px';
		oDivPoints.style.height = ( nHeight - (  nBorderWidth + ( 2 * nUnitMargin ) ) ) + 'px';
		oDivPoints.style.marginRight = nUnitMargin + 'px';
		oDivPoints.style.width = ( Math.round( this.getPoolUserPointsWidth() * 1.5 ) - ( 2 * nBorderWidth ) ) + 'px';   
		oDivPoints.style.textAlign = 'right';
		oDivPoints.appendChild( document.createTextNode( '' + jsonResult.Points ) );
	};
		
	this.shiftPoolUser = function( oPoolUserSrc, oPoolUserDest, bForwards )
	{
		// getDiv of oPoolUser and shift to the correct side
		var sId = this.getRoundBetConfigDivId( oPoolUserSrc, null, null );
		var oDiv = document.getElementById( sId );
		if ( oDiv == undefined )
		{
			console.log( 'could not find div for ' + oPoolUserSrc.getUser().getId() );
			return
		}
		if ( bForwards == true )
		{
			// console.log( 'shiftPoolUser forwards' );			
			oDiv.parentNode.insertBefore( oDiv, oDiv.previousSibling );
		}
		else
		{
			// console.log( 'shiftPoolUser backwards' );
			var oNextSibling = oDiv.nextSibling;
			if ( oNextSibling != null )
				oDiv.parentNode.insertBefore( oDiv, oNextSibling.nextSibling );
		}
	};
	
	this.addScoreToDiv = function( oDiv, oBet )
	{	
		var sScore = ' - ';
		if ( oBet != null )
			sScore = oBet.getHomeGoals() + ' - ' + oBet.getAwayGoals();			
		oDiv.appendChild( document.createTextNode( sScore ) );
	};
	
	this.addTeamToDiv = function( oDiv, oTeam )
	{		
		if ( oTeam != null )
		{
			VoetbalOog_Control_Factory().appendTeam( oDiv, oTeam, false, true, false, false );
		}
		else
		{
			oDiv.appendChild( document.createTextNode( '-' ) );
		}
	};
	
	this.getPoolUserDivWidth = function()
	{
		var nUnitMargin = m_oParent.getUnitMargin();
		return nUnitMargin + m_nPoolUserBetWidth + m_nPoolUserPointsWidth + nUnitMargin;
	};
	
	this.getPoolUserBetWidth = function()
	{
		return m_nPoolUserBetWidth;
	};
	
	this.getPoolUserPointsWidth = function()
	{
		return m_nPoolUserPointsWidth;
	};
}
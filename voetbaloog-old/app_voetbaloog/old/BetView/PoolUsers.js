/*
 * div met namen van poolusers gesorteerd per aantal punten
 * wanneer je op een blokje klikt, verschijnt of verdwijnt de persoon 
 */
function Ctrl_BetView_PoolUsers( oParent ) 
{	
	var m_oParent = oParent;
	var m_oPoolUsersToShow = new Array();
	var m_oPoolUsersToHide = new Array();
	var m_nUnitMargin = 2;
	// var m_nUnitHeight = 15;
	// var m_nMouseDifference = 9;
	var m_nPoolUserDivWidth = null;
	var m_sControlPrefix = 'ctrl_betview_poolusers_';
	// var m_nStartPosY = null;
	// var that = this;
	var m_oDiv = null;
	// var m_oDragDiv = null; 
	
	this.getDiv = function()
	{
		return m_oDiv;
	};
	
	// walk through divs and get poolusers
	this.getPoolUsersToShow = function()
	{
		var oPoolUsersToShow = new Array();
		// var oDivShow = document.getElementById( m_sControlPrefix + 'show' );
		var oUL = document.getElementById( 'ul1' );
		
		var oPoolUsers = m_oParent.getPool().getUsers();
		
		// console.log();
		for ( var nI = 0; nI < oUL.children.length; nI++ ) 
		{
			if ( !( oUL.children.hasOwnProperty( nI ) ) )
				continue;
			
			var sDivId = oUL.childNodes[nI].id;
			if ( sDivId == null )
				continue;
			
			var sPoolUserId = sDivId.substr( 4 );
			if ( sPoolUserId.length > 0 )
				oPoolUsersToShow.push( oPoolUsers[sPoolUserId] );			
		}
		
		return oPoolUsersToShow;
	};
	
	this.init = function()
	{	
		m_oDiv = DivHelper.create( m_oParent.getDiv(), null, 'floatleft', null, null );
		m_oDiv.style.border = '1px solid #C0C0C0';
		// m_oDiv.style.borderColor = 'green'; //';
		//m_oDiv.style.position = 'absolute';
		
		// var nHeight = 100; // info part + 2 headers
		// nHeight += m_oParent.getNrOfPoolUsers() * 14;
		// m_oDiv.style.height = nHeight + 'px';
		m_oDiv.style.width = ( this.getWidthOfPoolUserDiv() 
							+ ( 2 * m_nUnitMargin ) // unitmargin
							+ ( 2 * 2 ) // border show/hide
							+ ( 2 * 2 ) // margin show/hide
							// + ( 2 * 1 ) // border poolusersdiv excluded
							) + 'px';
		m_oDiv.unselectable = "on";
		
		// var nTotalHeight = parseInt( m_oDiv.style.height, 10 );
		
		// var nUnitHeight = parseInt( m_oDiv.style.fontSize, 10 );
		
		var oPoolUsers = m_oParent.getPool().getUsers( true );
		// maak hier een object met places to show
		var oPlacesToShow = this.getPlacesToShow( oPoolUsers );
		
		var nCounter = 1;
		for ( var nI in oPoolUsers )
		{	
			if ( !( oPoolUsers.hasOwnProperty( nI ) ) )
				continue;
			
			var oPoolUserIt = oPoolUsers[nI];			
			
			if ( oPlacesToShow[ nCounter ] != null  )
				m_oPoolUsersToShow.push( oPoolUserIt );
			else
				m_oPoolUsersToHide.push( oPoolUserIt );
			
			nCounter++;
		}
	};
	
	// de eerste drie moeten altijd getoond worden
	// de laatste moet sowieso getoond worden	
	// overige plekken worden gevuld door ingelogde gebruiker en hoger
	this.getPlacesToShow = function( oPoolUsers )
	{
		var oPlacesToShow = new Object();
		oPlacesToShow[1] = true; // first
		oPlacesToShow[2] = true; // second
		oPlacesToShow[3] = true; // third
		
		var nMaxNrOfPoolUsers = m_oParent.getMaxNrOfPoolUsers( this );
		
		// from place of pooluser and everything above until places are filled
		var oPoolUser = m_oParent.getPoolUser();
		var nPlacesToShow = nMaxNrOfPoolUsers - 4; /* 1,2,3 en laatst */
		var nMinimumPlace = 3 + nPlacesToShow;
		
		var nPoolUserPlace = nMinimumPlace;
		var nCounter = 1;
		for ( var nI in oPoolUsers )
		{	
			if ( !( oPoolUsers.hasOwnProperty( nI ) ) )
				continue;
			
			if ( oPoolUser == oPoolUsers[nI] && nCounter > nMinimumPlace )			
				nPoolUserPlace = nCounter;						
			nCounter++;
		}		
		// if nPoolUserPlace is last than subtract one because last place
		// already will be used
		if ( nPoolUserPlace == ( nCounter - 1 ) )
			nPoolUserPlace--;
		
		oPlacesToShow[ nCounter - 1 ] = true; // last	
		
		for ( var nJ = nPoolUserPlace ; nJ > nPoolUserPlace - nPlacesToShow ; nJ-- )
		{
			oPlacesToShow[ nJ ] = true; // rest places
		}
			
		return oPlacesToShow;
	};
	
	this.show = function()
	{		
		if ( m_oDiv == null )
		{
			this.init();
		
			var oDiv = this.getDiv();
			while ( oDiv.hasChildNodes() )
				oDiv.removeChild( oDiv.lastChild );
			
			var oDivShow = DivHelper.create( oDiv, null, null, null, null );
			{
				oDivShow.id = m_sControlPrefix + 'show';
				oDivShow.style.border = '2px solid #135113';
				oDivShow.style.margin = m_nUnitMargin + 'px';
			
				var oDivShowHeader = DivHelper.create( oDivShow, null, null, null, null );
				oDivShowHeader.style.fontWeight = 'bold';
				oDivShowHeader.style.textAlign = 'center';
				oDivShowHeader.appendChild( document.createTextNode( 'Getoonde' ) );
				oDivShowHeader.appendChild( document.createElement( "br" ) );
				oDivShowHeader.appendChild( document.createTextNode( 'deelnemers' ) );				
			}
			
			var oDivInfo = DivHelper.create( oDiv, null, null, null, null );
			{
				oDivInfo.style.border = '2px solid #135113';
				oDivInfo.style.margin = m_nUnitMargin + 'px';
				
				var oDivInfoText = DivHelper.create( oDivInfo, null, null, null, null );
				oDivInfoText.appendChild( document.createTextNode( 'De deelnemers zijn te verslepen.' ) );
			}
			
			var oDivHide = DivHelper.create( oDiv, null, null, null, null );
			oDivHide.id = m_sControlPrefix + 'hide';
			{
				oDivHide.style.border = '2px solid #135113';
				oDivHide.style.margin = m_nUnitMargin + 'px';
				
				var oDivHideHeader = DivHelper.create( oDivHide, null, null, null, null );
				oDivHideHeader.style.fontWeight = 'bold';
				oDivHideHeader.style.textAlign = 'center';
				oDivHideHeader.appendChild( document.createTextNode( 'Verborgen' ) );
				oDivHideHeader.appendChild( document.createElement( "br" ) );
				oDivHideHeader.appendChild( document.createTextNode( 'deelnemers' ) );
				oDivHide.appendChild( oDivHideHeader );		
			}
		}
	};
	
	this.initUsers = function()
	{
		// var oPoolUser = m_oParent.getPoolUser();
		
		var oDivShow = document.getElementById( m_sControlPrefix + 'show' );
		{
			var oUL = document.createElement( "ul" );
			{
				oUL.id = "ul1";
				oUL.className = "draglist";
				oDivShow.appendChild( oUL );
			}
			new YAHOO.util.DDTarget("ul1");
			
			var nCount = 0;
			for ( var nI in m_oPoolUsersToShow )
			{	
				if ( !( m_oPoolUsersToShow.hasOwnProperty( nI ) ) )
					continue;
				
				nCount++;
				
				var oPoolUserIt = m_oPoolUsersToShow[nI];
				// var sBackGroundColor = 'magenta';
				// if ( oPoolUser == oPoolUserIt )
					// sBackGroundColor = 'orange';
				
				var oLI = document.createElement( "li" );
				{
					oLI.id = "li1_" + oPoolUserIt.getId();
					oLI.className = "list1";
					oLI.appendChild( document.createTextNode( oPoolUserIt.getUser().getId() ) );
					oUL.appendChild( oLI );
				}
				
				//for ( var i = 1 ; i < m_oPoolUsersToShow.length + 1 ; i++ ) {
				//	console.log(i);
					new YAHOO.voetbaloog.DDList("li1_" + oPoolUserIt.getId());
				//}
				
				/*
				var oDivIt = DivHelper.create( oDivShow, null, null, null, null );
				oDivIt.id = m_sControlPrefix + "pooluserid_" + oPoolUserIt.getId();
				oDivIt.style.backgroundColor = sBackGroundColor;
				oDivIt.style.margin = m_nUnitMargin + 'px';
				oDivIt.style.cursor = 'move'; // crosshair
				oDivIt.style.width = this.getWidthOfPoolUserDiv() + 'px';
				oDivIt.style.height = m_nUnitHeight + 'px';
				oDivIt.cstmDropTarget = true;
				oDivIt.addEventListener( "mousedown", startDragDrop, false);
				oDivIt.appendChild( document.createTextNode( oPoolUserIt.getUser().getId() ) );
				*/
				m_oParent.refreshMatrix( oPoolUserIt, null, Ctrl_BetView.nAppendPoolUser );			
			}
		}
		
		var oDivHide = document.getElementById( m_sControlPrefix + 'hide' );
		{
			var oUL = document.createElement( "ul" );
			{
				oUL.id = "ul2";
				oUL.className = "draglist";
				oDivHide.appendChild( oUL );
			}
			new YAHOO.util.DDTarget("ul2");
			
			var nCount = 0;
			for ( var nI in m_oPoolUsersToHide )
			{	
				if ( !( m_oPoolUsersToHide.hasOwnProperty( nI ) ) )
					continue;
				
				nCount++;
				
				var oPoolUserIt = m_oPoolUsersToHide[nI];
				// var sBackGroundColor = 'magenta';
				// if ( oPoolUser == oPoolUserIt )
					// sBackGroundColor = 'purple';
				
				var oLI = document.createElement( "li" );
				{
					oLI.id = "li2_" + oPoolUserIt.getId();
					oLI.className = "list2";
					oLI.appendChild( document.createTextNode( oPoolUserIt.getUser().getId() ) );
					oUL.appendChild( oLI );
				}
				
				new YAHOO.voetbaloog.DDList("li2_" + oPoolUserIt.getId() );
				
				/*
				var oDivIt = DivHelper.create( oDivHide, null, null, null, null );
				oDivIt.style.backgroundColor = sBackGroundColor;
				oDivIt.style.margin = m_nUnitMargin + 'px';
				oDivIt.style.cursor = 'move';
				oDivIt.style.width = this.getWidthOfPoolUserDiv() + 'px';
				oDivIt.appendChild( document.createTextNode( oPoolUserIt.getUser().getId() ) );
				*/
			}
		}
		
		

		
		//for ( var i = 1 ; i < m_oPoolUsersToHide.length + 1 ; i++ ) {
		//	console.log(i);
		//	new YAHOO.voetbaloog.DDList("li2_" + i);
		//}
	};
	
	// onclick
	this.updatePoolUser = function( sPoolUserDivId )
	{
		
	};
	
	this.getWidthOfPoolUserDiv = function()
	{
		if ( m_nPoolUserDivWidth == null )
		{
			m_nPoolUserDivWidth = 80;
		}
		return m_nPoolUserDivWidth;
	};
	
	this.getNrOfPoolUsersShow = function()
	{
		return m_oPoolUsersToShow.length;
	};
	
	/*
	this.putEventsFromDb = function( oExpected )
	{
		var sUrlParamsPeriods = new String();
		{
			var oPeriods = this.getRosterSeason().getPeriods();
			for ( var nI in oPeriods )
			{
				var oPeriod = oPeriods[nI];
					sUrlParamsPeriods += m_oMainParent.getPeriodCheckBoxPrefix() + oPeriod.getId() + "/on/";			
			}
		}

		var sType = null; 
		if ( oExpected.getClassName() == Lesplan_Group_Factory().getClassName() )
			sType = 'group'; // group should be made obsolete and replaced by Lesplan_Group_Factory().getClassName()
		else if ( oExpected.getClassName() == Lesplan_Teacher_Factory().getClassName() )
			sType = 'teacher'; // group should be made obsolete and replaced by Lesplan_Teacher_Factory().getClassName()

		var nExpectedId = null;
		if ( oExpected != null )
			nExpectedId = oExpected.getId();

		document.body.style.cursor = 'wait';

		$.ajax({
			url: g_sPubMap + 'planning/ajax/' + sUrlParamsPeriods,
			data: { 	ajaxaction : 'getJSONEvents', type : sType,	value : nExpectedId },
			async : false
		}).done(function( json ) {
			if ( oExpected != null ){
				var oEvents = Lesplan_Event_Factory().createObjectsFromJSON( jsonEvents );
				oExpected.putEvents( oEvents );
			}
		});

		document.body.style.cursor = 'auto';

		return oExpected.getEvents();
	}
	*/
}
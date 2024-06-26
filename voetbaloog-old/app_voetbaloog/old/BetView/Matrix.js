function Ctrl_BetView_Matrix( oParent ) 
{	
	var m_oParent = oParent;
	var m_nMarginLeft = 5;
	var m_oDiv = null;
	// var m_sControlPrefix = 'ctrl_betview_matrix_';
	var m_nRoundBetConfigHeaderHeight = 16;
	var m_nRoundBetConfigRowHeight = 16;
	var m_nUnitMargin = 1;
	
	var m_nWidthMarginLeftRight = 10;
	var m_nHeaderMargin = 10;
	var m_oGamesControl = new Ctrl_BetView_Matrix_Games( this );
	var m_oBetsControl = new Ctrl_BetView_Matrix_Bets( this );
	
	this.getDiv = function()
	{
		return m_oDiv;
	};
	
	this.getParent = function()
	{
		return m_oParent;
	};
	
	this.show = function()
	{
		if ( m_oDiv == null )
		{
			m_oDiv = DivHelper.create( m_oParent.getDiv(), null, 'floatleft', null, null );
			m_oDiv.style.backgroundColor = 'green';
			m_oDiv.style.marginLeft = m_nMarginLeft + 'px';
			// m_oDiv.appendChild( document.createTextNode( m_oDiv.offsetWidth ) );
			
			m_oDiv.style.width = ( m_oParent.getMatrixWidth() -2 ) + 'px';
			
			m_oGamesControl.show();
			
			// m_oMatrixControl.setWidth
			m_oBetsControl.show();
			
			var oDiv = DivHelper.create( m_oDiv, null, null, null, null );
			oDiv.style.clear = 'both';
		}
	};
	
	this.getMaxNrOfPoolUsers = function( nWidth )
	{
		nWidth -= m_oGamesControl.getWidth() + m_nWidthMarginLeftRight;
		
		var nMaxNrOfPoolUsers = nWidth / ( this.getPoolUserDivWidth() + this.getUnitMargin() );
		
		nMaxNrOfPoolUsers = Math.floor( nMaxNrOfPoolUsers );
		
		return nMaxNrOfPoolUsers;		
	};
	
	this.getBetsWidth = function()
	{
		var nWidth = parseInt( this.getDiv().style.width, 10 );
		nWidth -= m_oGamesControl.getWidth() + m_nWidthMarginLeftRight;
		return nWidth;		
	};
	
	
	// max numbers of characters for pooluser is 10
	this.getPoolUserDivWidth = function()
	{
		return m_oBetsControl.getPoolUserDivWidth();
	};
	
	this.getMarginLeft = function()
	{
		return m_nMarginLeft;
	};
	
	this.refresh = function( oPoolUserSrc, oPoolUserDest, nAction )
	{
		return m_oBetsControl.refresh( oPoolUserSrc, oPoolUserDest, nAction );		
	};
	
	this.refreshBackgrounds = function()
	{
		return m_oBetsControl.refreshBackgrounds();
	};
	
	this.getNrOfPoolUsers = function()
	{
		return m_oParent.getNrOfPoolUsers();
	};

	this.refreshGamesRoundBetConfig = function( oRoundBetConfig, bExpanded )
	{
		m_oGamesControl.refreshRoundBetConfig( oRoundBetConfig, bExpanded );
	};
	
	this.refreshPoolUsersRoundBetConfig = function( oRoundBetConfig, bExpanded )
	{
		var oPoolUsersToShow = m_oParent.getPoolUsersToShow();
		for ( var nI in oPoolUsersToShow )
		{
			if ( !( oPoolUsersToShow.hasOwnProperty( nI ) ) )
				continue;
			
			var oPoolUser = oPoolUsersToShow[nI];
			m_oBetsControl.refreshRoundBetConfig( oPoolUser, oRoundBetConfig, bExpanded );
		}
		return true; 
	};
	
	this.isRoundBetConfigExpanded = function( oRoundBetConfig )
	{
		return m_oGamesControl.isRoundBetConfigExpanded( oRoundBetConfig );
	};
	
	this.getUnitMargin = function()
	{
		return m_nUnitMargin;
	};
	
	this.getHeaderMargin = function()
	{
		return m_nHeaderMargin;
	};
	
	this.getRoundBetConfigHeaderHeight = function()
	{
		return m_nRoundBetConfigHeaderHeight;
	};
	
	this.getRoundBetConfigRowHeightBase = function()
	{
		return m_nRoundBetConfigRowHeight;
	};
	
	
	this.getRoundBetConfigRowHeight = function( oRoundBetConfig )
	{
		if ( oRoundBetConfig.getBetType() == VoetbalOog_Bet_Qualify.nId 
			&& oRoundBetConfig.getRound().getGamesByDate().length > 0
		)
		{
			return 2 * this.getRoundBetConfigRowHeightBase() + this.getUnitMargin();
		}
		return this.getRoundBetConfigRowHeightBase();
	};
}
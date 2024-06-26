function Ctrl_BetView( oPool, oPoolUser, sDivId, nWidth )
{
	var m_sDivId = sDivId;
	document.getElementById( m_sDivId ).style.width = nWidth + 'px';
	var m_oPoolUser = oPoolUser;
	var m_oPool = oPool;
	var m_nNrOfPoolUsers = null;
	var m_oPoolUsersControl = null;
	var m_oMatrixControl = null;
	var m_nMaxNrOfPoolUsers = null;
	var m_bInitialized = false;

	this.getDiv = function()
	{
		return document.getElementById( m_sDivId );
	};

	this.getPoolUsersToShow = function()
	{
		if ( m_oPoolUsersControl != null )
			return m_oPoolUsersControl.getPoolUsersToShow();

		var oPoolUsersToShow = new Array();
		{
			var oPoolUsers = this.getPool().getUsers();
			for ( var nI in oPoolUsers )
			{
				if ( !( oPoolUsers.hasOwnProperty( nI ) ) )
					continue;

				oPoolUsersToShow.push( oPoolUsers[nI] );
			}
		}
		return oPoolUsersToShow;
	};

	this.initUsers = function()
	{
		var arrPoolUsers = this.getPoolUsersToShow();
		for ( var nI in arrPoolUsers )
		{
			if ( !( arrPoolUsers.hasOwnProperty( nI ) ) )
				continue;

			this.refreshMatrix( arrPoolUsers[nI], null, Ctrl_BetView.nAppendPoolUser );
		}
	};

	this.show = function()
	{
		if ( m_bInitialized == false )
		{
			// document.body.style.cursor = 'wait';

			m_oMatrixControl = new Ctrl_BetView_Matrix( this );

			m_nMaxNrOfPoolUsers = this.getMaxNrOfPoolUsers( null );
			if ( this.getNrOfPoolUsers() > m_nMaxNrOfPoolUsers )
			{
				m_oPoolUsersControl = new Ctrl_BetView_PoolUsers( this );
				m_oPoolUsersControl.show();
				m_nMaxNrOfPoolUsers = this.getMaxNrOfPoolUsers( m_oPoolUsersControl );
			}

			m_oMatrixControl.show( );

			if ( m_oPoolUsersControl != null )
				m_oPoolUsersControl.initUsers();
			else
				this.initUsers();

			m_oMatrixControl.refreshBackgrounds();

			var oDiv = DivHelper.create( this.getDiv(), null, null, null, null );
			oDiv.style.clear = 'both';

			// document.body.style.cursor = 'auto';
			m_bInitialized = true;
		}
	};

	this.refreshMatrix = function( oPoolUserSrc, oPoolUserDest, nAction )
	{
		return m_oMatrixControl.refresh( oPoolUserSrc, oPoolUserDest, nAction );
	};

	this.refreshMatrixRoundBetConfig = function( oRoundBetConfig, bExpanded )
	{
		m_oMatrixControl.refreshGamesRoundBetConfig( oRoundBetConfig, bExpanded );

		return m_oMatrixControl.refreshPoolUsersRoundBetConfig( oRoundBetConfig, bExpanded );
	};

	this.refreshBackgrounds = function()
	{
		return m_oMatrixControl.refreshBackgrounds();
	};

	this.getPool = function()
	{
		return m_oPool;
	};

	this.getPoolUser = function()
	{
		return m_oPoolUser;
	};

	this.getNrOfPoolUsers = function()
	{
		if ( m_nNrOfPoolUsers == null )
		{
			m_nNrOfPoolUsers = this.getPool().getUsers( true ).length;
		}
		return m_nNrOfPoolUsers;
	};

	this.getMatrixWidth = function()
	{
		//console.log(this.getDiv().style.width);
		//console.log(parseInt( this.getDiv().style.width, 10 ));
		//var nWidth = parseInt( this.getDiv().style.width, 10 );
		var nWidthTmp = parseInt( this.getDiv().style.width, 10 );
		if ( m_oPoolUsersControl != null )
		{
			var nPoolUserControlWidth = parseInt( m_oPoolUsersControl.getDiv().style.width, 10 );
			nWidthTmp -= ( nPoolUserControlWidth + m_oMatrixControl.getMarginLeft() );
		}
		return nWidthTmp;
	};

	this.getMaxNrOfPoolUsers = function( oPoolUsersControl )
	{
		var nWidth = parseInt( this.getDiv().style.width, 10 );
		if ( oPoolUsersControl != null )
		{
			var nPoolUserControlWidth = parseInt( oPoolUsersControl.getDiv().style.width, 10 );
			nWidth -= ( nPoolUserControlWidth + m_oMatrixControl.getMarginLeft() );
		}
		return m_oMatrixControl.getMaxNrOfPoolUsers( nWidth );
	};

	this.getMaxNrOfPoolUsersExt = function()
	{
		return m_nMaxNrOfPoolUsers;
	};

	this.getNrOfPoolUsersShow = function()
	{
		return m_oPoolUsersControl.getNrOfPoolUsersShow();
	};

	/*
	function getControlId( oRoundBetConfig, oObject, sPostfix )
	{
		var sRetval = m_sDivId + m_sControlPrefix + oRoundBetConfig.getId() + "_" + oObject.getId();
		if ( sPostfix != null )
			sRetval += sPostfix;
		return sRetval;
	}
	*/
}

Ctrl_BetView.nMovePoolUserForwards = 1;
Ctrl_BetView.nMovePoolUserBackwards = 2;
Ctrl_BetView.nRemovePoolUser = 3;
Ctrl_BetView.nAppendPoolUser = 4;
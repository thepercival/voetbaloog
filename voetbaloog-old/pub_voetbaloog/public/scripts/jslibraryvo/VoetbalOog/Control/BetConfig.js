function Ctrl_BetConfig()
{
	var m_sControlRoundPrefix = '_roundid';
	var m_sControlBetTypePrefix = '_bettypeid';
	var m_arrCurrentBetTimes = new Array();
	var m_bWritable = false;
	var m_sClassName = null;

	var m_oCompetitionSeason = null;
	var m_oRoundBetConfigs = null;

    this.isWritable = function(){ return m_bWritable; };
	this.makeWritable = function(){ m_bWritable = true; };
	this.putClassName = function( sClassName ){ m_sClassName = sClassName; };
	this.putCompetitionSeason = function( oCompetitionSeason ){ m_oCompetitionSeason = oCompetitionSeason; };

	this.putRoundBetConfigs = function( oRoundBetConfigs ) { m_oRoundBetConfigs = oRoundBetConfigs; }
	function getBetConfigs( oRound ) {
		var oBetConfigs = m_oRoundBetConfigs[ oRound.getId() ];
		if ( oBetConfigs == undefined ) {
			oBetConfigs = new Array();
			m_oRoundBetConfigs[ oRound.getId() ] = oBetConfigs;
		}
		return oBetConfigs;
	}

	this.show = function( vtDivId )
	{
		var oDiv;
		if ( typeof ( vtDivId ) == 'string' )
			oDiv = document.getElementById( vtDivId );
		else
			oDiv = vtDivId;

		if ( oDiv == undefined )
			return;

		oDiv.innerHTML = "";
		// oDiv.innerHTML = "Hier komt het visuele tijdsschema";
		// oDiv.appendChild( document.createElement("br") );
		var oTable = oDiv.appendChild( document.createElement( "TABLE" ) );
		if ( m_sClassName != null )
			oTable.className = m_sClassName;

		this.addHeaderRow( oTable );

		var oRounds = m_oCompetitionSeason.getRounds();
		for ( var nI in oRounds )
		{
			if ( !( oRounds.hasOwnProperty( nI ) ) )
				continue;

			var oRound = oRounds[ nI ];
			this.addRow( oTable, oRound );
		}

		this.addFooterRow( oTable );
	};

	this.addHeaderRow = function( oTable )
	{
		var oRow = oTable.insertRow( oTable.rows.length );
		oRow.className = "tableheader";

		var oCell = document.createElement("TH");
		oCell.appendChild( document.createTextNode("ronde") );
		oRow.appendChild( oCell );

		var oCell = document.createElement("TH");
		oCell.appendChild( document.createTextNode("soort") );
		oRow.appendChild( oCell );

		var oCell = document.createElement("TH");
		oCell.appendChild( document.createTextNode("deadline") );
		oRow.appendChild( oCell );

		var oCell = document.createElement("TH");
		oCell.appendChild( document.createTextNode("pnt") );
        if ( this.isWritable() == false )
		    oCell.style.textAlign = "right";
		oRow.appendChild( oCell );
	};

	this.addRow = function( oTable, oRound )
	{
		var oBetConfigs = getBetConfigs( oRound );

		var bNameColumnAdded = false;
		var sCellClassName = 'roundline';
		if ( oRound.isFirst() == true )
			sCellClassName = null;

		var nPossibleBetTypes = VoetbalOog_BetType_Factory().getAll( oRound );
		var nBetType = 1;
		var nBetTypes = VoetbalOog_BetType_Factory().getAll();
		while ( nBetType <= nBetTypes )
		{
			if ( ( ( nBetType & nPossibleBetTypes ) == nBetType ) == false )
			{
				nBetType = nBetType * 2;
				continue;
			}

			var oCurrentBetConfig = oBetConfigs[ nBetType ];
			if ( this.isWritable() == false && oCurrentBetConfig == null )
			{
				nBetType = nBetType * 2;
				continue;
			}

			var oRow = oTable.insertRow( oTable.rows.length );
			oRow.id = getControlId( 'row', oRound, nBetType );
			oRow.marginBottom = '10px';
			if ( bNameColumnAdded == false )
			{
				var oTableCell = oRow.insertCell( oRow.cells.length );
				oTableCell.className = sCellClassName;

				var el = document.createElement('span');
				el.innerHTML = VoetbalOog_Round_Factory().getName( oRound );
				oTableCell.appendChild( el );
				oTableCell.rowSpan = getNrOfBetConfigs( oRound );
			}

			var oTableCell = oRow.insertCell( oRow.cells.length );
			if ( bNameColumnAdded == false )
				oTableCell.className = sCellClassName;
			if ( this.isWritable() == true )
			{
				var oCheckBox = document.createElement("input");
				{
					oCheckBox.id = getControlId( 'chkbettype', oRound, nBetType );
					oCheckBox.name = oCheckBox.id;
					oCheckBox.type = "checkbox";

					if ( oCurrentBetConfig != null )
						oCheckBox.checked = true;
				}
				oCheckBox.onchange = function(){ updateBetType( this ); };
				oTableCell.appendChild( oCheckBox );
			}

			var sDescription = VoetbalOog_BetType_Factory().getDescription( nBetType );
			oTableCell.appendChild( document.createTextNode( sDescription ) );

			var oTableCell = oRow.insertCell( oRow.cells.length );
			if ( bNameColumnAdded == false )
				oTableCell.className = sCellClassName;
			if ( this.isWritable() == true )
			{
				var oSelect = document.createElement("select");
				oSelect.id = getControlId( 'cbxbettime', oRound, nBetType );
				oSelect.name = oSelect.id;
				oSelect.onchange = function(){ updateBetTimes( this ); };

				var nPossibleBetTimes = VoetbalOog_BetTime_Factory().get( nBetType );
				var nBetTime = 1;
				var nBetTimes = VoetbalOog_BetTime_Factory().get();
				while ( ( nBetTime & nBetTimes ) == nBetTime )
				{
					if ( ( ( nBetTime & nPossibleBetTimes ) == nBetTime ) == false )
					{
						nBetTime = nBetTime * 2;
						continue;
					}

					var sDescription = VoetbalOog_BetTime_Factory().getDescription( nBetTime );
					oSelect.options.add( new Option( sDescription, nBetTime ) );

					nBetTime = nBetTime * 2;
				}
				oTableCell.appendChild( oSelect );
				updateBetTimeSelected( oRound, oCurrentBetConfig );
			}
			else if ( oCurrentBetConfig != null )
			{
				var nBetTime = oCurrentBetConfig.getBetTime();
				var sDescription = VoetbalOog_BetTime_Factory().getDescription( nBetTime );
				oTableCell.appendChild( document.createTextNode( sDescription ) );
			}

			var oTableCell = oRow.insertCell( oRow.cells.length );
			if ( bNameColumnAdded == false )
				oTableCell.className = sCellClassName;
			if ( this.isWritable() == true )
			{
				var oSelect = document.createElement("select");
				oSelect.id = getControlId( 'cbxpoints', oRound, nBetType );
				oSelect.name = oSelect.id;

				for ( var nJ = 1 ; nJ <= 60 ; nJ++ )
				{
					if ( nJ <= 6 || ( nJ <= 32 && ( nJ % 2 == 0 || nJ % 5 == 0 ) ) || nJ % 5 == 0 )
					{
						sText = nJ;
						if ( nJ < 10 )
							sText = "0" + nJ;
						var oOption = new Option( sText, nJ );
						if ( oCurrentBetConfig != null && oCurrentBetConfig.getPoints() == nJ )
							oOption.selected = true;
						oSelect.options.add( oOption );
					}
				}
				oTableCell.appendChild( oSelect );
			}
			else if ( oCurrentBetConfig != null )
			{
				oTableCell.style.textAlign = 'right';
				var sDescription = oCurrentBetConfig.getPoints();
				oTableCell.appendChild( document.createTextNode( sDescription ) );
			}

			bNameColumnAdded = true;
			nBetType = nBetType * 2;
		}
		if ( this.isWritable() == true )
			updateVisibility( oRound );
	};

	this.addFooterRow = function( oTable )
	{
		if ( this.isWritable() == true )
		{
			var oRow = oTable.insertRow( oTable.rows.length );
			var oCell = document.createElement("TH");
			oCell.style.textAlign = "center";
			oCell.colSpan = 4;

			var oButton = document.createElement("input");
			oButton.className = "btn btn-default"
			oButton.type = "submit";
			oButton.name = "btnsavebetconfigs";
			oButton.value = "alles opslaan";

			oCell.appendChild( oButton );

			oRow.appendChild( oCell );
		}
	};

	function updateVisibility( oRound )
	{
		var oBetConfigs = getBetConfigs( oRound );
		var nBetTypes = VoetbalOog_BetType_Factory().getAll( oRound );
		var nBetType = 1;

		while ( nBetType <= nBetTypes )
		{
			if ( ( ( nBetType & nBetTypes ) == nBetType ) == false )
			{
				nBetType = nBetType * 2;
				continue;
			}

			var oCurrentBetConfig = oBetConfigs[ nBetType ];

			var sDisplay = '';
			if ( oCurrentBetConfig == null ) //  make last part of row invisible
				sDisplay = 'none';

			var oControlBetTime = document.getElementById( getControlId( 'cbxbettime', oRound, nBetType ) );

			oControlBetTime.style.display = sDisplay;

			var oControlPoints = document.getElementById( getControlId( 'cbxpoints', oRound, nBetType ) );
			oControlPoints.style.display = sDisplay;

			nBetType = nBetType * 2;
		}
	}

	function updateBetType( oCheckBox )
	{
		var oRound = getRoundFromControlId( oCheckBox.id );
		var nBetType = getBetTypeFromControlId( oCheckBox.id );
		var oBetConfigs = getBetConfigs( oRound );
		var oBetConfig = oBetConfigs[ nBetType ];

		if ( oCheckBox.checked == true ) // add bettype to round
		{
			if ( oBetConfig == undefined || oBetConfig == null )
			{
				oBetConfig = VoetbalOog_Round_BetConfig_Factory().createObject();
				oBetConfig.putId( "__NEW__" + oRound.getId() + nBetType );
				//oBetConfig.putPoints( null );
				oBetConfig.putBetType( nBetType );
				oBetConfig.putBetTime( m_arrCurrentBetTimes[nBetType] );
				oBetConfig.putRound( oRound );
				//oBetConfig.putPool( null );
				oBetConfigs[ nBetType ] = oBetConfig;
			}
		}
		else // if ( oCheckBox.checked == false ) // remove bettype from round
		{
			if ( !( oBetConfig == undefined || oBetConfig == null ) )
				delete oBetConfigs[ oBetConfig.getBetType() ];
		}

		updateVisibility( oRound );
		updateBetTimeSelected( oRound, oBetConfig );
	}

	function updateBetTimes( oSelect )
	{
		var nSelectedBetTime = oSelect.options[oSelect.selectedIndex].value;
		var nBetType = getBetTypeFromControlId( oSelect.id );
		var nDependantBetTypes = VoetbalOog_BetType_Factory().getDependant( nBetType );

		// for setting correct bettime when creating betconfig
		m_arrCurrentBetTimes[nBetType] = nSelectedBetTime;

		var oRounds = m_oCompetitionSeason.getRounds();
		for ( var nI in oRounds )
		{
			if ( !( oRounds.hasOwnProperty( nI ) ) )
				continue;

			var oRound = oRounds[nI];
			var oBetConfigs = getBetConfigs( oRound );
			for ( var nJ in oBetConfigs )
			{
				if ( !( oBetConfigs.hasOwnProperty( nJ ) ) )
					continue;

				var oBetConfig = oBetConfigs[nJ];
				var nCurrentBetType = oBetConfig.getBetType();
				if ( ( nCurrentBetType & nDependantBetTypes ) == nCurrentBetType )
				{
					oBetConfig.putBetTime( nSelectedBetTime );
					updateBetTimeSelected( oRound, oBetConfig );
				}
			}
		}
	}

	function updateBetTimeSelected( oRound, oBetConfig )
	{
		if ( oBetConfig == null )
			return;

		var nBetType = oBetConfig.getBetType();

		var oSelect = document.getElementById( getControlId( 'cbxbettime', oRound, nBetType ) );

		var nBetTime = oBetConfig.getBetTime();
		// for setting correct bettime when creating betconfig
		m_arrCurrentBetTimes[nBetType] = nBetTime;

		for ( var nI = 0 ; nI < oSelect.options.length ; nI++ )
		{
			var oOption = oSelect.options[nI];
			if ( oOption.value == nBetTime )
				oOption.selected = true;
		}
	}



	function getControlId( sControlPrefix, oRound, nBetType )
	{
		return sControlPrefix + m_sControlRoundPrefix + oRound.getId() + m_sControlBetTypePrefix + nBetType;
	}

	function getRoundFromControlId( sControlId )
	{
		var sRoundId = sControlId.substring(
			sControlId.lastIndexOf( m_sControlRoundPrefix ) + m_sControlRoundPrefix.length,
			sControlId.lastIndexOf( m_sControlBetTypePrefix )
		);
		return m_oCompetitionSeason.getRounds()[ sRoundId ];
	}

	function getBetTypeFromControlId( sControlId )
	{
		return sControlId.substring(
			sControlId.lastIndexOf( m_sControlBetTypePrefix ) + m_sControlBetTypePrefix.length,
			sControlId.length
		);
	}

	function getNrOfBetConfigs( oRound )
	{
		var oBetConfigs = getBetConfigs( oRound );
		var nCount = 0;
		var nPossibleBetTypes = VoetbalOog_BetType_Factory().getAll( oRound );
		var nBetType = 1;
		var nBetTypes = VoetbalOog_BetType_Factory().getAll();
		while ( nBetType <= nBetTypes )
		{
			if ( ( ( nBetType & nPossibleBetTypes ) == nBetType ) == false )
			{
				nBetType = nBetType * 2;
				continue;
			}

			var oBetConfig = oBetConfigs[ nBetType ];
			if ( m_bWritable == false && oBetConfig == null )
			{
				nBetType = nBetType * 2;
				continue;
			}

			nBetType = nBetType * 2;
			nCount++;
		}
		return nCount;
	}
}
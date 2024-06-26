function Ctrl_Payments( sParentDivId, oPayments, nNrOfCompetitors )
{
	var m_oTable = null;
	var m_oMessageDiv;
	var m_arrPayments = new Array();
	var m_nMaxNrOfPlaces = 10;
	var m_nNrOfCompetitors = nNrOfCompetitors;

	init( sParentDivId );
	initPayments( oPayments );
	updateMessage();

	function init( sParentDivId )
	{
		var oParent = document.getElementById( sParentDivId );

		// init table and table header
		m_oTable = oParent.appendChild( document.createElement("TABLE") );
		m_oTable.className = "table table-striped";
		m_oTable.style.marginBottom = "10px";
		var oRow = m_oTable.insertRow( m_oTable.rows.length );
		oRow.className = "tableheader";
		oRow.appendChild( document.createElement("TH") ).appendChild(document.createTextNode("winplaats"));
		oRow.appendChild( document.createElement("TH") ).appendChild(document.createTextNode("winstuitkering"));
		oRow.appendChild( document.createElement("TH") ).appendChild(document.createTextNode(""));

		var oFooterRow = m_oTable.insertRow( m_oTable.rows.length );
		oFooterRow.className = "tableheader";
		var oCell = oFooterRow.appendChild( document.createElement("TH") );
		oCell.colSpan = 3;
		{
			var oAddBtn = oCell.appendChild( document.createElement("button") );
			oAddBtn.id = "payments_addrow_btn";
			oAddBtn.className = "btn btn-sm btn-default";
			oAddBtn.onclick = function(){
				m_arrPayments.push( 1 );
				addPaymentRow( m_arrPayments.length, 1 );
				updateMessage();
				return false;
			};
			oAddBtn.appendChild( document.createTextNode('voeg een winplaats toe') );
		}

		var oSubmit = oParent.appendChild( document.createElement( 'input' ) );
		oSubmit.type = 'submit';
		oSubmit.name = 'btnuitkeringen';
		oSubmit.className = "btn btn-default";
		oSubmit.value = "opslaan";

		m_oMessageDiv = oParent.appendChild( document.createElement("div") );
	}

	function initPayments( oPayments )
	{
		for ( var nI in oPayments )
		{
			if ( !( oPayments.hasOwnProperty( nI ) ) )
				continue;

			var oPayment = oPayments[ nI ];
			if ( oPayment.getPlace() != 0 )
				m_arrPayments.push( oPayment.getTimesStake() );
		}

		for ( var nPlace = 0 ; nPlace < m_arrPayments.length ; nPlace++ )
			addPaymentRow( nPlace + 1, m_arrPayments[ nPlace ] );
	}

	function addPaymentRow( nPlace, nTimes )
	{
		if ( placesLeft() == false )
			return;

		var lastRow = m_oTable.rows.length - 1;

		var row = m_oTable.insertRow( lastRow );

		// remove removebutton from previous row
		var oPreviousRmvBtn = document.getElementById( 'rmvbtn' + ( nPlace - 1 ) );
		if ( oPreviousRmvBtn != undefined )
			oPreviousRmvBtn.parentNode.removeChild( oPreviousRmvBtn );

		var oPlaceCell = row.insertCell(0);
		oPlaceCell.appendChild( document.createTextNode( '' + nPlace ) );
		var oHidden = oPlaceCell.appendChild( document.createElement( 'input' ) );
		oHidden.type = 'hidden';
		oHidden.id = 'paymentplace' + nPlace;
		oHidden.name = oHidden.id;
		oHidden.value = nTimes;

		var oTimesCell = row.insertCell(1);
		createSelectTimes( oTimesCell, nPlace, nTimes );

		var oRemoveCell = row.insertCell(2);
		oRemoveCell.id = 'rmvcell' + ( nPlace );
		if ( nPlace != 1 )
			addRemoveBtn( oRemoveCell, nPlace );

		if ( placesLeft() == false )
			document.getElementById( "payments_addrow_btn" ).style.display = 'none';
	}

	function addRemoveBtn( vtCell, nPlace )
	{
		if ( typeof vtCell == "string" )
			vtCell = document.getElementById( vtCell );
		var oRemoveBtn = vtCell.appendChild( document.createElement('button') );
		oRemoveBtn.id = 'rmvbtn' + nPlace;
		oRemoveBtn.className = 'btn btn-xs btn-default';
		oRemoveBtn.onclick = function(){
			m_oTable.deleteRow( nPlace );
			m_arrPayments.pop();
			if ( nPlace - 1 > 1 )
				addRemoveBtn( 'rmvcell' + ( nPlace - 1 ), nPlace - 1 );
			document.getElementById( "payments_addrow_btn" ).style.display = 'block';
			updateMessage();
			return false;
		};
		var oSpan = oRemoveBtn.appendChild( document.createElement('span') );
		oSpan.className = "glyphicon glyphicon-remove";
	}

	function placesLeft()
	{
		return ( m_arrPayments.length < m_nMaxNrOfPlaces );
	}

	function createSelectTimes( oContainer, nPlace, nTimes )
	{
		var oSelect = oContainer.appendChild( document.createElement("select") );
		oSelect.id = "selecttimesstake" + nPlace;
		oSelect.place = nPlace;
		oSelect.className = "form-control selecttimesstake";

		if ( nTimes == -1 )
		{
			var oOption = new Option( 'alle overige inleg ', -1 );
			oOption.selected = ( nTimes == -1 );
			oSelect.options.add( oOption );
			oSelect.disabled = true;
		}
		else
		{
			for ( var nI = 1 ; nI < 10 ; nI++ )
			{
				var oOption = new Option( '0' + nI + ' maal de inleg', nI );
				oOption.selected = ( nTimes == nI );
				oSelect.options.add( oOption );
			}

			var oOption = new Option( '10 maal de inleg', 10 );
			oOption.selected = ( nTimes == 10 );
			oSelect.options.add( oOption );

			var oOption = new Option( '15 maal de inleg', 15 );
			oOption.selected = ( nTimes == 15 );
			oSelect.options.add( oOption );

			var oOption = new Option( '20 maal de inleg', 20 );
			oOption.selected = ( nTimes == 20 );
			oSelect.options.add( oOption );

			var oOption = new Option( '25 maal de inleg', 25 );
			oOption.selected = ( nTimes == 25 );
		}

		oSelect.onchange = function(){
			var selIndex = this.selectedIndex;
			if ( selIndex != null )
			{
				console.log( this.place );
				var nTimes = parseInt( this.options[selIndex].value );
				m_arrPayments[ this.place ] = nTimes;
				document.getElementById( 'paymentplace' + this.place ).value = nTimes;
			}

			updateMessage();
		};

		return oSelect;
	}

	function updateMessage()
	{
		var nRestPaymentPlace = 1;
		var nNrOfIncorrectPlaces = m_arrPayments.length - m_nNrOfCompetitors;
		var nTotalTimes = 0;
		{
			for ( var nPlace = 0 ; nPlace < m_arrPayments.length ; nPlace++ ){
				var nTimes = m_arrPayments[ nPlace ];
				if ( nTimes > 0 )
					nTotalTimes += nTimes;
			}
		}

		var sMessage = "";
		if ( nNrOfIncorrectPlaces > 0 )
		{
			var sWinPlaces = 'is ' + nNrOfIncorrectPlaces + ' winplaats';
			if ( nNrOfIncorrectPlaces > 1 )
				sWinPlaces = 'zijn ' + nNrOfIncorrectPlaces + ' winplaatsen';

			var sLanguageExt = 'heeft zich nu ' + m_nNrOfCompetitors + ' deelnemer';
			if ( m_nNrOfCompetitors > 1 )
				sLanguageExt = 'hebben zich nu ' + m_nNrOfCompetitors + ' deelnemers';

			sMessage += "<div class='alert alert-warning'><p class='alert-link'>";
			sMessage += 'Er ' + sWinPlaces + ' gedefinieerd, waarvoor niet genoeg deelnemers zijn. ';
			sMessage += 'Er ' + sLanguageExt + ' ingeschreven, dit moeten ' + ( m_nNrOfCompetitors + nNrOfIncorrectPlaces ) + ' deelnemers worden. ';
			sMessage += 'Als deze situatie zich bij het begin van de competitie nog voordoet, worden de winplaatsen( > ' + m_nNrOfCompetitors + ' ) verwijderd.';
			sMessage += "</p></div>";
		}

		var nRestStakeTimes = m_nNrOfCompetitors - nTotalTimes;

		if ( nRestPaymentPlace != null && nRestStakeTimes <= 0 )
		{
			sMessage += "<div class='alert alert-warning'><p class='alert-link'>";
			sMessage += 'Voor winplaats ' + nRestPaymentPlace + ' blijft er geen inleg over. ';
			sMessage += 'Als deze situatie zich bij het begin van de competitie nog voordoet, worden de winplaatsen van laag naar hoog verwijderd.';
			sMessage += "</p></div>";
		}

		if ( sMessage.length > 0 )
		{
			m_oMessageDiv.innerHTML = sMessage;
			m_oMessageDiv.style.display = 'block';
		}
		else
		{
			m_oMessageDiv.style.display = 'none';
		}
	}
}
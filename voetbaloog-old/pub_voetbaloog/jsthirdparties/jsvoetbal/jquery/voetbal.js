/**
 * Run!
 */
jQuery.noConflict();
jQuery(document).ready(function ($) {

	if ($('.page-default-admin').length) {
		
		$(document).on( "click", 'button.edit', function(){
			var nId = $( this ).data("id");
			fillEditModal( nId, null, null );
		});
		
		function fillEditModal( nId, nExternId, nTeamId )
		{
			if ( nId == null ) {
				document.getElementById("form-title").innerHTML = "toevoegen";
				document.getElementById("btnsave").value = "add";
			}
			else {
				document.getElementById("form-title").innerHTML = "wijzigen";
				document.getElementById("btnsave").value = "edit";
			}
			
			document.getElementById("form-content").innerHTML = null;
			document.getElementById("form-waiting").style.display = "block";
			$('#editModal').modal('show');
			
			$.ajax({
				url: g_sPubMap + 'voetbal/'+ g_sEntity +'/ajax/',
				data: { method: "editform", id : nId, externid: nExternId, teamid: nTeamId },
				async : true
			}).done(function( sHtml ) {
				document.getElementById("form-waiting").style.display = "none";
				document.getElementById("form-content").innerHTML = sHtml;
				
				if ($('#page-admin-person').length || $('#page-admin-season').length ) {
					$('.input-group.date').datepicker({
						format: "D d M yyyy",
						language: "nl",
						autoclose: true
					}).on('changeDate',function ( evt ){
						// console.log(evt.target);
						// if ( evt.target.id == "playermembershipstartdatetime" || evt.target.id == "playermembershipenddatetime" )
							$(".input-group-addon.refresh").trigger("click");
					} );
				}

                if ( nTeamId > 0 ) {
                    $(".input-group-addon.refresh").trigger("click");
                }
			});
		}

		if ( $('#page-admin-person').length  ) {
			$(document).on( "change", '#playermembershipteamid', function(){
				$(".input-group-addon.refresh").trigger("click");
			});
		}


		$(document).on( "click", '.input-group-addon.refresh', function(){
			var sSelectId = $( this ).data("selectid");
			var sTeamSelectId = $( this ).data("teamselectid");
			var sTeamId = $( "#"+sTeamSelectId+" option:selected" ).attr("value");

			if ( sTeamId.length == 0 )
				return;
			
			var oSelect = $( "#" + sSelectId ); 
			oSelect.empty().append('<option value=""></option>');

			$.ajax({
				url: g_sPubMap + 'player/ajax/',
				data: { method: "getavailablebacknumbers", teamid : sTeamId },
				dataType: "json",
				async : true
			}).done(function( jsonAvBackNumbers ) {				
				$.each(jsonAvBackNumbers, function(nAvBackNumber, obj) {
					oSelect.append('<option value="' + nAvBackNumber + '">' + nAvBackNumber + '</option>');
				});
			});
		});
		
		if ( g_bAutoFill == true && g_nExternId != undefined )
			fillEditModal( null, g_nExternId, g_nTeamId );
		else if( g_nEditId != undefined && g_nEditId > 0 ){
			if ( $('#page-admin-person').length )
				fillEditModal( g_nEditId, null, g_nTeamId );
			else
				fillEditModal( g_nEditId, null, null );
		}
	}
	
	if ($('#page-admin-competitionseason').length) {

		$("ul.nav.nav-wizard > li > a[data-toggle='tab']").click(function () {

			var sAction = $(this).data("action");

			document.getElementById("wizard-content-waiting").style.display = "block";
			document.getElementById("wizard-content").innerHTML = null;
			$.ajax({
				url: g_sPubMap + 'admincompetitionseason/ajax/',
				data: {method: sAction, id: g_nCompetitionSeasonId},
				async: true
			}).done(function (sHtml) {
				document.getElementById("wizard-content-waiting").style.display = "none";
				document.getElementById("wizard-content").innerHTML = sHtml;
			});
		});

		// settings
		$(document).on("click", 'a#promotioninfo', function () {
			$('#promotionInfoModal').modal({});
		});

		$('.input-group.date').datepicker({
			format: "D d M yyyy",
			language: "nl",
			autoclose: true
		});
	}

	if ($('.page-import').length) {
		// Max input vars is 1000. So Only usable input data should be send
		$('form.minify').submit(function( event ) {
			
			var oForm = $( this );
			
			var sEntity = oForm.data("entity");
			
			var nNr = 0;
			var bFound = true;
			while ( bFound == true ){
				var jsonObject = {};
				$('*[data-'+ sEntity +'nr="' + nNr + '"]').each(function(){
					jsonObject[ $( this ).data("property") ] = this.value;
					this.parentNode.removeChild( this );	
				});
				
				var bFound = false;
				var sJSON = JSON.stringify( jsonObject );
				if ( sJSON.length > 2 ) {
					bFound = true;
					
					$('<input>').attr({
						type: 'hidden',
						name: sEntity + '-' + nNr,
						value: sJSON
					}).appendTo( oForm );
				}
				
				nNr++;
			}
			// event.preventDefault();
			return true;
		});
	}
	
	if ($('#page-admincompetition-update').length) {		
		$('#btn-season-add').click(function() {
			$('#addModal').modal( {} );			
		});
	}
		
	if ($('#page-admin-game').length) {
		
		$("#gameid").change(function(){
			this.form.submit();
		});

		$('.input-group.date').datepicker({
			format: "D d M yyyy",
			language: "nl",
			autoclose: true
		});
	
		$('td[data-playerid]').click(function() {
			var nPlayerId = $(this).data("playerid");
	
			$.ajax({
				url: g_sPubMap + 'voetbal/wedstrijd/ajax/',
				data: { method : 'deelname', gameid : nSelectedGameId, playerid : nPlayerId },
				async : true
			}).done(function( sRetVal ) {
				document.getElementById( "form-participation" ).innerHTML = sRetVal;				
				$('#participationModal').modal( {} );				
			});			
		})
		.css('cursor', 'pointer');
	}
	
	if ($('#page-admin-team').length) {
		
		if ( nNrOfPersons > 0 )
			$('#myTab a[href="#'+ sActiveTabId + '"]').tab('show');
		
		$(document).on( "click", 'button.editplayer', function(){
			var nPlayerId = $( this ).data("playerid");			
			if ( nPlayerId == null ) {
				document.getElementById("form-playermembership-title").innerHTML = "toevoegen";
				document.getElementById("btnsaveplayermembership").value = "add";
			}
			else {
				document.getElementById("form-playermembership-title").innerHTML = "wijzigen";
				document.getElementById("btnsaveplayermembership").value = "edit";
			}			
			var nPlayerTeamId = $( this ).data("playerteamid");
			
			document.getElementById("form-playermembership").innerHTML = null;
			document.getElementById("form-playermembership-waiting").style.display = "block";
			$('#playermembershipModal').modal('show');
			
			$.ajax({
				url: g_sPubMap + 'voetbal/team/ajax/',
				data: { method: "playermembershipform", playerteamid : nPlayerTeamId, playerid : nPlayerId },
				async : true
			}).done(function( sHtml ) {
				document.getElementById("form-playermembership-waiting").style.display = "none";
				document.getElementById("form-playermembership").innerHTML = sHtml;
				
				$('.input-group.date').datepicker({
					format: "D d M yyyy",
					language: "nl",
					autoclose: true
				});
			});
		});
	}
});
/*
20 seconden nemen voor importeren wedstrijden
dan vanaf begin wedstrijd kijken welk updatemoment aan de beurt is
hiervoor is de array nodig in de crontab, deze verplaatsen naar FCUPDATE

 */
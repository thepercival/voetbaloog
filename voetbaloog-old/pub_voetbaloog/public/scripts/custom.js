/**
 * Run!
 */
jQuery.noConflict();
jQuery(document).ready(function ($) {

	if ($('#page-home').length) {
		$(".clickable-cell").click(function() {
			window.document.location = $(this).closest('tr').attr("href");
		});
		$('.clickable-cell').css('cursor', 'pointer');		
	}

	if ($('#page-pool-stand').length || $('#page-pool-stand-allertijden').length || $('#page-pool-bets').length ) {
		// highlight all rows with table class pu-highlight and row class pu-?
		function changeCurrentUserRows(nUserId) {
			$("table.pu-highlight tr.pu-" + nUserId).addClass('success');
			$("table.pu-highlight").removeClass('pu-highlight');
		}
	}

	if ($('#page-pool-stand').length) {
		$(document).on("click", '.btn-stand-info', function () {
			$('#standInfoModal').modal({});
		});

        if ( g_bShowAlltimes ){
            $.ajax({
                url: g_sPubMap + 'pool/ajax/',
                data: {method: "getpooluseridalltimes", poolid: g_nPoolId},
                async: true
            }).done(function (sPoolUserId) {
                if( sPoolUserId == undefined || sPoolUserId.length == 0 ){
                    console.error("kon geen winnaar aller tijden vinden");
                }
                $("table#table-standings tr[data-pooluserid='" + sPoolUserId + "'] > td.pooluser-name")
                    .append( $("<span style='margin-left: 3px;'></span>")
                        .addClass("glyphicon glyphicon-queen")
                        .prop('title', 'eerste plaats ranglijst allertijden')
                        .tooltip()
                    );
            });
        }
	}

	if ($('#page-pool-stand-allertijden').length) {
		$(document).on( "click", '.btn-show-records', function(){
			showRecords( $( this).data("type") == "pos" );
		});

		function showRecords( bPositive )
		{
			var oDiv = null;
			if ( bPositive ) {
				oDiv = document.getElementById( "records-positive" );
				document.getElementById( "recordsModelHeader" ).innerHTML = "positieve records";
				document.getElementById( "records-negative" ).style.display = 'none';
			}
			else {
				oDiv = document.getElementById( "records-negative" );
				document.getElementById( "recordsModelHeader" ).innerHTML = "negatieve records";
				document.getElementById( "records-positive" ).style.display = 'none';
			}
			oDiv.style.display = 'block';

			$('#recordsModal').modal( {} );

			if ( $( oDiv ).data( "viewed" ).length > 0 )
				return;
			$( oDiv ).data( "viewed", "true" );

			$.ajax({
				url: g_sPubMap + 'pool/ajax/',
				data: { method: "getrecords", positive : bPositive, poolid: g_nPoolId },
				async : true
			}).done(function( sHtml ) {
				oDiv.innerHTML = sHtml;
				if ( g_nPoolUserId != null )
					changeCurrentUserRows( g_nUserId );
			});
		}
	}

	if ($('#page-pool-bets').length) {

		$('#btn-copy-bets').click(function() {
			$('#copyModal').modal();
		});

		g_oNowMinHalfHour.add( "m", -30 );
		g_oNowPlusHalfHour.add( "m", 30 );

		$('#btntogglebetconfig').click(function() {
			$('#pointsModal').modal( {} );

			if ( $('#betconfiginfo').data("loaded") == "1" )
				return;

			$.ajax({
				url: g_sPubMap + 'voorspelinstellingen/ajax/',
				data: { method : 'getdata', csid : g_nCompetitionSeasonId, poolid : g_oPool != null ?  g_oPool.getId() : null },
				dataType: "json",
				async : true
			}).done(function( json ) {
				var oBetConfigControl = new Ctrl_BetConfig();
				oBetConfigControl.putClassName( 'table table-striped' );
				var oRoundBetConfigs = VoetbalOog_Round_BetConfig_Factory().createObjectsFromJSON( json.data.roundbetconfigs );
				var oCompetitionSeason = VoetbalOog_CompetitionSeason_Factory().createObjectFromJSON( json.data.competitionseason );
				oBetConfigControl.putCompetitionSeason( oCompetitionSeason );
				oBetConfigControl.putRoundBetConfigs( oRoundBetConfigs );
				oBetConfigControl.show( 'betconfiginfo' );
				$('#betconfiginfo').data( "loaded", "1" );

			}).fail(function( jqXHR, textStatus, errorThrown ){
				console.log( textStatus );
				console.log( jqXHR.responseText );
			});
		});

		if ( g_bBetsReadable == true ) {

			var oBetView = new Ctrl_BetView(g_oPool, g_oNow);
			oBetView.show();

			$el = $("#betsviewheader");
			$('.nav-affix').affix({
				offset: {
					top: $el.offset().top + $el.outerHeight(true)
				}
			});

			$('body').scrollspy( { target: '#myScrollspy' } );

			oBetView.navigate( $, g_nBetViewRoundNr );

			$('.betsview-right div[data-teamid]').css("cursor","pointer");
			$('.betsview-right div[data-teamid]').click(function (e) {
				$('.betsview-right div[data-teamid]').removeClass( "teamselected" );
				$('.betsview-right div[data-teamid="'+ $(this).data("teamid")+'"]').addClass( "teamselected" );
			});
		}

		if ( g_bBetsEditable == true || g_bBetsReadable == true )
		{
			if (g_nPoolUserId != undefined) {
				g_oPoolUser = g_oPool.getUsers()[g_nPoolUserId];
				changeCurrentUserRows(g_nUserId);
			}
		}

		if ( g_bBetsEditable == true )
		{
			g_oRankableControl = new Ctrl_BetEdit( g_oPoolUser, g_oNow, sBetEditControlId );
			g_oRankableControl.putJQuery( $ );
			g_oRankableControl.putHelper( new Ctrl_BetHelper( 'div_bethelper', 'betsStatisticsModal' ) );
			g_oRankableControl.show();
		}

		if( g_bBetsEditable == true && g_bBetsReadable == true )
			$('#betsTab a[href="#'+ g_sBetTabId +'"]').tab('show');

		if ( bWelcome == true )
			$('#welcomeModal').modal( {} );
	}

	if ($('#page-competitionseason-index').length) {

		var oCompetitionSeason = VoetbalOog_CompetitionSeason_Factory().createObjectFromJSON( g_jsonCompetitionSeason );
		var jsonOptions = {};

		g_oRankableControl = new Ctrl_CompetitionSeasonView( oCompetitionSeason, g_oNow, 'csview', jsonOptions );
		g_oRankableControl.show();

		$.fn.scrollView = function () {
			return this.each(function () {
				$('html, body').animate({
					scrollTop: $(this).offset().top
				}, 1000);
			});
		}

		var oRoundInProgress =  g_oRankableControl.getRoundInProgress();
		if ( oRoundInProgress != null && oRoundInProgress.getNumber() > 0 ) {
			$('#cs-rounddivid-' + oRoundInProgress.getId() ).scrollView();
		}
	}

	if ( $('#page-pool-bets').length || $('#page-competitionseason-index').length ) {
		/* for changing windows size and showing tables */
		var g_sWinSize = '';

		window.onresize = function () {
			var sNewWinSize = 'xs'; // default value, check for actual size
			if ($(this).width() >= 1200) {
				sNewWinSize = 'lg';
			} else if ($(this).width() >= 992) {
				sNewWinSize = 'md';
			} else if ($(this).width() >= 768) {
				sNewWinSize = 'sm';
			}

			if (sNewWinSize != g_sWinSize) {

				if (sNewWinSize == 'sm' && g_sWinSize == 'xs' && g_oRankableControl != null ) {
					// alle standen naar rechts
					$(".poule-ranking-div").each(function () {
						if ($(this.parentElement).hasClass("well")) {
							var nPouleId = this.parentElement.getAttribute("data-pouleid");
							var sParentId = g_oRankableControl.getPouleRankingParentDivId(nPouleId);
							document.getElementById(sParentId).appendChild(this);
						}
					});
				}
				else if (sNewWinSize == 'xs' && g_sWinSize == 'sm' && g_oRankableControl != null ) {
					// alle standen naar links
					$(".poule-ranking-div").each(function () {
						if ($(this.parentElement).hasClass("well")) {
							return;
						}
						var nPouleId = this.parentElement.getAttribute("data-pouleid");
						var oRankingWell = document.getElementById("collapseRankingWell" + nPouleId);
						if (oRankingWell.parentElement.getAttribute("aria-expanded") == "true")
							oRankingWell.appendChild(this);
					});
				}
				g_sWinSize = sNewWinSize;
			}
		};
	}

	if ($('#page-pool-aanmaken').length) {
		// init
		$('#aanmaakTab a').click(function (e) {
			e.preventDefault();
			return false;
		});

		$('#errordiv_step2').hide();

		if ( g_bStepOneClick == true ) {
			$("#btn_step1_next").trigger( "click" );
		}

		// step 1
		$("#competitionseasonid").change(function () {
			var bValid = false;
			if ($('#competitionseasonid').val().length > 0)
				bValid = true;

			if (bValid == true) {
				$("#btn_step1_next").removeAttr("disabled");
				$('#errordiv_step1').hide();
			}
			else {
				$('#errordiv_step1').show();
				$("#btn_step1_next").attr("disabled", "disabled");
			}
		});

		$("#competitionseasonid").trigger('change');

		$("#btn_step1_next").click(function (e) {
			g_oPoolData.CompetitionSeasonId = $('#competitionseasonid').val();
			g_oPoolData.CompetitionSeasonName = $('#competitionseasonid').text();

			$('#step2_competitionseasonid').html(g_oPoolData.CompetitionSeasonName);

			g_bCanChooseExistingPool = fillAvailablePoolNames();
			if (g_bCanChooseExistingPool == true) {
				$('#form-group-name-both').show();
				$('#form-group-name-new').hide();

				$("#label-existingname").addClass('active');
				$("#label-newname").removeClass('active');
				$('#formgroup-newname').hide();
				$('#formgroup-existingname').show();
			}
			else {
				$('#form-group-name-new').show();
				$('#form-group-name-both').hide();
			}
			validateStep2();

			$('#aanmaakTab a[href="#step2"]').tab('show');
		});

		// step 2
		$("#nametypenew").change(function () {
			$('#formgroup-newname').show();
			$('#formgroup-existingname').hide();
		});
		$("#nametypeexisting").change(function () {
			$('#formgroup-newname').hide();
			$('#formgroup-existingname').show();
		});

		$("#newnamenew").keyup(function () {
			validateStep2();
			$('#errordiv_step2').hide();
		});
		$("#newname").keyup(function () {
			validateStep2();
			$('#errordiv_step2').hide();
		});
		$("#existingname").change(function () {
			validateStep2();
			$('#errordiv_step2').hide();
		});

		function fillAvailablePoolNames() {
			var bCanChooseExistingPool = false;
			$.ajax({
				url: g_sPubMap + 'pool/ajax/',
				data: {method: 'getavailables', competitionseasonid: g_oPoolData.CompetitionSeasonId},
				dataType: "json"
			}).done(function (jsonPools) {
				var sel = $("#existingname");
				var sFirstId = null;
				sel.empty();
				for (var sId in jsonPools) {
					if (jsonPools.hasOwnProperty(sId)) {
						if (sFirstId == null)
							sFirstId = jsonPools[sId].Id;
						sel.append('<option data-content="' + jsonPools[sId].Name + '" value="' + jsonPools[sId].Id + '">' + jsonPools[sId].Name + '</option>');
						bCanChooseExistingPool = true;
					}
				}
				// select first option
				if (bCanChooseExistingPool)
					sel.val(sFirstId);
			});
			return bCanChooseExistingPool;
		}

		function validateStep2() {

			var bValid = false;
			if (!g_bCanChooseExistingPool && $('#newnamenew').val().length >= 3)
				bValid = true;
			else if ($("#label-newname").hasClass("active") && $('#newname').val().length >= 3)
				bValid = true;
			else if ($("#label-existingname").hasClass("active") && $('#existingname').val().length > 0)
				bValid = true;

			if (bValid == true) {
				$("#btn_step2_next").removeAttr("disabled");
			}
			else {
				$("#btn_step2_next").attr("disabled", "disabled");
			}
		}

		$("#btn_step2_next").click(function (e) {

			if (!g_bCanChooseExistingPool || $("#label-newname").hasClass("active")) {
				var sNewName = g_bCanChooseExistingPool ? $('#newname').val() : $('#newnamenew').val();
                $.ajax({
					url: g_sPubMap + 'pool/ajax/',
					data: {
						method: 'isnameavailable',
                        newname: sNewName,
						competitionseasonid: g_oPoolData.CompetitionSeasonId
					}
				}).done(function (bIsNameAvailableParam) {
					var bIsNameAvailable = ( bIsNameAvailableParam == "true" );
                    if (bIsNameAvailable == false) // newpoolname is not available
                    {
                        $('#errordiv_step2').show();
                        return;
                    }
                    else {
                        g_oPoolData.Name = sNewName;
                        $('#step3_competitionseasonid').html(g_oPoolData.CompetitionSeasonName);
                        $('#step3_poolname').html(g_oPoolData.Name);
                        $('#aanmaakTab a[href="#step3"]').tab('show');
                    }
				});
			}
			else
            {
				g_oPoolData.Name = $("#existingname option:selected").text();
                $('#step3_competitionseasonid').html(g_oPoolData.CompetitionSeasonName);
                $('#step3_poolname').html(g_oPoolData.Name);
                $('#aanmaakTab a[href="#step3"]').tab('show');
            }
		});

		$("#btn_step2_previous").click(function (e) {
			$('#aanmaakTab a[href="#step1"]').tab('show');
		});

		$("#nametypenew").trigger('change');

		// step 3
		$( "#poolaanmakenform" ).submit(function( event ) {
			$("<input type='hidden' />")
				.attr("id", "competitionseasonid").attr("name", "competitionseasonid")
				.attr("value", g_oPoolData.CompetitionSeasonId )
				.prependTo( this );

			$("<input type='hidden' />")
				.attr("id", "name").attr("name", "name")
				.attr("value", g_oPoolData.Name )
				.prependTo( this );

			// event.preventDefault();
		});

		$("#btn_step3_previous").click(function( e ){
			$('#aanmaakTab a[href="#step2"]').tab('show');
		});
	}

	if ($('#page-admin-competitionseason-rounbbetconfigs').length) {
		$('.btn-updateroundbetconfigs').click(function() {
			$('#updateRBCsModal').modal( {} );

			var nCSId = $('.btn-updateroundbetconfigs').data("csid");
			document.getElementById('competitionseasonid').value = nCSId;

			$.ajax({
				url: g_sPubMap + 'voorspelinstellingen/ajax/',
				data: { method : 'getdata', csid : nCSId },
				dataType: "json",
				async : true
			}).done(function( json ) {
				var oBetConfigControl = new Ctrl_BetConfig();
				oBetConfigControl.makeWritable();
				oBetConfigControl.putClassName( 'table table-striped' );
				var oRoundBetConfigs = VoetbalOog_Round_BetConfig_Factory().createObjectsFromJSON( json.data.roundbetconfigs );
				var oCompetitionSeason = VoetbalOog_CompetitionSeason_Factory().createObjectFromJSON( json.data.competitionseason );
				oBetConfigControl.putCompetitionSeason( oCompetitionSeason );
				oBetConfigControl.putRoundBetConfigs( oRoundBetConfigs );

				oBetConfigControl.show( 'betconfigdiv' );

			}).fail(function( jqXHR, textStatus, errorThrown ){
				console.log( textStatus );
				console.log( jqXHR.responseText );
			});
		});
	}

	if ($('#page-admin-bets').length) {
		$('.btn-updatebets').click(function() {
			$('#updateBetsModal').modal( {} );
			$("#competitionseasonid").val( $('.btn-updatebets').data("csid") );
		});

		$('.input-group.date').datepicker({
			format: "D d M yyyy",
			language: "nl",
			autoclose: true
		});
	}

	if ($('#page-pool-admin-invite').length) {

		if( g_bWelcome == true )
			$('#welcomeModal').modal( {} );
	}

	if ($('#page-pool-admin-stakepayments').length) {

		if ( g_bPoolHasStarted != true && g_nPoolStake > 0 )
		{
			$.ajax({
				url: g_sPubMap + 'pool/ajax/',
				data: { method : 'getobject', poolid : g_nPoolId, dataflag : g_nDataFlag },
				dataType: "json",
				async : true
			}).done(function( jsonPool ) {
				var oPool = VoetbalOog_Pool_Factory().createObjectFromJSON( jsonPool );
				var oPaymentsControl = new Ctrl_Payments( 'parentpayments', oPool.getPayments(), g_nNrOfPoolUsers );
			});
		}
	}

	if ($('#page-pool-admin-poolusers').length) {
		$('#btn-send-reminder-open-modal').click(function (e) {
			$('#reminderModal').modal( {} );
		});
	}

	if ($('#page-pool-admin-betconfig').length) {


		$.ajax({
			url: g_sPubMap + 'voorspelinstellingen/ajax/',
			data: { method : 'getdata', csid : g_nCompetitionSeasonId, poolid : g_nPoolId },
			dataType: "json",
			async : true
		}).done(function( json ) {
			var oBetConfigControl = new Ctrl_BetConfig();
			if ( !g_bPoolHasStarted && !g_bBetsDone ) { oBetConfigControl.makeWritable(); }
			oBetConfigControl.putClassName( 'table table-striped' );
			var oRoundBetConfigs = VoetbalOog_Round_BetConfig_Factory().createObjectsFromJSON( json.data.roundbetconfigs );
			var oCompetitionSeason = VoetbalOog_CompetitionSeason_Factory().createObjectFromJSON( json.data.competitionseason );
			oBetConfigControl.putCompetitionSeason( oCompetitionSeason );
			oBetConfigControl.putRoundBetConfigs( oRoundBetConfigs );
			oBetConfigControl.show( 'betconfigdiv' );

		}).fail(function( jqXHR, textStatus, errorThrown ){
			console.log( textStatus );
			console.log( jqXHR.responseText );
		});
	}

	if( g_nUserId != null )
		setInterval(function(){ $.ajax({ url: g_sPubMap + 'user/extendsession/', }); }, 600000 ); // 600000 = 10 minuten

	$(window).on('beforeunload', function() {
		$('#loadingModal').modal( {} );
	});	
});
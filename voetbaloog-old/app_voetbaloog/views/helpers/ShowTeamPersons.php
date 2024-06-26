<?php

class Zend_View_Helper_ShowTeamPersons
{
	protected $m_sMainDivId = "teampersons";

	public function ShowTeamPersons( $oCompetitionSeason, $oTeam, $arrOptions = array() )
	{
		if ( $oCompetitionSeason === null
			or $oTeam === null
			or array_key_exists( "width", $arrOptions ) === false
			or array_key_exists( "widthpercentagephoto", $arrOptions ) === false
		)
			return "";

		$sBackgroundColorStaffMember = "lightgray";
		if ( array_key_exists( "backgroundcolorstaffmember", $arrOptions ) === true )
			$sBackgroundColorStaffMember = $arrOptions["backgroundcolorstaffmember"];

		$sBackgroundColorPlayer = "whitesmoke";
		if ( array_key_exists( "backgroundcolorplayer", $arrOptions ) === true )
			$sBackgroundColorPlayer = $arrOptions["backgroundcolorplayer"];

		$sBackgroundColorPlayerSelected = "#99CCFF";
		if ( array_key_exists( "backgroundcolorplayerselected", $arrOptions ) === true )
			$sBackgroundColorPlayerSelected = $arrOptions["backgroundcolorplayerselected"];

		$nNrOfColumns = 3;
		$nLeftMargin = 3;

		// haal coachperiods en haal playerperiods op
		$nCount = 0;
		$oMemberships = $oTeam->getMemberShips( $oCompetitionSeason );
		foreach( $oMemberships as $oMembershipsIt )
			$nCount += $oMembershipsIt->count();

		$nNrOfItemsPerColumn = ceil( $nCount / $nNrOfColumns );

		// 900 is containerwidth
		$nWidthGameDetails = 200;
		$nWidthPerColumn = floor( ( $arrOptions["width"] - $nWidthGameDetails ) / $nNrOfColumns );

		$nWidthPhoto = floor( ( $nWidthPerColumn / 100 ) * $arrOptions["widthpercentagephoto"] );

		$sHtml = "<div id=\"".$this->m_sMainDivId."\">";

		$bEndDivSet = true;
		$nCounter = 0;
		foreach( $oMemberships as $oMembershipsIt )
		{
			foreach( $oMembershipsIt as $oMembership )
			{
				if ( ( $nCounter % $nNrOfItemsPerColumn ) === 0 )
				{
					$bEndDivSet = false;
					$sHtml .= "<div style=\"float:left;\">";
				}

				$sBackgroundColor = null;
				if ( $oMembership instanceof Voetbal_Team_Membership_Player )
					$sBackgroundColor = $sBackgroundColorPlayer;
				else if ( $oMembership instanceof Voetbal_Team_Membership_StaffMember )
					$sBackgroundColor = $sBackgroundColorStaffMember;

				$sInnerDiv = null;
				$sOnClick = null;
				$sStyle = "style=\"margin-top:5px; width:".( $nWidthPerColumn - $nLeftMargin )."px; margin-left:".$nLeftMargin."px; background-color:".$sBackgroundColor.";";

				if ( $oMembership instanceof Voetbal_Team_Membership_Player )
				{
					$oGames = $oMembership->getGames();

					$nNrOfGames = $oGames->count();
					$nNrOfGoals = 0;
					if ( $nNrOfGames > 0 )
					{
						$oOptions = Construction_Factory::createOptions();
						$oOptions->addFilter( "VoetbalOog_Goal::OwnGoal", "EqualTo", false );
						$oOptions->addFilter( "VoetbalOog_Goal::TeamMembershipPlayer", "EqualTo", $oMembership );
						$oOptions->addFilter( "VoetbalOog_Goal::Game", "EqualTo", $oGames );
						$nNrOfGoals = VoetbalOog_Goal_Factory::getNrOfObjectsFromDatabase( $oOptions );
					}

					$sOnClick = "onclick=\"changeBackgroundColor( this ); refreshGameDetails( ".$oMembership->getId()." );\" ";
					$sStyle .= " cursor:pointer;";

					$sInnerDiv = "
						<div style=\"overflow:none; white-space:nowrap; border-bottom:1px solid gray;\">
							" . $oMembership->getClient()->getFullName()."
						</div>
						<div style=\"float:left;\">
							<img src=\"".Zend_Registry::get("baseurl")."image/generatecaptcha/playermembershipid/".$oMembership->getId()."/width/".$nWidthPhoto."/\" alt=\"foto\">
						</div>
						<div style=\"float:left; width:".( $nWidthPerColumn - ( $nLeftMargin + $nWidthPhoto ) )."px;\">
							<table>
							<tr><td>Rugnr</td><td>:</td><td>" . $oMembership->getBackNumber() . "</td></tr>
							<tr><td>Goals</td><td>:</td><td>".$nNrOfGoals."</td></tr>
							<tr><td>Gspld</td><td>:</td><td>" . $nNrOfGames . "</td></tr>
							</table>
						</div>
						<div style=\"clear:both;\"></div>
					";
				}
				else if ( $oMembership instanceof Voetbal_Team_Membership_StaffMember )
				{
					$sInnerDiv = "
						<div style=\"overflow:none; border-bottom:1px solid gray;\">
							" . $oMembership->getClient()->getFullName()."
						</div>
						<div style=\"float:left;\">
							<img src=\"".Zend_Registry::get("baseurl")."image/generatecaptcha/staffmembershipid/".$oMembership->getId()."/width/".$nWidthPhoto."/\" alt=\"foto\">
						</div>
						<div style=\"float:left; width:".( $nWidthPerColumn - ( $nLeftMargin + $nWidthPhoto ) )."px;\">
							<table>
							<tr><td>Functie</td><td>:</td><td>" . $oMembership->getFunctionX() . "</td></tr>
							<tr><td colspan=3>&nbsp;</td></tr>
							<tr><td colspan=3>&nbsp;</td></tr>
							</table>
						</div>
						<div style=\"clear:both;\"></div>
					";
				}


				$sStyle .= "\"";

				$sPeriodDiv = "<div " . $sStyle . " " . $sOnClick . ">";
				$sPeriodDiv .= $sInnerDiv;
				$sPeriodDiv .= "</div>";

				$sHtml .= $sPeriodDiv;

				$nCounter++;
				if ( ( ( $nCounter ) % $nNrOfItemsPerColumn ) === 0 )
				{
					$sHtml .= "</div>";
					$bEndDivSet = true;
				}
			}
		}
		if ( $bEndDivSet === false )
			$sHtml .= "</div>";


		$sHtml .= "<div style=\"float:left;\">";
		$sHtml .= 	"<div id=\"gamedetails\" style=\"background-color:".$sBackgroundColorPlayerSelected."; margin-top:5px; width:".( $nWidthGameDetails - $nLeftMargin )."px; margin-left:".$nLeftMargin."px;\">";
		$sHtml .= 		"<div style='text-align:center;'>wedstrijddetails:<br><br>klik<br>met<br>de<br>muis<br>op<br>een<br>speler<br>om<br>de<br>wedstrijddetails<br>van<br>de<br>speler<br>te<br>bekijken</div>";
		$sHtml .= 	"</div>";
		$sHtml .= "</div>";

		$sHtml .= "<div style=\"clear:both;\">";
		$sHtml .= "</div>";

		$sHtml .= "</div>";

		// oHeader.style.textDecoration = 'underline';
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_Round::CompetitionSeason", "EqualTo", $oCompetitionSeason );
		$oGames = Voetbal_Game_Factory::createObjectsFromDatabaseExt( $oTeam, $oOptions, "Voetbal_Team" );

		return $this->getJS( $oGames, $arrOptions ) . $sHtml;
	}

	protected function getJS( $oGames, $arrOptions )
	{
		$sBackgroundColorPlayer = "whitesmoke";
		if ( array_key_exists( "backgroundcolorplayer", $arrOptions ) === true )
			$sBackgroundColorPlayer = $arrOptions["backgroundcolorplayer"];

		$sBackgroundColorPlayerSelected = "#99CCFF";
		if ( array_key_exists( "backgroundcolorplayerselected", $arrOptions ) === true )
			$sBackgroundColorPlayerSelected = $arrOptions["backgroundcolorplayerselected"];

		$nDataFlag = VoetbalOog_JSON::$nGame_Participations;
		$nDataFlag += VoetbalOog_JSON::$nGame_Goals;
		$sJSON = Voetbal_Game_Factory::convertObjectsToJSON( $oGames, $nDataFlag );

		$sJS = "<script type=\"text/javascript\">";

		$sJS .= "var g_oGames = Voetbal_Game_Factory().createObjectsFromJSON( ".$sJSON." );";

		$sJS .= "
			var g_oGamePopup = new Ctrl_Popup_Dynamic( null, null );
			var oPopupDiv = g_oGamePopup.getPopupDiv();
			oPopupDiv.removeChild( oPopupDiv.firstChild );
			oPopupDiv.removeChild( oPopupDiv.lastChild );
			// var nLegendaWidth = 250;
			// g_oInfoPopup.putOffSet( new Point( - ( nLegendaWidth - document.getElementById( 'legenda' ).offsetWidth + ( nToolTipBorder * 2 ) ), document.getElementById( 'legenda' ).offsetHeight ) );
			oPopupDiv.className = 'popupDiv';
		";

		$sJS .= "
			function changeBackgroundColor( oDiv )
			{
				var oMainDiv = document.getElementById( '".$this->m_sMainDivId."' );
				var oChildNodes = oMainDiv.childNodes;
				for ( var nI = 0 ; nI < oChildNodes.length ; nI++ )
				{
					var oChildNode = oChildNodes[nI];

					var oGrantChildNodes = oChildNode.childNodes;
					for ( var nJ = 0 ; nJ < oGrantChildNodes.length ; nJ++ )
					{
						var oGrantChildNode = oGrantChildNodes[nJ];

						if ( oGrantChildNode.id == 'gamedetails' )
							continue;

						if ( oGrantChildNode != undefined && oGrantChildNode.style != undefined )
							oGrantChildNode.style.backgroundColor = '".$sBackgroundColorPlayer."';
					}
				}
				oDiv.style.backgroundColor = '".$sBackgroundColorPlayerSelected."';
			}

			function refreshGameDetails( nTeamMembershipPlayerId )
			{
				var oDiv = document.getElementById( 'gamedetails' );
				if ( oDiv == undefined )
					return;

				while ( oDiv.hasChildNodes() )
					oDiv.removeChild( oDiv.lastChild );

				var oPhotoDiv = oDiv.appendChild( document.createElement( 'div' ) );
				var oImg = oPhotoDiv.appendChild( document.createElement('img') );
				oImg.src = '".Zend_Registry::get("baseurl")."image/generatecaptcha/playermembershipid/ ' + nTeamMembershipPlayerId + '/width/ ' + oDiv.offsetWidth + '/';
				oImg.alt = 'foto x';

				var oHeaderDiv = oDiv.appendChild( document.createElement( 'div' ) );
				oHeaderDiv.style.overflow = 'none';
				oHeaderDiv.style.borderBottom = '1px solid gray';
				oHeaderDiv.innerHTML = 'Wedstrijddetails';

				var bNoGames = true;
				for( var nI in g_oGames )
				{
					var oGame = g_oGames[nI];

					var bShow = false;

					var oParticipations = oGame.getParticipations();
					for( var nJ in oParticipations )
					{
						var oParticipation = oParticipations[nJ];
						if ( oParticipation.getTeamMembershipPlayer().getId() == nTeamMembershipPlayerId )
						{
							bShow = true;
							break;
						}
					}

					if ( bShow == true )
					{
						var sClassName = 'scheduledgame';

						VoetbalOog_Control_Factory().appendGame( oDiv, oGame, sClassName );

						g_oGamePopup.connectDiv( oDiv, function( json )
							{
								while ( json.popup.body.hasChildNodes() )
									json.popup.body.removeChild( json.popup.body.lastChild );
								Ctrl_GameView().show( json.popup.body, json.game );
							},
							{ game : oGame, popup : g_oGamePopup }
						);

						bNoGames = false;
					}
				}

				if ( bNoGames == true )
				{
					var oTmpDiv = oDiv.appendChild( document.createElement( 'div' ) );
					oTmpDiv.style.color = 'red';
					oTmpDiv.innerHTML = 'Deze speler heeft nog geen wedstrijden gespeeld.';
				}
			}
		";
		return $sJS . "</script>";
	}
}
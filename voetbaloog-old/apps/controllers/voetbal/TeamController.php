<?php

class Voetbal_TeamController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$this->pre();

		$sBtnSave = $this->getParam('btnsave');
		if ( strlen ( $sBtnSave ) > 0 ) {
			if ( $sBtnSave === "add" )
				$this->add();
			else if ( $sBtnSave === "edit" )
				$this->update();
			else if ( $sBtnSave === "remove" )
				$this->remove();
		}
		else if ( strlen ( $this->getParam('btndeletestaffmembership') ) > 0 )
			$this->deleteStaffMemberMembership();
		else if ( strlen( $this->getParam("editid") ) > 0 )
			$this->view->editid = (int) $this->getParam("editid");
	}

	protected function pre()
	{
		$oFilters = Construction_Factory::createOptions();
		$oFilters->addOrder( "Voetbal_Team::Name", false );
		$this->view->oTeams = Voetbal_Team_Factory::createObjectsFromDatabase( $oFilters );

		$this->sort();
	}

	protected function sort()
	{
		$this->view->oTeams->uasort(
			function( $oTeamA, $oTeamB )
			{
				return ( $oTeamA->getName() < $oTeamB->getName() ? -1 : 1 );
			}
		);
	}

	protected function add()
	{
		$sName = $this->getParam('name');
		if ( strlen ( $sName ) == 0 )
			$this->view->errormessage = "naam is niet ingevuld";
		if ( mb_strlen( $sName, "UTF-8" ) > Voetbal_Team::MAX_NAME_LENGTH )
			$this->view->errormessage = "naam is te lang";

		if ( $this->view->errormessage !== null )
			return;

		$oDbWriter = Voetbal_Team_Factory::createDbWriter();
		$this->view->oTeams->addObserver( $oDbWriter );

		$oTeam = Voetbal_Team_Factory::createObject();
		$oTeam->putId( "__new__" );
		$oTeam->putName( $sName );
		$oTeam->putAbbreviation( $this->getParam("abbreviation") );
		$oTeam->putImageName( $this->getParam("imagename") );

		$nAssociationId = (int) $this->getParam("associationid");
		if ( $nAssociationId > 0 )
			$oTeam->putAssociation( $nAssociationId );

		$this->view->oTeams->add( $oTeam );

		try
		{
			$oDbWriter->write();
			$this->view->savemessage = "het team is toegevoegd";
			$this->sort();
		}
		catch ( Exception $e)
		{
			$this->view->oTeams->remove( $oTeam );
			$this->view->errormessage = "team ".$sName." kon niet worden toegevoegd: " . $e->getMessage();
		}
	}

	protected function update()
	{
		$sName = $this->getParam('name');
		if ( strlen ( $sName ) == 0 )
			$this->view->errormessage = "naam is niet ingevuld";
		if ( mb_strlen( $sName, "UTF-8" ) > Voetbal_Team::MAX_NAME_LENGTH )
			$this->view->errormessage = "naam is te lang";
		$oTeam = null;
		{
			$nId = (int) $this->getParam("teamid");
			if ( $nId > 0 )
				$oTeam = $this->view->oTeams[ $nId ];
		}
		if ( $oTeam === null )
			$this->view->errormessage = "team kon niet gevonden worden";

		if ( $this->view->errormessage !== null )
			return;

		$oDbWriter = Voetbal_Team_Factory::createDbWriter();
		$oTeam->addObserver( $oDbWriter );

		$oTeam->putName( $sName );
		$oTeam->putAbbreviation( $this->getParam("abbreviation") );
		$oTeam->putImageName( $this->getParam("imagename") );

		$nAssociationId = (int) $this->getParam("associationid");
		if ( $nAssociationId > 0 )
			$oTeam->putAssociation( $nAssociationId );

		try
		{
			$oDbWriter->write();
			$this->view->savemessage = "het team is gewijzigd";
		}
		catch ( Exception $e)
		{
			$this->view->errormessage = "team ".$oTeam->getName()." kon niet worden gewijzigd: " . $e->getMessage();
		}
	}

	protected function remove()
	{
		$oDbWriter = Voetbal_Team_Factory::createDbWriter();
		$this->view->oTeams->addObserver( $oDbWriter );

		$oTeam = null;
		{
			$nId = (int) $this->getParam("teamid");
			if ( $nId > 0 )
				$oTeam = $this->view->oTeams[ $nId ];
		}
		if ( $oTeam === null )
			$this->view->errormessage = "team kon niet gevonden worden";

		if ( $this->view->errormessage !== null )
			return;

		$this->view->oTeams->remove( $oTeam );

		try {
			$oDbWriter->write();
			$this->view->savemessage = "team ".$oTeam->getName()." is verwijderd";
		}
		catch ( Exception $e ){
			$this->view->errormessage = "team ".$oTeam->getName()." kon niet worden verwijderd: " . $e->getMessage();
		}
	}

	public function updateAction()
	{
		$this->getResponse()->insert("extrajsincludes", $this->_helper->GetDateIncludes("js") );
		$this->getResponse()->insert("extracssincludes", $this->_helper->GetDateIncludes("css", false) );

		$this->view->tabidstaffmemberships = "staffmembers";
		$this->view->tabidplayermemberships = "playermemberships";

		$this->view->activetabid = $this->getParam( "activetabid" );

		$this->pre();

		$nTeamId = (int) $this->getParam("teamid");
		if ( $nTeamId > 0 )
			$this->view->oTeam = $this->view->oTeams[ $nTeamId ];

		if ( $this->view->oTeam === null )
		{
			$this->render( 'index');
			return;
		}

		$this->view->oPlayerMemberships = $this->view->oTeam->getPlayerMemberships( null );
		$nPlayerMembershipId = (int) $this->getParam("playermembershipid");
		if ( $nPlayerMembershipId > 0 )
			$this->view->oPlayerMembership = $this->view->oPlayerMemberships[ $nPlayerMembershipId ];

		$this->view->oStaffMemberMemberships = $this->view->oTeam->getStaffMemberships( null );
		$nStaffMemberMembershipId = (int) $this->getParam("staffmembershipid");
		if ( $nStaffMemberMembershipId > 0 )
			$this->view->oStaffMemberMembership = $this->view->oStaffMemberMemberships[ $nStaffMemberMembershipId ];
		$this->view->oFunctions = Patterns_Factory::createCollectionExt(
			"Trainer",
			"Leider",
			"Fysio"
		);

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addOrder( "Voetbal_Association::Name", false );
		$this->view->oAssociations = Voetbal_Association_Factory::createObjectsFromDatabase( $oOptions );

		if ( strlen ( $this->getParam('btnimportplayerphotos') ) > 0 )
			throw new Exception("function is not in import-moduke, now needs implementing...", E_ERROR );
		else if ( strlen ( $this->getParam('btndeleteplayermembership') ) > 0 )
			$this->deletePlayerMembership();
		else if ( strlen ( $this->getParam('btnsaveplayermembership') ) > 0 ) {
			if ( $this->getParam('btnsaveplayermembership') === "add" )
				$this->addPlayerMembership();
			else if ( $this->getParam('btnsaveplayermembership') === "edit" )
				$this->updatePlayerMembership();
		}
		else if ( strlen ( $this->getParam('btnsavestaffmemberplayermembership') ) > 0 ) {
			if ( $this->getParam('btnsavestaffmembermembership') === "add" )
				$this->addStaffMemberMembership();
			else if ( $this->getParam('btnsavestaffmembermembership') === "edit" )
				$this->updateStaffMemberMembership();
		}
		else if ( strlen ( $this->getParam('btndeletestaffmembership') ) > 0 )
			$this->deleteStaffMemberMembership();
	}

	public function ajaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		if ( $this->getParam('method') === "playermembershipform" )
		{
			$this->view->playermembershipteamid = (int) $this->getParam('playerteamid');
			$nPlayerId = (int) $this->getParam('playerid');
			if ( $nPlayerId > 0 )
				$this->view->oPlayer = Voetbal_Team_Membership_Player_Factory::createObjectFromDatabase( $nPlayerId );

			$sHtml = $this->view->render( "voetbal/team/lid/speler.phtml" );
			echo $sHtml;
		}
		else if ( $this->getParam('method') === "editform" )
		{
			$nId = (int) $this->getParam('id');
			if ( $nId > 0 )
				$this->view->oTeam = Voetbal_Team_Factory::createObjectFromDatabase( $nId );

			echo $this->render( "edit" );
		}
		else{
			echo "no input-param 'method'";
		}
	}

	protected function addPlayerMembership()
	{
		$this->view->playermembershiperrormessage = $this->_helper->checkPlayerMembershipInput();

		if ( $this->view->playermembershiperrormessage === null )
		{
			$this->view->oPlayerMembership = Voetbal_Team_Membership_Player_Factory::createObject();
			$this->view->oPlayerMembership->putId( "__NEW__" );
			$this->view->oPlayerMembership->putClient( $this->view->playermembershippersonid );
			$this->view->oPlayerMembership->putStartDateTime( $this->view->playermembershipstartdatetime );
			$this->view->oPlayerMembership->putEndDateTime($this->view->playermembershipenddatetime );
			$this->view->oPlayerMembership->putLine( $this->view->playermembershipline );
			$this->view->oPlayerMembership->putBackNumber( $this->view->playermembershipbacknumber );
			$this->view->oPlayerMembership->putProvider( $this->view->playermembershipteamid );

			$oDbWriter = Voetbal_Team_Membership_Player_Factory::createDbWriter();
			$this->view->oPlayerMemberships->addObserver( $oDbWriter );

			$this->view->oPlayerMemberships->add( $this->view->oPlayerMembership );

			try
			{
				if ( $oDbWriter->write() === true )
				{
					// $oDbWriter = Voetbal_Team_Membership_Player_Factory::createDbWriter();
					// $this->view->oPlayerMembership->addObserver( $oDbWriter );
					// $this->saveImage( "playermembershipphoto", $this->view->oPlayerMembership, false, $sMessage );
					// if ( $sMessage === null )
						// $oDbWriter->write();

					$this->view->playermembershipsavemessage = "speler ".$this->view->oPlayerMembership->getClient()->getFullName()." toegevoegd";
					// if ( $sMessage !== null )
						// $this->view->playermembershipsavemessage .= "<br>" . $sMessage;
				}
			}
			catch( Exception $e )
			{
				$this->view->playermembershiperrormessage = "speler kon niet worden toegevoegd: " . $e->getMessage();
			}
		}
	}

	protected function updatePlayerMembership()
	{
		$this->view->playermembershiperrormessage = $this->_helper->checkPlayerMembershipInput( true );

		if ( $this->view->playermembershiperrormessage === null )
		{
			$oDbWriter = Voetbal_Team_Membership_Player_Factory::createDbWriter();
			$this->view->oPlayerMembership->addObserver( $oDbWriter );
/*
			$bRemoveAvatar = ( $this->getParam( "removeplayermembershipphoto" ) === "on" );
			$this->saveImage( "playermembershipphoto", $this->view->oPlayerMembership, $bRemoveAvatar, $sMessage );
			if ( $sMessage !== null )
				$this->view->playermembershiperrormessage = $sMessage;
*/
			if ( $this->view->playermembershiperrormessage !== null )
				return;

			// $this->view->oPlayerMembership->putClient( $this->view->playermembershippersonid );
			$this->view->oPlayerMembership->putStartDateTime( $this->view->playermembershipstartdatetime );
			$this->view->oPlayerMembership->putEndDateTime($this->view->playermembershipenddatetime );
			$this->view->oPlayerMembership->putBackNumber( $this->view->playermembershipbacknumber );
			$this->view->oPlayerMembership->putLine( $this->view->playermembershipline );

			try
			{
				if ( $oDbWriter->write() === true )
					$this->view->playermembershipsavemessage = "speler ".$this->view->oPlayerMembership->getClient()->getFullName()." aangepast";
			}
			catch( Exception $e )
			{
				$this->view->playermembershiperrormessage = "speler kon niet worden gewijzigd: " . $e->getMessage();
			}
		}
	}

	protected function deletePlayerMembership()
	{
		// kan alleen als er geen wedstrijden en goals op deze playermemberships aanwezig zijn
		$oDbWriter = Voetbal_Team_Membership_Player_Factory::createDbWriter();
		$this->view->oPlayerMemberships->addObserver( $oDbWriter );

		$this->view->oPlayerMemberships->remove( $this->view->oPlayerMembership );

		try
		{
			if ( $oDbWriter->write() === true )
			{
				$this->view->playermembershiplistsavemessage = "speler lidmaatschap verwijderd";
				$this->view->playermembershiplistsavemessage .= "<br>Add check if player has games or goals.";
			}
		}
		catch( Exception $e )
		{
			$this->view->playermembershiplisterrormessage = "speler kon niet verwijderd worden. speler heeft al wedstrijden gespeeld.<br>Je kunt de speler inactief maken om de speler te verbergen.";
		}
		$this->view->oPlayerMembership = null;
	}

	protected function addStaffMemberMembership()
	{
		$this->view->staffmembershiperrormessage = $this->checkStaffMemberMembershipInput();

		if ( $this->view->staffmembershiperrormessage === null )
		{
			$this->view->oStaffMemberMembership = Voetbal_Team_Membership_StaffMember_Factory::createObject();
			$this->view->oStaffMemberMembership->putId( "__NEW__" );
			$this->view->oStaffMemberMembership->putClient( $this->view->staffmembershippersonid );
			$this->view->oStaffMemberMembership->putStartDateTime( $this->view->staffmembershipstartdatetime );
			$this->view->oStaffMemberMembership->putEndDateTime($this->view->staffmembershipenddatetime );
			$this->view->oStaffMemberMembership->putFunctionX( $this->view->staffmembershipfunctionx );
			$this->view->oStaffMemberMembership->putImportance( $this->view->staffmembershipimportance );
			$this->view->oStaffMemberMembership->putProvider( $this->view->oTeam );

			$oDbWriter = Voetbal_Team_Membership_StaffMember_Factory::createDbWriter();
			$this->view->oStaffMemberMemberships->addObserver( $oDbWriter );

			$this->view->oStaffMemberMemberships->add( $this->view->oStaffMemberMembership );

			if ( $oDbWriter->write() === true )
			{
				$oDbWriter = Voetbal_Team_Membership_StaffMember_Factory::createDbWriter();
				$this->view->oStaffMemberMembership->addObserver( $oDbWriter );
				$this->saveImage( "staffmembershipphoto", $this->view->oStaffMemberMembership, false, $sMessage );
				if ( $sMessage === null )
					$oDbWriter->write();

				$this->view->staffmembershipsavemessage = "begeleider lidmaarschap toegevoegd";
				if ( $sMessage !== null )
					$this->view->staffmembershipsavemessage .= "<br>" . $sMessage;
			}
		}
	}

	protected function updateStaffMemberMembership()
	{
		$this->view->staffmembershiperrormessage = $this->checkStaffMemberMembershipInput( true );

		if ( $this->view->staffmembershiperrormessage === null )
		{
			$oDbWriter = Voetbal_Team_Membership_StaffMember_Factory::createDbWriter();
			$this->view->oStaffMemberMembership->addObserver( $oDbWriter );

			$bRemoveAvatar = ( $this->getParam( "removestaffmembershipphoto" ) === "on" );
			$this->saveImage( "staffmembershipphoto", $this->view->oStaffMemberMembership, $bRemoveAvatar, $sMessage );
			if ( $sMessage !== null )
				$this->view->staffmembershiperrormessage = $sMessage;

			if ( $this->view->staffmembershiperrormessage === null )
			{
				$this->view->oStaffMemberMembership->putClient( $this->view->staffmembershippersonid );
				$this->view->oStaffMemberMembership->putStartDateTime( $this->view->staffmembershipstartdatetime );
				$this->view->oStaffMemberMembership->putEndDateTime($this->view->staffmembershipenddatetime );
				$this->view->oStaffMemberMembership->putFunctionX( $this->view->staffmembershipfunctionx );
				$this->view->oStaffMemberMembership->putImportance( $this->view->staffmembershipimportance );

				if ( $oDbWriter->write() === true )
				{
					$this->view->staffmembershipsavemessage = "begeleider lidmaatschap aangepast.";
				}
			}
		}
	}

	protected function checkStaffMemberMembershipInput( $bUpdate = false )
	{
		$this->view->staffmembershippersonid = (int) $this->getParam( "staffmembershippersonid" );
		$this->view->staffmembershipfunctionx = $this->getParam( "staffmembershipfunctionx" );
		$this->view->staffmembershipimportance = (int) $this->getParam( "staffmembershipimportance" );
		$this->view->staffmembershipstartdatetime = $this->_helper->getDateTime( 'staffmembershipstartdatetime', false );
		$this->view->staffmembershipenddatetime = $this->_helper->getDateTime( 'staffmembershipenddatetime', false );

		if ( $this->view->staffmembershippersonid === 0 )
			return "er is geen persoon geselecteerd";

		if ( strlen( $this->view->staffmembershipfunctionx ) === 0 )
			return "er is geen functie geselecteerd";

		if ( $this->view->staffmembershipimportance === 0 )
			return "er is geen belangrijkheid geselecteerd";

		if ( $this->view->staffmembershipstartdatetime === null )
			return "er is geen startdatum geselecteerd.";

		if ( $this->view->staffmembershipenddatetime !== null
			and $this->view->staffmembershipenddatetime < $this->view->staffmembershipstartdatetime
		)
			return "de einddatum ligt voor de startdatum";

		// membership overlap
		$oTimeSlotChecked = Voetbal_Team_Membership_StaffMember_Factory::createObject();
		$oTimeSlotChecked->putStartDateTime( $this->view->staffmembershipstartdatetime );
		$oTimeSlotChecked->putEndDateTime( $this->view->staffmembershipenddatetime );

		$oOptions = Construction_Factory::createFiltersForTimeSlots( "Voetbal_Team_Membership_StaffMember", $oTimeSlotChecked, Agenda_TimeSlot::EXCLUDE_NONE, true );
		$oOptions->addFilter( "Voetbal_Team_Membership_StaffMember::Provider", "EqualTo", $this->view->oTeam );
		$oOptions->addFilter( "Voetbal_Team_Membership_StaffMember::Client", "EqualTo", $this->view->staffmembershippersonid );
		$oOptions->addFilter( "Voetbal_Team_Membership_StaffMember::FunctionX", "EqualTo", $this->view->staffmembershipfunctionx );
		if ( $bUpdate === true )
			$oOptions->addFilter( "Voetbal_Team_Membership_StaffMember::Id", "NotEqualTo", $this->view->oStaffMemberMembership );
		$oStaffMemberMemberships = Voetbal_Team_Membership_StaffMember_Factory::createObjectsFromDatabase( $oOptions );
		if ( $oStaffMemberMemberships->count() > 0 )
			return "deze begeleider is al actief bij dit team in deze periode voor deze funtie";

		return null;
	}

	protected function deleteStaffMemberMembership()
	{
		// kan alleen als er geen wedstrijden en goals op deze staffmemberships aanwezig zijn
		$oDbWriter = Voetbal_Team_Membership_StaffMember_Factory::createDbWriter();
		$this->view->oStaffMemberMemberships->addObserver( $oDbWriter );

		$this->view->oStaffMemberMemberships->remove( $this->view->oStaffMemberMembership );

		try
		{
			if ( $oDbWriter->write() === true )
			{
				$this->view->staffmembershiplistsavemessage = "begeleider lidmaatschap verwijderd";
			}
		}
		catch( Exception $e )
		{
			$this->view->staffmembershiplistsavemessage = "begeleider kon niet verwijderd worden. Speler heeft al wedstrijden gespeeld.<br>Je kunt de speler inactief maken om de speler te verbergen";
		}
		$this->view->oStaffMemberMembership = null;
	}

	// 1 foto moet jpg zijn
	// 2 minimale breedte 480, minimale hoogte 640,
	// 3 minimale verhouding 3-4
	// 4 maximale verhouding 3-5
	protected function saveImage( $sInputId, $oMembership, $bRemoveAvatar, &$sMessage )
	{
		if ( $bRemoveAvatar === true )
		{
			$oMembership->putPicture( null );
			return;
		}

		$arrOptions = array(
			"min_image_width" => 480,
			"min_image_height" => 640,
			"min_aspect_ratio" => 3/5,
			"max_aspect_ratio" => 3/4
		);
		$vtPicture = $this->_helper->GetImage( $sInputId, $arrOptions );

		if ( is_bool( $vtPicture ) and $vtPicture === false )
		{
			return;
		}
		elseif ( $vtPicture[0] === false )
		{
			$sMessage = $vtPicture[1];
		}
		else
		{
			$oMembership->putPicture( $vtPicture[1] );
		}
	}
}

?>

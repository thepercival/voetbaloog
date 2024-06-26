<?php

class Voetbal_PersoonController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$this->getResponse()->insert("extrajsincludes", $this->_helper->GetDateIncludes("js") );
		$this->getResponse()->insert("extracssincludes", $this->_helper->GetDateIncludes("css", false) );

		$this->view->oPersons = Voetbal_Person_Factory::createObjectsFromDatabase();

		$sBtnSave = $this->getParam('btnsave');
		if ( strlen ( $sBtnSave ) > 0 ) {
			if ( $sBtnSave === "add" )
				$this->add();
			else if ( $sBtnSave === "edit" )
				$this->update();
			else if ( $sBtnSave === "remove" )
				$this->remove();
		}
		else if ( strlen( $this->getParam('autofill') ) > 0 )
			$this->autofill();
		else if ( strlen( $this->getParam('editid') ) > 0 ) {
			$this->view->editid = (int)$this->getParam('editid');
			$this->view->teamid = (int) $this->getParam('newteamid');
		}
		else if ( strlen( $this->getParam('btnvalidate') ) > 0 )
			$this->validate();
		else if ( strlen( $this->getParam('btnendplayermembership') ) > 0 )
			$this->endMembership();
	}

	/**
	 * automitically get played details from external source and show popup
	 */
	protected function autofill()
	{
		$nExternId = (int) $this->getParam('externid');
		if ( $nExternId === 0 )
			$this->view->errormessage = "externid is niet meegegeven voor het automatisch invullen";
		$nTeamId = (int) $this->getParam('teamid');
		if ( $nTeamId === 0 )
			$this->view->errormessage = "teamid is niet meegegeven voor het automatisch invullen";

		if ( $this->view->errormessage !== null )
			return;

		$this->view->autofill = true;
		$this->view->externid = $nExternId;
		$this->view->teamid = $nTeamId;
	}

	protected function add()
	{
		$sFirstName = $this->getParam('firstname');
		if ( strlen ( $sFirstName ) === 0 )
			$this->view->errormessage = "voornaam is niet ingevuld";

		$sLastName = $this->getParam('lastname');
		if ( strlen ( $sLastName ) === 0 )
			$this->view->errormessage = "achternaam is niet ingevuld";

		$oDateOfBirth = $this->_helper->getDateTime( 'dateofbirth', false );
		if ( $oDateOfBirth === null )
			$this->view->errormessage = "geboortedatum is niet ingevuld";

		$nTeamId = (int) $this->getRequest()->getParam( "playermembershipteamid" );

		if ( $this->view->errormessage !== null )
			return;

		$commandBus = $this->_helper->getCommandBus();
		$addUpdatePersonCommand = new Voetbal_Command_AddUpdatePerson( null );
		$addUpdatePersonCommand->putFirstName( $sFirstName );
		$addUpdatePersonCommand->putNameInsertions( $this->getParam('insertion') );
		$addUpdatePersonCommand->putLastName( $sLastName );
		$addUpdatePersonCommand->putDateOfBirth( $oDateOfBirth );
		if ( $nTeamId > 0 ) {
			$vtRetVal = $this->convertInputToStructure( null, true );
			if ( is_string( $vtRetVal ) ) {
				$this->view->errormessage = $vtRetVal;
				return;
			}
			$addUpdatePersonCommand->putPlayerPeriods(array( $vtRetVal ));
		}
		$sExternId = strlen ( $this->getParam('externid') ) > 0 ? $this->getParam('externid') : null;
		$addUpdatePersonCommand->putExternId( $sExternId );
		$addUpdatePersonCommand->putBus( $commandBus );

		try
		{
			$oPerson = $commandBus->handle( $addUpdatePersonCommand );
			$this->view->oPersons->add( $oPerson );
			$this->view->successmessage = "de persoon is toegevoegd";
			if ( $nTeamId > 0 ) {
				$this->view->playermembershippersonid = $oPerson->getId();
			}
		}
		catch ( Exception $e)
		{
			$this->view->oPersons->remove( $oPerson );
			$this->view->errormessage = "de persoon is niet toegevoegd : ".$e->getMessage();
		}
	}

	public function convertInputToStructure( $oPlayerMembership, $bAddingPerson = false )
	{
		$nProviderId = (int)$this->getRequest()->getParam("playermembershipteamid");
		$nClientId = (int)$this->getRequest()->getParam("playermembershippersonid");
		$oStartDateTime = $this->_helper->GetDateTime('playermembershipstartdatetime', false);
		$oEndDateTime = $this->_helper->GetDateTime('playermembershipenddatetime', false);
		$nBackNumber = null;
		{
			$sBackNumber = $this->getRequest()->getParam("playermembershipbacknumber");
			if (strlen($sBackNumber) > 0)
				$nBackNumber = (int)$sBackNumber;
		}
		$nLine = (int)$this->getRequest()->getParam("playermembershipline");

		if ($nProviderId === 0)
			return "er is geen team geselecteerd";

		if ($nBackNumber === 0)
			return "er is geen rugnummer geselecteerd";

		if ($oStartDateTime === null)
			return "er is geen startdatum geselecteerd";

		if ($oEndDateTime !== null and $oEndDateTime < $oStartDateTime
		)
			return "de einddatum ligt voor de startdatum";

		if (!$bAddingPerson) {
			// membership overlap
			$oTimeSlotChecked = Voetbal_Team_Membership_Player_Factory::createObject();
			$oTimeSlotChecked->putStartDateTime($oStartDateTime);
			$oTimeSlotChecked->putEndDateTime($oEndDateTime);

			$oOptions = Construction_Factory::createFiltersForTimeSlots("Voetbal_Team_Membership_Player", $oTimeSlotChecked, Agenda_TimeSlot::EXCLUDE_NONE, true);
			$oOptions->addFilter("Voetbal_Team_Membership_Player::Provider", "EqualTo", $nProviderId);
			$oOptions->addFilter("Voetbal_Team_Membership_Player::Client", "EqualTo", $nClientId);
			if ($oPlayerMembership !== null)
				$oOptions->addFilter("Voetbal_Team_Membership_Player::Id", "NotEqualTo", $oPlayerMembership);
			$oPlayerMemberships = Voetbal_Team_Membership_Player_Factory::createObjectsFromDatabase($oOptions);
			if ($oPlayerMemberships->count() > 0)
				return "deze speler is al actief bij dit team in deze periode";

			// backnumber overlap
			$oOptions = Construction_Factory::createFiltersForTimeSlots("Voetbal_Team_Membership_Player", $oTimeSlotChecked, Agenda_TimeSlot::EXCLUDE_NONE, true);
			$oOptions->addFilter("Voetbal_Team_Membership_Player::Provider", "EqualTo", $nProviderId);
			$oOptions->addFilter("Voetbal_Team_Membership_Player::BackNumber", "EqualTo", $nBackNumber);
			if ($oPlayerMembership !== null)
				$oOptions->addFilter("Voetbal_Team_Membership_Player::Id", "NotEqualTo", $oPlayerMembership);
			$oPlayerMemberships = Voetbal_Team_Membership_Player_Factory::createObjectsFromDatabase($oOptions);
			if ($oPlayerMemberships->count() > 0) {
				$oPlayerMembership = $oPlayerMemberships->first();
				return "speler " . $oPlayerMembership->getClient()->getFullName() . " heeft rugnummer " . $oPlayerMembership->getBackNumber() . " al tijdens deze periode";
			}
		}

		$oPlayerPeriod = new stdClass();
		$oPerson = null;
		if ($nClientId > 0) {
			$oPerson = new stdClass();
			$oPerson->id = $nClientId;
		}
		$oPlayerPeriod->person = $oPerson;
		$oPlayerPeriod->team = new StdClass();
		$oPlayerPeriod->team->id = $nProviderId;
		$oPlayerPeriod->timeslot = new stdClass();
		$oPlayerPeriod->timeslot->startdatetime = $oStartDateTime;
		$oPlayerPeriod->timeslot->enddatetime = $oEndDateTime;
		$oPlayerPeriod->backnumber = $nBackNumber;
		$oPlayerPeriod->line = $nLine;

		return $oPlayerPeriod;
	}

	protected function update()
	{
		$sFirstName = $this->getParam('firstname');
		if ( strlen ( $sFirstName ) === 0 )
			$this->view->errormessage = "voornaam is niet ingevuld";

		$sLastName = $this->getParam('lastname');
		if ( strlen ( $sLastName ) === 0 )
			$this->view->errormessage = "achternaam is niet ingevuld";

		$oDateOfBirth = $this->_helper->getDateTime( 'dateofbirth', false );
		if ( $oDateOfBirth === null )
			$this->view->errormessage = "geboortedatum is niet ingevuld";

		$oPerson = null;
		{
			$nId = (int) $this->getParam("personid");
			if ( $nId > 0 )
				$oPerson = $this->view->oPersons[ $nId ];
		}
		if ( $oPerson === null )
			$this->view->errormessage = "persoon kon niet gevonden worden";

		$nTeamId = (int) $this->getRequest()->getParam( "playermembershipteamid" );

		if ( $this->view->errormessage !== null )
			return;

		$commandBus = $this->_helper->getCommandBus();
		$addUpdatePersonCommand = new Voetbal_Command_AddUpdatePerson( $oPerson );
		$addUpdatePersonCommand->putFirstName( $sFirstName );
		$addUpdatePersonCommand->putNameInsertions( $this->getParam('insertion') );
		$addUpdatePersonCommand->putLastName( $sLastName );
		$addUpdatePersonCommand->putDateOfBirth( $oDateOfBirth );
		if ( $nTeamId > 0 ) {
			$vtRetVal = $this->convertInputToStructure( null, false );
			if ( is_string( $vtRetVal ) ) {
				$this->view->errormessage = $vtRetVal;
				return;
			}
			$addUpdatePersonCommand->putPlayerPeriods(array( $vtRetVal ));
		}
		$sExternId = strlen ( $this->getParam('externid') ) > 0 ? $this->getParam('externid') : null;
		$addUpdatePersonCommand->putExternId( $sExternId );
		$addUpdatePersonCommand->putBus( $commandBus );

		try
		{
			$oPerson = $commandBus->handle( $addUpdatePersonCommand );
			$this->view->successmessage = "persoon ".$oPerson->getFullName( Voetbal_Person::CALLTYPE_FULLNAME_FIRSTNAMELETTER )." opgeslagen";
		}
		catch ( Exception $e)
		{
			$this->view->errormessage = "persoon niet opgeslagen : ".$e->getMessage();
		}
	}

	protected function endMembership()
	{
		$oPlayerMembership = Voetbal_Team_Membership_Player_Factory::createObjectFromDatabase( (int) $this->getParam("endplayermembershipid") );
		$oEndDateTime = $this->_helper->GetDateTime('endplayermembershipenddatetime', false);

		$commandBus = $this->_helper->getCommandBus();
		$addUpdatePlayerPeriodCommand = new Voetbal_Command_AddUpdatePlayerPeriod(
			$oPlayerMembership,
			$oPlayerMembership->getClient(),
			$oPlayerMembership->getProvider(),
			Agenda_Factory::createTimeSlotNew( $oPlayerMembership->getStartDateTime(), $oEndDateTime )
		);
		$addUpdatePlayerPeriodCommand->putBus( $commandBus );

		try
		{
			$commandBus->handle( $addUpdatePlayerPeriodCommand );
			$this->view->successmessage = "huidige spelersperiode voor ".$oPlayerMembership->getClient()->getFullName( Voetbal_Person::CALLTYPE_FULLNAME_FIRSTNAMELETTER )." beeindigd";
		}
		catch ( Exception $e)
		{
			$this->view->errormessage = "huidige spelersperiode kon niet worden beeindigd : ".$e->getMessage();
		}

	}

	protected function remove()
	{
		$oDbWriter = Voetbal_Person_Factory::createDbWriter();
		$this->view->oPersons->addObserver( $oDbWriter );

		$oPerson = $this->view->oPersons[ (int) $this->getParam("personid") ];
		if ( $oPerson === null ) {
			$this->view->errormessage = "kan persoon niet vinden";
			return;
		}

		//if ( $oPerson->getStaffMemberMemberships()->count() > 0 )
			//$this->view->message = "persoon kan niet verwijderd worden, er zijn nog minimaal ".$oPerson->getStaffMemberPeriods()->count()." periode(n) als staflid aanwezig";

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_Team_Membership_Player::Client", "EqualTo", $oPerson );
		$nNrOfGames = Voetbal_Game_Participation_Factory::getNrOfObjectsFromDatabase( $oOptions );
		if ( $nNrOfGames > 0 ) {
			$this->view->errormessage = "persoon kan niet verwijderd worden, hij neemt nog deel aan wedstrijden";
			return;
		}

		$this->view->oPersons->remove( $oPerson );

		try
		{
			$oDbWriter->write();
			$this->view->successmessage = "persoon ".$oPerson->getFullName()." verwijderd";
		}
		catch ( Exception $e)
		{
			$this->view->oPersons->add( $oPerson );
			$this->view->errormessage = "persoon kan niet verwijderd worden : " . $e->getMessage();
		}
	}

	protected function validate()
	{
		$oPerson = null;
		{
			$nId = (int) $this->getParam("personid");
			if ( $nId > 0 )
				$oPerson = $this->view->oPersons[ $nId ];
		}
		if ( $oPerson === null ) {
			$this->view->errormessage = "persoon kon niet gevonden worden";
			return;
		}

		$handlerMiddleware = Voetbal_Command_Main_Factory::getMiddleWare();
		$transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );
		$commandBus = new \League\Tactician\CommandBus([$transactionMiddleware,$handlerMiddleware]);

		try
		{
			$validatePersonCommand = new Voetbal_Command_ValidatePerson( $oPerson );
			$vtRetVal = $commandBus->handle( $validatePersonCommand );

			if ( $vtRetVal !== true )
				$this->view->errormessage = $vtRetVal;
			else {
				$this->view->successmessage = "de persoon ".$oPerson->getFullName( Voetbal_Person::CALLTYPE_FULLNAME_FIRSTNAMELETTER )." is gevalideerd";
			}
		}
		catch( Exception $e )
		{
			$this->view->errormessage = "onbekende fout: ".$e->getMessage();
		}
	}

	public function ajaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		if ( $this->getParam('method') === "editform" )
		{
			$nId = (int) $this->getParam('id');
			if ( $nId > 0 ) {
				$this->view->oPerson = Voetbal_Person_Factory::createObjectFromDatabase( $nId );
				$this->view->newteamid = (int) $this->getParam('teamid');
			}

			echo $this->render( "edit" );
		}
		else{
			echo "no input-param 'method'";
		}
	}
}

?>

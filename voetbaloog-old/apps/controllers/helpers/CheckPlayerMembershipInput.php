<?php

/**
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Apps_Helper_CheckPlayerMembershipInput extends Zend_Controller_Action_Helper_Abstract
{
	public function direct( $bUpdate = false )
	{
		$oController = $this->getActionController();
		if ( $oController->view->playermembershipteamid === null )
			$oController->view->playermembershipteamid = (int) $this->getRequest()->getParam( "playermembershipteamid" );
		if ( $oController->view->playermembershippersonid === null )
		$oController->view->playermembershippersonid = (int) $this->getRequest()->getParam( "playermembershippersonid" );
		$oController->view->playermembershipbacknumber = (int) $this->getRequest()->getParam( "playermembershipbacknumber" );
		$oController->view->playermembershipline = (int) $this->getRequest()->getParam( "playermembershipline" );

		$oController->view->playermembershipstartdatetime = $oController->getHelper("getDateTime")->direct('playermembershipstartdatetime', false);
		$oController->view->playermembershipenddatetime = $oController->getHelper("getDateTime")->direct('playermembershipenddatetime', false);

		if ( $oController->view->playermembershipteamid === 0 )
			return "er is geen team geselecteerd";

		if ( $oController->view->playermembershippersonid === 0 )
			return "er is geen persoon geselecteerd";

		if ( $oController->view->playermembershipbacknumber === 0 )
			return "er is geen rugnummer geselecteerd";

		if ( $oController->view->playermembershipstartdatetime === null )
			return "er is geen startdatum geselecteerd";

		if ( $oController->view->playermembershipenddatetime !== null
				and $oController->view->playermembershipenddatetime < $oController->view->playermembershipstartdatetime
		)
			return "de einddatum ligt voor de startdatum";

		// membership overlap
		$oTimeSlotChecked = Voetbal_Team_Membership_Player_Factory::createObject();
		$oTimeSlotChecked->putStartDateTime( $oController->view->playermembershipstartdatetime );
		$oTimeSlotChecked->putEndDateTime( $oController->view->playermembershipenddatetime );

		$oOptions = Construction_Factory::createFiltersForTimeSlots( "Voetbal_Team_Membership_Player", $oTimeSlotChecked, Agenda_TimeSlot::EXCLUDE_NONE, true );
		$oOptions->addFilter( "Voetbal_Team_Membership_Player::Provider", "EqualTo", $oController->view->playermembershipteamid );
		$oOptions->addFilter( "Voetbal_Team_Membership_Player::Client", "EqualTo", $oController->view->playermembershippersonid );
		if ( $bUpdate === true )
			$oOptions->addFilter( "Voetbal_Team_Membership_Player::Id", "NotEqualTo", $oController->view->oPlayerMembership );
		$oPlayerMemberships = Voetbal_Team_Membership_Player_Factory::createObjectsFromDatabase( $oOptions );
		if ( $oPlayerMemberships->count() > 0 )
			return "deze speler is al actief bij dit team in deze periode";

		// backnumber overlap
		$oOptions = Construction_Factory::createFiltersForTimeSlots( "Voetbal_Team_Membership_Player", $oTimeSlotChecked, Agenda_TimeSlot::EXCLUDE_NONE, true );
		$oOptions->addFilter( "Voetbal_Team_Membership_Player::Provider", "EqualTo", $oController->view->playermembershipteamid );
		$oOptions->addFilter( "Voetbal_Team_Membership_Player::BackNumber", "EqualTo", $oController->view->playermembershipbacknumber );
		if ( $bUpdate === true )
			$oOptions->addFilter( "Voetbal_Team_Membership_Player::Id", "NotEqualTo", $oController->view->oPlayerMembership );
		$oPlayerMemberships = Voetbal_Team_Membership_Player_Factory::createObjectsFromDatabase( $oOptions );
		if ( $oPlayerMemberships->count() > 0 )
		{
			$oPlayerMembership = $oPlayerMemberships->first();
			return "speler ".$oPlayerMembership->getClient()->getFullName()." heeft rugnummer ".$oPlayerMembership->getBackNumber()." al tijdens deze periode";
		}
		return null;
	}
}
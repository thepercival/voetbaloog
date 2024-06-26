<?php

class Apps_Helper_GetRolesForResource extends Zend_Controller_Action_Helper_Abstract
{
	public function direct()
	{
		$oActionController = $this->getActionController();

		$oUser = $oActionController->view->oUser;
		if ( $oUser === null )
			return RAD_Auth_Role_Factory::createObjects();

		$oRequest = $oActionController->getRequest();

		$sAction = $oRequest->getControllerName() . "/" . $oRequest->getActionName() . "/";

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "RAD_Auth_Action::Name", "EqualTo", $sAction );
		$oOptions->addFilter( "RAD_Auth_Action::Module", "EqualTo", APPLICATION_NAME );
		$oAction = RAD_Auth_Action_Factory::createObjectFromDatabase( $oOptions );

		if ( $oAction === null )
			return RAD_Auth_Role_Factory::createObjects();

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "RAD_Auth_User::Id", "EqualTo", $oUser );
		return RAD_Auth_Role_Factory::createObjectsFromDatabaseExt( $oAction, $oOptions );
	}
}
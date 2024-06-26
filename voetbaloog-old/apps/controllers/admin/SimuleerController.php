<?php

class Admin_SimuleerController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addOrder( "RAD_Auth_User::Id", false );
		$this->view->oUsers = RAD_Auth_User_Factory::createObjectsFromDatabase( $oOptions );

		$this->view->message = $this->handleAction();
	}

	protected function handleAction()
	{
		$vtRetVal = null;
		if ( strlen( $this->getRequest()->getParam("btnswitchuser") ) > 0 )
		{
			$nUserId = (int) $this->getRequest()->getParam("newuserid");
			if ( $nUserId > 0 )
				$vtRetVal = $this->switchUser( $nUserId );
			else
				$vtRetVal = "<div class=\"alert alert-danger\">gebruiker is niet gezet</div>";
		}

		return $vtRetVal;
	}

	protected function switchUser( $nUserId )
	{
		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );

		$oSession->userid = $nUserId;
		$oSession->oRootMenuItem = null;
		$oSession->simulation = true;
		$this->refresh();
		$r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		$r->gotoUrl( "index/index/" )->redirectAndExit();
	}

	protected function refresh()
	{
		ZendExt_Cache::getCache( null, APPLICATION_PATH  . "/cache" )->remove( 'acl' );
		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
		if ( $oSession !== null )
			$oSession->oRootMenuItem = null;
	}
}
<?php

class PoolUserController extends Zend_Controller_Action
{
	public function ajaxAction()
	{
		$response = $this->getResponse()->clearBody();
		$this->_helper->viewRenderer->setNoRender();

		$sAction = $this->getParam("ajaxaction");

		$sRetVal = null;

		if ( $sAction === "getfrompool" )
			$sRetVal = $this->ajaxGetFromPool();

		echo $sRetVal;

		die();
	}

	protected function ajaxGetFromPool()
	{
		// check if user has rights
		$nPoolId = (int) $this->getParam("poolid");
		if ( $nPoolId === 0 )
			return "{ error : 'er is geen pool opgegeven.'}";

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_Pool_User::User", "NotEqualTo", $this->view->oUser );
		$oOptions->addFilter( "VoetbalOog_Pool_User::Pool", "EqualTo", $nPoolId );
		$oPoolUsers = VoetbalOog_Pool_User_Factory::createObjectsFromDatabase( $oOptions );

		return VoetbalOog_Pool_User_Factory::convertObjectsToJSON( $oPoolUsers );
	}
}
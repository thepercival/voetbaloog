<?php

class VoetbalOog_ZendExt_Plugin_Image extends Zend_Controller_Plugin_Abstract
{
	public function dispatchLoopStartup( Zend_Controller_Request_Abstract $request )
	{
		if ( $request->getControllerName() === "image" and $request->getActionName() === "generatecaptcha" )
		{
			$nWidth = (int) $request->getParam("width");

			$vtData = null;

			$nUserId = (int) $request->getParam("userid");
			if ( $nUserId > 0 )
				$vtData = $this->getUserImage( $nUserId );

			$nPlayerMembershipId = (int) $request->getParam("playermembershipid");
			if ( $nPlayerMembershipId > 0 )
				$vtData = Voetbal_Team_Membership_Player_Factory::getPicture( $nPlayerMembershipId );

			if ( $vtData === null or strlen( $vtData ) === 0 )
				$vtData = file_get_contents( Zend_Registry::get("baseurl")."public/images/nobody.jpg" );

			if ( $nWidth > 0 )
				$vtData = RAD_Image_Factory::resize( $vtData, $nWidth );

			header('Content-Type: image/jpeg', true);
			echo $vtData;

			die();
		}
	}

	private function getUserImage( $nId )
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addReadProperties( "VoetbalOog_User::Picture" );
		$oOptions->addFilter( "VoetbalOog_User::Id", "EqualTo", $nId );
		$oUser = VoetbalOog_User_Factory::createObjectFromDatabase( $oOptions );
		if ( $oUser !== null )
			return $oUser->getPicture();
		return null;
	}
}
?>
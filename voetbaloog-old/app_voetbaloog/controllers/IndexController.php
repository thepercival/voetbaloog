<?php

class IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{
		if ( $this->getParam("cordova") === "surething" ) {
			$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
			$oSession->cordova = true;
		}

		$sMessage = $this->getParam('mainerrormessage');
		if ( strlen( $sMessage ) > 0 )
			$this->view->mainerrormessage = urldecode( $sMessage );
        $sMessageSuccess = $this->getParam('mainsuccessmessage');
        if ( strlen( $sMessageSuccess ) > 0 )
            $this->view->mainsuccessmessage = urldecode( $sMessageSuccess );

		if ( $this->view->oUser !== null)
		{
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addLimit( 20 );
			$oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
			$oOptions->addOrder( "VoetbalOog_Pool::Name", false );
			$oOptions->addFilter( "VoetbalOog_Pool_User::User", "EqualTo", $this->view->oUser );
			$this->view->oUserPools = VoetbalOog_Pool_Factory::createObjectsFromDatabaseExt( $this->view->oUser, $oOptions );

			$bStarted = false; $bEnded = false;
			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "Voetbal_CompetitionSeason::Public", "EqualTo", true );
			$oOptions->addLimit( 1 );
			$oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjectsFromDatabaseCustom($bStarted, $bEnded, $oOptions);
			if ($oCompetitionSeasons->count() > 0 )
				$this->view->oAvailableCompSeason = $oCompetitionSeasons->first();
		}

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addLimit( 40 );
		$oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
		$oOptions->addOrder( "VoetbalOog_Pool::Name", false );
		$oOptions->addFilter( "Voetbal_CompetitionSeason::Public", "EqualTo", true );
		$this->view->oAllPools = VoetbalOog_Pool_Factory::createObjectsFromDatabase( $oOptions );

		if( $this->view->oUserPools !== null )
			$this->view->oAllPools->removeCollection( $this->view->oUserPools );
	}
}

?>

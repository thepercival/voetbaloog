<?php

/**
 * @author coen
 */
class VoorspellingenbijwerkenController extends Zend_Controller_Action
{
	public function indexAction() {

		$this->getResponse()->insert("extrajsincludes", $this->_helper->GetDateIncludes("js", false ) );
		$this->getResponse()->insert("extracssincludes", $this->_helper->GetDateIncludes("css", false ) );

		$this->view->successmessage = urldecode( $this->getParam("successmessage") );
		$this->view->errormessage = urldecode( $this->getParam("errormessage") );

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
		$bStarted                        = true;
		$bEnded                          = null;
		$this->view->oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjectsFromDatabaseCustom( $bStarted, $bEnded, $oOptions );
	}

	public function verwerkenAction()
	{
		$sRedirectUrl = $this->view->urlcontroller . "/index/";

		if ( $this->getRequest()->isPost() ) {
			try
			{
				$oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( (int) $this->getParam( 'competitionseasonid' ) );
				if ( $oCompetitionSeason === null ) {
					throw new Exception( "het competitieseizoen kon niet gevonden worden", E_ERROR );
				}
				$oValidateDateTime = $this->_helper->GetDateTime('gamedatetime');
				if ( $oValidateDateTime === null ) {
					throw new Exception( "er moet een validatiedatum worden opgegeven", E_ERROR );
				}

				$handlerMiddleware     = VoetbalOog_Command_Main_Factory::getMiddleWare();
				$transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get( "db" ) );
				$commandBus            = new \League\Tactician\CommandBus( [ $transactionMiddleware, $handlerMiddleware ] );

				// command update RoundbetConfigs
				$updateBetsCommand = new VoetbalOog_Command_UpdateBets( $oCompetitionSeason );
				if ( $oValidateDateTime !== null )
					$updateBetsCommand->putValidateDateTime( $oValidateDateTime );
				$updateBetsCommand->putBus( $commandBus );
				$commandBus->handle( $updateBetsCommand );

				$oCache = ZendExt_Cache::getDefaultCache();
				$oCache->clean( Zend_Cache::CLEANING_MODE_MATCHING_TAG,	array( 'competitionseason'.$oCompetitionSeason->getId() ) );

				$this->redirect( $sRedirectUrl . "successmessage/" . urlencode( "de voorspellingen zijn bijgewerkt" ) . "/" );

			} catch ( Exception $e ) {
				$this->redirect( $sRedirectUrl . "errormessage/" . urlencode( "de voorspellingen konden niet worden bijgewerkt : " . $e->getMessage() ) . "/" );
			}
		}
		$this->redirect( $sRedirectUrl );
	}
}

?>

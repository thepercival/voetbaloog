<?php

/**
 * @author coen
 */
class VoorspelinstellingenController extends Zend_Controller_Action
{
	public function competitieseizoenAction() {
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
		$bStarted                        = false;
		$bEnded                          = null;
		$this->view->oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjectsFromDatabaseCustom( $bStarted, $bEnded, $oOptions );

		$this->view->successmessage = urldecode( $this->getParam("successmessage") );
		$this->view->errormessage = urldecode( $this->getParam("errormessage") );

		$this->getResponse()->insert("extrajsincludes", $this->_helper->AddIncludes() );
	}

	public function ajaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$arrData = array(); $nCode = 0; $sMessage = null;
		try
		{
			if ( $this->getParam('method') === "getdata" )
			{
				$oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( (int) $this->getParam('csid') );
                $oPool = null;
                {
                    $nPoolId = (int) $this->getParam('poolid');
                    if ( $nPoolId > 0 ) {
                        $oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( $nPoolId );
                    }
                }
                $oRBCOwner = $oPool !== null ? $oPool : $oCompetitionSeason;
				$oRoundBetConfigs = VoetbalOog_Round_BetConfig_Factory::createObjectsFromDatabaseExt( $oRBCOwner );

				$arrData = array(
					"competitionseason" => json_decode( Voetbal_CompetitionSeason_Factory::convertObjectToJSON( $oCompetitionSeason, Voetbal_JSON::$nCompetitionSeason_Rounds ) ),
					"roundbetconfigs" => json_decode( VoetbalOog_Round_BetConfig_Factory::convertObjectsToJSON( $oRoundBetConfigs ) )
				);
			}
			else {
				throw new Exception( "input-method not recognized", E_ERROR );
			}
		}
		catch( Exception $e )
		{
			$sMessage = $e->getMessage();
			$nCode = -1;
		}
		$this->_helper->jsonOutput( $arrData, $nCode, $sMessage );
	}

	public function verwerkenAction()
	{
        $sRedirectUrl = Zend_Registry::get("baseurl");

		if ( $this->getRequest()->isPost() ) {
			try
			{
				$oPool = null;
				{
					$nPoolId = (int) $this->getParam( 'poolid' );
					if ( $nPoolId > 0 ) {
						$oPool = VoetbalOog_Pool_Factory::createObjectFromDatabase( $nPoolId );
					}
				}
                $sRedirectUrl .= $oPool === null ? "voorspelinstellingen/competitieseizoen/" : "poolbeheer/voorspellingen/poolid/".$oPool->getId()."/";

				$oCompetitionSeason = null;
				if ( $oPool === null ) {
					$nCSId = (int) $this->getParam( 'competitionseasonid' );
					if ( $nCSId > 0 ) {
						$oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( $nCSId );
					}
				}
				else {
                    $oCompetitionSeason = $oPool->getCompetitionSeason();
				}

				if ( $oCompetitionSeason === null ) {
					throw new Exception( "het competitieseizoen kon niet gevonden worden", E_ERROR );
				}

				$oRBCOwner = $oPool !== null ? $oPool : $oCompetitionSeason;
				$arrBetConfigs = $this->_helper->GetBetConfigs( $oCompetitionSeason );
                // var_dump( $arrBetConfigs );
				$handlerMiddleware     = VoetbalOog_Command_Main_Factory::getMiddleWare();
				$transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get( "db" ) );
				$commandBus            = new \League\Tactician\CommandBus( [ $transactionMiddleware, $handlerMiddleware ] );

				// command update RoundbetConfigs
				$updateRoundbetConfigsCommand = new VoetbalOog_Command_UpdateRoundBetConfigs( $oRBCOwner, $arrBetConfigs );
				$updateRoundbetConfigsCommand->putBus( $commandBus );
				$commandBus->handle( $updateRoundbetConfigsCommand );

				$this->redirect( $sRedirectUrl . "successmessage/" . urlencode( "de ". (  $oPool === null ? "standaard " : null ) ."voorspelinstellingen zijn opgeslagen" ) . "/" );
			} catch ( Exception $e ) {
				$this->redirect( $sRedirectUrl . "errormessage/" . urlencode( "de ". (  $oPool === null ? "standaard " : null ) ."voorspelinstellingen konden niet worden opgeslagen : " . $e->getMessage() ) . "/" );
			}
		}
		$this->redirect( $sRedirectUrl );
	}
}

?>

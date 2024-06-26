<?php

class CompetitionSeasonController extends Zend_Controller_Action
{
	public function pdfAction()
	{
		$this->_putObjects();

		$oPdf = VoetbalOog_Pdf_Factory::createCompetitionSeason( $this->view->oCompetitionSeason );

		$sPdfFileName = APPLICATION_NAME . " " . $this->view->oCompetitionSeason->getName() .".pdf";

		$this->_helper->pdf( $oPdf, $sPdfFileName, "inline" );
	}

	public function ajaxAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$arrData = array(); $nCode = 0; $sMessage = null;
		try
		{
			if ( $this->getParam('ajaxaction') === "getprevious" )
			{
				$nMaxNrOfCompetitionSeasons = (int) $this->getParam("maxamount");
				$oCompetition = null;
				if ( strlen( $this->getParam("competitionid") ) > 0 )
					$oCompetition = Voetbal_Competition_Factory::createObjectFromDatabase( (int) $this->getParam("competitionid") );
				$arrData = json_decode( $this->ajaxGetPrevious( $oCompetition, $nMaxNrOfCompetitionSeasons ), true );
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

	protected function ajaxGetPrevious( $oCompetition, $nMaxNrOfCompetitionSeasons )
	{
		$bStarted = null; $bEnded = true;
		$oOptions = Construction_Factory::createOptions();
		if ( $oCompetition !== null )
			$oOptions->addFilter( "Voetbal_CompetitionSeason::Competition", "EqualTo", $oCompetition );
		$oOptions->addFilter( "Voetbal_CompetitionSeason::Public", "EqualTo", true );
		$oOptions->addLimit( $nMaxNrOfCompetitionSeasons );
		$oOptions->addOrder( "Voetbal_Season::StartDateTime", true );
		$oPreviousCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjectsFromDatabaseCustom( $bStarted, $bEnded, $oOptions );

		$nDataFlag = Voetbal_JSON::$nCompetitionSeason_Rounds;
		$nDataFlag += Voetbal_JSON::$nRound_Poules;
		$nDataFlag += Voetbal_JSON::$nPoule_Games;
		$sData = Voetbal_CompetitionSeason_Factory::convertObjectsToJSON( $oPreviousCompetitionSeasons, $nDataFlag );
		// var_dump( $sData );
		return $sData;
	}
}

?>

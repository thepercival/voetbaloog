<?php
class Voetbal_ApiController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function competitionseasonAction()
    {
        $arrData = array(); $nCode = 0; $sMessage = null;
        try {
            if ($this->getParam('subaction') === "savestructure")
            {
                $arrCompetitionSeason = json_decode( file_get_contents('php://input'), true);
                if ( !is_array( $arrCompetitionSeason ) ) {
                    throw new Exception( "de structuur is niet gevuld", E_ERROR );
                }

                $oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( (int) $arrCompetitionSeason["id"] );

                $handlerMiddleware = Voetbal_Command_Main_Factory::getMiddleWare();
                $transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );
                $commandBus = new \League\Tactician\CommandBus([$transactionMiddleware,$handlerMiddleware]);

                $removeAddCSStructure = new Voetbal_Command_RemoveAddCSStructure( $oCompetitionSeason, $arrCompetitionSeason );
                $removeAddCSStructure->putBus( $commandBus );
                $commandBus->handle( $removeAddCSStructure );
            }
            else if ($this->getParam('subaction') === "saveteams")
            {
                $arrRound = json_decode( file_get_contents('php://input'), true);
                if ( !is_array( $arrRound ) ) {
                    throw new Exception( "er is geen invoer verstuurd", E_ERROR );
                }

                $oRound = Voetbal_Round_Factory::createObjectFromDatabase( (int) $arrRound["id"] );

                $handlerMiddleware = Voetbal_Command_Main_Factory::getMiddleWare();
                $transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );
                $commandBus = new \League\Tactician\CommandBus([$transactionMiddleware,$handlerMiddleware]);

                $sEditMode = Voetbal_Factory::getConfigValue( "csadmin", "teams", "inputtypeselect" ) ? "assign" : "update";
                $updateFirstRoundTeams = new Voetbal_Command_UpdateFirstRoundTeams( $oRound, $arrRound, $sEditMode );
                $commandBus->handle( $updateFirstRoundTeams );
            }
            else if ($this->getParam('subaction') === "savegames")
            {
                $arrCreateGamesSettings = json_decode( file_get_contents('php://input'), true);
                if ( !is_array( $arrCreateGamesSettings ) ) {
                    throw new Exception( "er is geen invoer verstuurd", E_ERROR );
                }

                $oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( (int) $arrCreateGamesSettings["csid"] );

                $handlerMiddleware = Voetbal_Command_Main_Factory::getMiddleWare();
                $transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );
                $commandBus = new \League\Tactician\CommandBus([$transactionMiddleware,$handlerMiddleware]);

                $removeAddCSGames = new Voetbal_Command_RemoveAddCSGames( $oCompetitionSeason );

                $oDateTime = new DateTime( $arrCreateGamesSettings["startdatetime"], new DateTimeZone('UTC') );
                $oDateTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));
                $oDateTime = Agenda_Factory::createDateTime( $oDateTime->format( Agenda_DateTime::STR_SQLDATETIME ) );
                $removeAddCSGames->putStartDateTime( $oDateTime );
                $commandBus->handle( $removeAddCSGames );
            }
            else if ($this->getParam('subaction') === "saveproperties")
            {
                $arrProperties = json_decode( file_get_contents('php://input'), true);
                if ( !is_array( $arrProperties ) ) {
                    throw new Exception( "er is geen invoer verstuurd", E_ERROR );
                }

                $oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase( (int) $arrProperties["csid"] );

                $handlerMiddleware = Voetbal_Command_Main_Factory::getMiddleWare();
                $transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );
                $commandBus = new \League\Tactician\CommandBus([$transactionMiddleware,$handlerMiddleware]);

                $updateCompetitionSeason = new Voetbal_Command_UpdateCompetitionSeason( $oCompetitionSeason );

                $updateCompetitionSeason->putPublic( $arrProperties["public"] );
                $commandBus->handle( $updateCompetitionSeason );
            }
            else
            {
                $oCompetitionSeason = Voetbal_CompetitionSeason_Factory::createObjectFromDatabase((int)$this->getParam("id"));
                $nDataflag = (int)$this->getParam("dataflag");

                if ($oCompetitionSeason === null)
                    throw new Exception("geen competitieseizoen voor id : " . $this->getParam("id"));
                $arrData = Voetbal_CompetitionSeason_Factory::convertObjectToJSON2($oCompetitionSeason, $nDataflag);
            }
        }
        catch( Exception $e )
        {
            $sMessage = $e->getMessage();
            $nCode = -1;
        }
        $this->_helper->jsonOutput( $arrData, $nCode, $sMessage );
    }

    public function roundAction()
    {
        $arrData = array(); $nCode = 0; $sMessage = null;
        try {
            if ($this->getParam('action') === "blabla") {
                // blabla code
            } else {
                $oRound = Voetbal_Round_Factory::createObjectFromDatabase( (int) $this->getParam("id") );
                if ( $oRound === null )
                    throw new Exception("geen ronde voor id : " . $this->getParam("id") );
                $nDataflag = (int)$this->getParam("dataflag");
                $arrData = Voetbal_Round_Factory::convertObjectToJSON2( $oRound, $nDataflag );
            }
        }
        catch( Exception $e )
        {
            $sMessage = $e->getMessage();
            $nCode = -1;
        }
        $this->_helper->jsonOutput( $arrData, $nCode, $sMessage );
    }

    public function teamsAction()
    {
        $arrData = array(); $nCode = 0; $sMessage = null;
        try {
            if ($this->getParam('action') === "blabla") {
                // blabla code
            } else {
                $oAssociation = null;
                $associationId = (int) $this->getParam("associationid");
                if( $associationId > 0 ) {
                    $oAssociation = Voetbal_Association_Factory::createObjectFromDatabase( $associationId );
                }

                $oOptions = Construction_Factory::createOptions();
                if( $oAssociation !== null ) {
                    $oOptions->addFilter("ROC_Team::Association", "EqualTo", $oAssociation);
                }
                $oTeams = Voetbal_Team_Factory::createObjectsFromDatabase($oOptions);
                $nDataflag = (int)$this->getParam("dataflag");
                $arrData = Voetbal_Team_Factory::convertObjectsToJSON2( $oTeams, $nDataflag );
            }
        }
        catch( Exception $e )
        {
            $sMessage = $e->getMessage();
            $nCode = -1;
        }
        $this->_helper->jsonOutput( $arrData, $nCode, $sMessage );
    }
}
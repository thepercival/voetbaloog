<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 14-12-15
 * Time: 15:42
 */

class Voetbal_ExternController extends Zend_Controller_Action
{
    private function getExternalLib()
    {
        $cfgApp = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini');
        $bHasImport = ( $cfgApp->get( "import" ) !== null );
        if ( $bHasImport === false )
            throw new Exception("impport module is niet geconfigureerd", E_ERROR);

        return Voetbal_Factory::getExternalLib( $cfgApp->get( "import" )->name );
    }


    public function apiAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $arrData = array(); $nCode = 0; $sMessage = null;
        try
        {
            // competition needs externid and needs to be filled in
            // competitionseason needs externid and needs to be filled in
            // season needs externid and needs to be filled in
            if ( $this->getParam('method') === "getteams" ) // for csexternid( always one round and one poule )
            {
                $sCSId = $this->getParam("csid"); // s1147
                $sCompetitionId = $this->getParam("competitionid"); // "eredivisie"
                $sSeasonId = $this->getParam("seasonid"); // "2015-2016"

                $oExternalLib = $this->getExternalLib();

                $arrData = $oExternalLib->getTeams( $sCSId, $sCompetitionId, $sSeasonId );
                $arrPropertiesToConvert = array( "id" => function( $oObject, $vtPropValue) { $oObject->externid = $vtPropValue; } );
                Import_Factory::convertProperties( $arrData, $arrPropertiesToConvert );
            }
            else if ( $this->getParam('method') === "getgames" ) // for csexternid( always one round and one poule )
            {
                $sCSId = $this->getParam("csid"); // s1147
                $sCompetitionId = $this->getParam("competitionid"); // "eredivisie"
                $sSeasonId = $this->getParam("seasonid"); // "2015-2016"

                $oExternalLib = $this->getExternalLib();

                $arrData = $oExternalLib->getScheduledGames( $sCSId, $sCompetitionId, $sSeasonId );
                $arrPropertiesToConvert = array( "id" => function( $oObject, $vtPropValue) { $oObject->externid = $vtPropValue; } );
                Import_Factory::convertProperties( $arrData, $arrPropertiesToConvert );
            }
            else if ( $this->getParam('method') === "getgame" ) // for gameexternid
            {
                $sId = $this->getParam("id"); // 698196

                $oExternalLib = $this->getExternalLib();

                $oGame = Voetbal_Game_Factory::createObjectFromDatabase( (int) $sId );
                $sCSExternId = Import_Factory::getIdFromExternId( $oGame->getCompetitionSeason()->getExternId() );
                $oDefaultPlayerPeriodTimeSlot = Voetbal_Team_Membership_Player_Factory::getDefaultPlayerPeriodTimeSlot( $oGame );
                $arrData = $oExternalLib->getGame( $sCSExternId, $sId, $oDefaultPlayerPeriodTimeSlot );
                $arrPropertiesToConvert = array( "id" => function( $oObject, $vtPropValue) { $oObject->externid = $vtPropValue; } );
                Import_Factory::convertProperties( $arrData, $arrPropertiesToConvert );
            }
            else if ( $this->getParam('method') === "getplayer" ) // for personexternid ( maybe later for teamexternid to check transfers )
            {
                $sId = $this->getParam("id"); // 698196
                $oExternalLib = $this->getExternalLib();
                $arrData = $oExternalLib->getPlayer( $sId );
                $arrPropertiesToConvert = array( "id" => function( $oObject, $vtPropValue) { $oObject->externid = $vtPropValue; } );
                Import_Factory::convertProperties( $arrData, $arrPropertiesToConvert );
            }
            else {
                throw new Exception("geen method-parameter opgegeven");
            }
        }
        catch( Exception $e ) {
            $sMessage = $e->getMessage();
            $nCode = -1;
        }
        $this->_helper->jsonOutput( $arrData, $nCode, $sMessage );


    }
}
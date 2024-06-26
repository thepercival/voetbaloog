<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:44
 */

class VoetbalOog_Command_Handler_UpdateRoundBetConfigs
{
    protected $m_sControlRoundPrefix = '_roundid';
    protected $m_sControlBetTypePrefix = '_bettypeid';

    public function handle( VoetbalOog_Command_UpdateRoundBetConfigs $command )
    {
        $oRBCOwner = $command->getRBCOwner();
        if ( $oRBCOwner === null )
            throw new Exception( "de voorspelinstellingen-eigenaar is leeg", E_ERROR );

        $oDbWriter = VoetbalOog_Round_BetConfig_Factory::createDbWriter();

        $oCompetitionSeason = null;
        if ( $oRBCOwner instanceof Voetbal_CompetitionSeason_Interface )
            $oCompetitionSeason = $oRBCOwner;
        else if ( $oRBCOwner instanceof VoetbalOog_Pool_Interface )
            $oCompetitionSeason = $oRBCOwner->getCompetitionSeason();

        if ( $oCompetitionSeason === null )
            throw new Exception( "het competitieseizoen is leeg", E_ERROR );

        $oPool = null;
        if ( $oRBCOwner instanceof VoetbalOog_Pool_Interface ) {
            $oPool = $oRBCOwner;

            $oOptions = Construction_Factory::createOptions();
            $oOptions->addFilter( "VoetbalOog_Round_BetConfig::Pool", "EqualTo", $oPool );
            if ( VoetbalOog_Bet_Factory::getNrOfObjectsFromDatabase( $oOptions ) > 0 ) {
              throw new Exception( "de voorspelinstellingen kunnen niet meer gewijzigd worden, omdat er al voorspellingen zijn gedaan voor deze pool", E_ERROR );
            }
        }

        $oRoundBetConfigs = VoetbalOog_Round_BetConfig_Factory::createObjectsFromDatabaseExt( $oRBCOwner );
        foreach( $oRoundBetConfigs as $oBetConfigs ) {
            $oBetConfigs->addObserver( $oDbWriter );
            $oBetConfigs->flush();
        }

        $oBetConfigs = VoetbalOog_Round_BetConfig_Factory::createObjects();
        $oBetConfigs->addObserver( $oDbWriter );

        $arrBetConfigs = $command->getBetConfigs();
        foreach( $arrBetConfigs as $arrBetConfig )
        {
            $nRoundId = $arrBetConfig["roundid"];
            $nBetType = $arrBetConfig["bettype"];

            $oBetConfig = VoetbalOog_Round_BetConfig_Factory::createObject();
            $oBetConfig->putId( "__NEW__" . $nRoundId . "-".$nBetType );
            $oBetConfig->putRound( $nRoundId );
            $oBetConfig->putBetType( $nBetType );
            $oBetConfig->putBetTime( (int) $arrBetConfig["bettime"] );
            $oBetConfig->putPoints( (int) $arrBetConfig["points"] );
            $oBetConfig->putPool( $oPool );

            $oBetConfigs->add( $oBetConfig );
        }

        try {
            $oDbWriter->write();
        }
        catch ( Exception $e ) {
            return "voorspel-instellingen niet gewijzigd : ".$e->getMessage();
        }
        return true;
    }
}
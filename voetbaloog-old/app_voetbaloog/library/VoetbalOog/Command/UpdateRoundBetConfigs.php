<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class VoetbalOog_Command_UpdateRoundBetConfigs extends Voetbal_Command
{
    private $m_oRBCOwner;
    private $m_arrBetConfigs;

    private $m_oBus;

    public function __construct( $oRBCOwner, $arrBetConfigs )
    {
        $this->m_oRBCOwner = $oRBCOwner;
        $this->m_arrBetConfigs = $arrBetConfigs;
    }

    public function getRBCOwner(){ return $this->m_oRBCOwner; }
    public function getBetConfigs(){ return $this->m_arrBetConfigs; }

    public function getBus(){ return $this->m_oBus; }
    public function putBus( $oBus ){ $this->m_oBus = $oBus; }
}
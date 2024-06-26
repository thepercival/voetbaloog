<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class VoetbalOog_Command_UpdateBets extends Voetbal_Command
{
    private $m_oCompetitionSeason;
    private $m_oValidateDateTime;

    private $m_oBus;

    public function __construct( $oCompetitionSeason )
    {
        $this->m_oCompetitionSeason = $oCompetitionSeason;
    }

    public function getCompetitionSeason(){ return $this->m_oCompetitionSeason; }

    public function getValidateDateTime(){ return $this->m_oValidateDateTime; }
    public function putValidateDateTime( $oValidateDateTime ){ $this->m_oValidateDateTime = $oValidateDateTime; }

    public function getBus(){ return $this->m_oBus; }
    public function putBus( $oBus ){ $this->m_oBus = $oBus; }
}
<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class Voetbal_Command_RemoveCopyCSStructure extends Voetbal_Command
{
    private $m_oFromCompetitionSeason;
    private $m_oToCompetitionSeason;

    private $m_oBus;

    public function __construct( $oFromCompetitionSeason, $oToCompetitionSeason )
    {
        $this->m_oFromCompetitionSeason = $oFromCompetitionSeason;
        $this->m_oToCompetitionSeason = $oToCompetitionSeason;
    }

    public function getFromCompetitionSeason(){ return $this->m_oFromCompetitionSeason; }
    public function getToCompetitionSeason(){ return $this->m_oToCompetitionSeason; }

    public function getBus(){ return $this->m_oBus; }
    public function putBus( $oBus ){ $this->m_oBus = $oBus; }
}
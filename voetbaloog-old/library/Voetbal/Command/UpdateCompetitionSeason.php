<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class Voetbal_Command_UpdateCompetitionSeason extends Voetbal_Command
{
    private $m_oCompetitionSeason;
    private $m_bPublic;
    
    private $m_oBus;

    public function __construct( Voetbal_CompetitionSeason $oCompetitionSeason )
    {
        $this->m_oCompetitionSeason = $oCompetitionSeason;
    }

    public function getCompetitionSeason(){ return $this->m_oCompetitionSeason; }

    public function getPublic(){ return $this->m_bPublic; }
    public function putPublic( $bPublic ){ $this->m_bPublic = $bPublic; }

    public function getBus(){ return $this->m_oBus; }
    public function putBus( $oBus ){ $this->m_oBus = $oBus; }
}
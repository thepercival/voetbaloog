<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class Voetbal_Command_AddCompetitionSeason extends Voetbal_Command
{
    private $m_oCompetition;
    private $m_oSeason;
    private $m_oDefaultAssociation;

    private $m_oBus;

    private $m_bPublic = false;
    private $m_sExternId = null;

    public function __construct( $oCompetition, $oSeason )
    {
        $this->m_oCompetition = $oCompetition;
        $this->m_oSeason = $oSeason;
    }

    public function getCompetition(){ return $this->m_oCompetition; }
    public function getSeason(){ return $this->m_oSeason; }

    public function getDefaultAssociation(){ return $this->m_oDefaultAssociation; }
    public function putDefaultAssociation( $oDefaultAssociation ){ $this->m_oDefaultAssociation = $oDefaultAssociation; }
    
    public function getPublic(){ return $this->m_bPublic; }
    public function putPublic( $bPublic ){ $this->m_bPublic = $bPublic; }
    
    public function getExternId(){ return $this->m_sExternId; }
    public function putExternId( $sExternId ){ $this->m_sExternId = $sExternId; }

    public function getBus(){ return $this->m_oBus; }
    public function putBus( $oBus ){ $this->m_oBus = $oBus; }
}
<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class Voetbal_Command_RemoveAddCSGames extends Voetbal_Command
{
    private $m_oCompetitionSeason;      // Voetbal_CompetitionSeason
    private $m_oStartDateTime;          // DateTime
    private $m_nMinutesBetweenGames;    // int

    // private $m_oBus;

    public function __construct( $oCompetitionSeason )
    {
        $this->m_oCompetitionSeason = $oCompetitionSeason;
    }

    public function getCompetitionSeason(){ return $this->m_oCompetitionSeason; }

    public function getStartDateTime(){ return $this->m_oStartDateTime; }
    public function putStartDateTime( $oStartDateTime ){ $this->m_oStartDateTime = $oStartDateTime; }

    public function getMinutesBetweenGames(){ return $this->m_nMinutesBetweenGames; }
    public function putMinutesBetweenGames( $nMinutesBetweenGames ){ $this->m_nMinutesBetweenGames = $nMinutesBetweenGames; }
    
    // public function getBus(){ return $this->m_oBus; }
    // public function putBus( $oBus ){ $this->m_oBus = $oBus; }
}
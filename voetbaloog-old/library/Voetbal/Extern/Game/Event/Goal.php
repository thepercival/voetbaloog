<?php

/**
 * @package Voetbal
 */
class Voetbal_Extern_Game_Event_Goal extends Voetbal_Extern_Game_Event
{
    /**
     * @var int
     */
    protected $m_nHome;
    /**
     * @var int
     */
    protected $m_nAway;
    /**
     * @var bool
     */
    protected $m_bOwn;
    /**
     * @var bool
     */
    protected $m_bPenalty;
    /**
     * @var Voetbal_Extern_Game_Participation
     */
    protected $m_oAssist;


    public function __construct( Voetbal_Extern_Game_Participation $oGameParticipation, int $nMinute, int $nHome, int $nAway )
    {
        parent::__construct( $oGameParticipation, $nMinute );
        $this->m_nHome = $nHome;
        $this->m_nAway = $nAway;
        $this->m_bOwn = false;
        $this->m_bPenalty = false;
        $this->putId($oGameParticipation->getId() . "_" . $nMinute . "_goal_" . $nHome . "_" . $nAway);
    }

    public function getGameParticipation(): Voetbal_Extern_Game_Participation
    {
        return $this->m_oGameParticipation;
    }

    public function getMinute(): int
    {
        return $this->m_nMinute;
    }

    public function getHome(): int
    {
        return $this->m_nHome;
    }

    public function getAway(): int
    {
        return $this->m_nAway;
    }

    public function getOwn(): ? bool
    {
        return $this->m_bOwn;
    }

    public function putOwn( bool $bOwn )
    {
        $this->m_bOwn = $bOwn;
    }

    public function getPenalty(): bool
    {
        return $this->m_bPenalty;
    }

    public function putPenalty( bool $bPenalty )
    {
        $this->m_bPenalty = $bPenalty;
    }

    public function getAssist(): ?Voetbal_Extern_Game_Participation
    {
        return $this->m_oAssist;
    }

    public function putAssist( Voetbal_Extern_Game_Participation $oAssist )
    {
        $this->m_oAssist = $oAssist;
    }
}

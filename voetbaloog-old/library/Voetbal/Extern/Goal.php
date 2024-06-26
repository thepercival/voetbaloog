<?php

/**
 * @package Voetbal
 */
class Voetbal_Extern_Goal extends Patterns_Idable_Object
{
    /**
     * @var Voetbal_Extern_Game_Participation
     */
    protected $m_oGameParticipation;
    /**
     * @var int
     */
    protected $m_nMinute;
    /**
     * @var bool
     */
    protected $m_bPenalty;
    /**
     * @var bool
     */
    protected $m_bOwnGoal;
    /**
     * @var Voetbal_Extern_Game_Participation
     */
    protected $m_oAssistGameParticipation;

    public function __construct( Voetbal_Extern_Game_Participation $oGameParticipation, int $nHome, int $nAway )
    {
        parent::__construct();
        $this->putId( $nHome . "_" . $nAway );
        $this->m_oGameParticipation = $oGameParticipation;
        $this->m_nMinute = 0;
        $this->m_bPenalty = false;
        $this->m_bOwnGoal = false;
        
        $oGameParticipation->getGoals()->add( $this );
        $oGameParticipation->getGame()->getGoals()->add( $this );
    }

    public function getGameParticipation(): Voetbal_Extern_Game_Participation
    {
        return $this->m_oGameParticipation;
    }

    public function getMinute(): int
    {
        return $this->m_nMinute;
    }

    public function putMinute( int $nMinute )
    {
        $this->m_nMinute = $nMinute;
    }

    public function getPenalty(): bool
    {
        return $this->m_bPenalty;
    }

    public function putPenalty( bool $bPenalty )
    {
        $this->m_bPenalty = $bPenalty;
    }

    public function getOwnGoal(): bool
    {
        return $this->m_bOwnGoal;
    }

    public function putOwnGoal( bool $bOwnGoal )
    {
        $this->m_bOwnGoal = $bOwnGoal;
    }

    public function getAssistGameParticipation(): ?Voetbal_Extern_Game_Participation
    {
        return $this->m_oAssistGameParticipation;
    }

    public function putAssistGameParticipation( Voetbal_Extern_Game_Participation $oAssist )
    {
        $this->m_oAssistGameParticipation = $oAssist;
    }
}

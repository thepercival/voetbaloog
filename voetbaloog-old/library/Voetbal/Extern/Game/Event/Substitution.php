<?php

/**
 * @package Voetbal
 */
class Voetbal_Extern_Game_Event_Substitution extends Voetbal_Extern_Game_Event
{
    /**
     * @var Voetbal_Extern_Game_Participation
     */
    protected $m_oIn;

    public function __construct( Voetbal_Extern_Game_Participation $oGameParticipation, int $nMinute, Voetbal_Extern_Game_Participation $oIn )
    {
        parent::__construct( $oGameParticipation, $nMinute );
        $this->m_oIn = $oIn;
        $this->putId($oGameParticipation->getId() . "_" . $nMinute . "_substiture" );
    }

    public function getGameParticipation(): Voetbal_Extern_Game_Participation
    {
        return $this->m_oGameParticipation;
    }

    public function getMinute(): int
    {
        return $this->m_nMinute;
    }

    public function getIn(): Voetbal_Extern_Game_Participation
    {
        return $this->m_oIn;
    }
}

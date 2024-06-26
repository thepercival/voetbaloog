<?php

/**
 * @package Voetbal
 */
abstract class Voetbal_Extern_Game_Event extends Patterns_Idable_Object
{
    /**
     * @var Voetbal_Extern_Game_Participation
     */
    protected $m_oGameParticipation;
    /**
     * @var int
     */
    protected $m_nMinute;

    public function __construct( Voetbal_Extern_Game_Participation $oGameParticipation, int $nMinute )
    {
        parent::__construct();
        $this->m_oGameParticipation = $oGameParticipation;
        $this->m_nMinute = $nMinute;
        $oGameParticipation->getGame()->getEvents()->add( $this );
    }

    public function getGameParticipation(): Voetbal_Extern_Game_Participation
    {
        return $this->m_oGameParticipation;
    }

    public function getMinute(): int
    {
        return $this->m_nMinute;
    }
}
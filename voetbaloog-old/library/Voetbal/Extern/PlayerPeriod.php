<?php

/**
 * @package Voetbal
 */
class Voetbal_Extern_PlayerPeriod
{
    /**
     * @var Voetbal_Extern_Team
     */
    protected $m_oTeam;
    /**
     * @var Voetbal_Extern_Person
     */
    protected $m_oPerson;
    /**
     * @var Agenda_TimeSlot
     */
    protected $m_oTimeSlot;

    /**
     * @var int
     */
    protected $m_nLine;


    public function __construct( Voetbal_Extern_Team $oTeam, Voetbal_Extern_Person $oPerson, Agenda_TimeSlot $oTimeSlot = null )
    {
        $this->m_oTeam = $oTeam;
        $this->m_oPerson = $oPerson;
        $this->m_oTimeSlot = $oTimeSlot;
    }

    public function getTeam(): Voetbal_Extern_Team
    {
        return $this->m_oTeam;
    }

    public function getPerson(): Voetbal_Extern_Person
    {
        return $this->m_oPerson;
    }

    public function getTimeSlot(): ?Agenda_TimeSlot
    {
        return $this->m_oTimeSlot;
    }

    public function getLine(): ?int {
        return $this->m_nLine;
    }

    public function putLine(int $nLine) {
        $this->m_nLine = $nLine;
    }
}
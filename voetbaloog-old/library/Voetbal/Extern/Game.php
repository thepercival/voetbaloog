<?php

/**
 * @package Voetbal
 */
class Voetbal_Extern_Game extends Patterns_Idable_Object
{
    /**
     * @var int
     */
    protected $m_nRoundNumber;

    /**
     * @var Agenda_DateTime
     */
    protected $m_oStartDateTime;

    /**
     * @var Voetbal_Extern_Team
     */
    protected $m_oHomeTeam;
    /**
     * @var Voetbal_Extern_Team
     */
    protected $m_oAwayTeam;

    public function __construct( string $vtId )
    {
        parent::__construct();
        $this->putId( $vtId );
    }

    public function getRoundNumber(): ?int
    {
        return $this->m_nRoundNumber;
    }

    public function putRoundNumber( int $nRoundNumber )
    {
        $this->m_nRoundNumber = $nRoundNumber;
    }

    public function getStartDateTime(): ?Agenda_DateTime
    {
        return $this->m_oStartDateTime;
    }

    public function putStartDateTime( Agenda_DateTime $oStartDateTime )
    {
        $this->m_oStartDateTime = $oStartDateTime;
    }

    public function getHomeTeam(): ?Voetbal_Extern_Team
    {
        return $this->m_oHomeTeam;
    }

    public function putHomeTeam( Voetbal_Extern_Team $oHomeTeam )
    {
        $this->m_oHomeTeam = $oHomeTeam;
    }

    public function getAwayTeam(): ?Voetbal_Extern_Team
    {
        return $this->m_oAwayTeam;
    }

    public function putAwayTeam( Voetbal_Extern_Team $oAwayTeam )
    {
        $this->m_oAwayTeam = $oAwayTeam;
    }

    public function getTeam( int $nHomeAway ): ?Voetbal_Extern_Team
    {
        return $nHomeAway === Voetbal_Game::HOME ? $this->getHomeTeam() : $this->getAwayTeam();
    }
}

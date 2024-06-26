<?php

/**
 * @package Voetbal
 */
class Voetbal_Extern_Game_Participation extends Patterns_Idable_Object
{
    /**
     * @var Voetbal_Extern_GameExt
     */
    protected $m_oGame;
    /**
     * @var Voetbal_Extern_PlayerPeriod
     */
    protected $m_oPlayerPeriod;
    /**
     * @var int
     */
    protected $m_nIn;
    /**
     * @var int
     */
    protected $m_nOut;
    /**
     * @var int
     */
    protected $m_nYellowCard;
    /**
     * @var int
     */
    protected $m_nYellowCard2;
    /**
     * @var int
     */
    protected $m_nRedCard;
    /**
     * @var Patterns_Collection|Voetbal_Extern_Goal[]
     */
    protected $m_oGoals;

    public function __construct( Voetbal_Extern_GameExt $oGame, Voetbal_Extern_PlayerPeriod $oPlayerPeriod )
    {
        parent::__construct();
        $this->putId( $oPlayerPeriod->getPerson()->getId() );
        $this->m_oGame = $oGame;
        $this->m_oPlayerPeriod = $oPlayerPeriod;
        $this->m_nIn = 0;
        $this->m_nOut = 0;
        $this->m_nYellowCard = 0;
        $this->m_nYellowCard2 = 0;
        $this->m_nRedCard = 0;
        $this->m_oGoals = Patterns_Factory::createCollection();

        $nHomeAway = $oGame->getHomeTeam() == $oPlayerPeriod->getTeam() ? Voetbal_Game::HOME : Voetbal_Game::AWAY;
        $oGame->getParticipations( $nHomeAway )->add( $this );

    }

    public function getGame(): Voetbal_Extern_GameExt
    {
        return $this->m_oGame;
    }

    public function getPlayerPeriod(): Voetbal_Extern_PlayerPeriod
    {
        return $this->m_oPlayerPeriod;
    }

    public function getIn(): int
    {
        return $this->m_nIn;
    }

    public function putIn( int $nIn )
    {
        $this->m_nIn = $nIn;
    }

    public function getOut(): int
    {
        return $this->m_nOut;
    }

    public function putOut( int $nOut )
    {
        $this->m_nOut = $nOut;
    }

    public function getYellowCard(): int
    {
        return $this->m_nYellowCard;
    }

    public function putYellowCard( int $nYellowCard )
    {
        $this->m_nYellowCard = $nYellowCard;
    }

    public function getYellowCard2(): int
    {
        return $this->m_nYellowCard2;
    }

    public function putYellowCard2( int $nYellowCard2 )
    {
        $this->m_nYellowCard2 = $nYellowCard2;
    }

    public function getRedCard(): int
    {
        return $this->m_nRedCard;
    }

    public function putRedCard( int $nRedCard )
    {
        $this->m_nRedCard = $nRedCard;
    }

    public function getGoals(): Patterns_Collection {
        return $this->m_oGoals;
    }
}

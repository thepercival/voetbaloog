<?php

/**
 * @package Voetbal
 */
class Voetbal_Extern_GameExt extends Voetbal_Extern_Game
{
    /**
     * @var int
     */
    protected $m_nState;
    /**
     * @var int
     */
    protected $m_nHomeGoals;
    /**
     * @var int
     */
    protected $m_nAwayGoals;
    /**
     * @var Patterns_Collection|Voetbal_Extern_Game_Participation[]
     */
    protected $m_oHomeParticipations;
    /**
     * @var Patterns_Collection|Voetbal_Extern_Game_Participation[]
     */
    protected $m_oAwayParticipations;
    /**
     * @var Patterns_Collection|Voetbal_Extern_Game_Event[]
     */
    protected $m_oEvents;
    /**
     * @var Patterns_Collection|Voetbal_Extern_Goal[]
     */
    protected $m_oGoals;

    public function __construct( string $vtId )
    {
        parent::__construct( $vtId );
        $this->m_oHomeParticipations = Patterns_Factory::createCollection();
        $this->m_oAwayParticipations = Patterns_Factory::createCollection();
        $this->m_oEvents = Patterns_Factory::createCollection();
        $this->m_oGoals = Patterns_Factory::createCollection();
    }

    public function getState(): ?int
    {
        return $this->m_nState;
    }

    public function putState( int $nState )
    {
        $this->m_nState = $nState;
    }

    public function getHomeGoals(): ?int
    {
        return $this->m_nHomeGoals;
    }

    public function putHomeGoals( int $nHomeGoals )
    {
        $this->m_nHomeGoals = $nHomeGoals;
    }

    public function getAwayGoals(): ?int
    {
        return $this->m_nAwayGoals;
    }

    public function putAwayGoals( int $nAwayGoals )
    {
        $this->m_nAwayGoals = $nAwayGoals;
    }

    public function getParticipations( int $nHomeAway = null ): Patterns_Collection
    {
        if( $nHomeAway === Voetbal_Game::HOME ) {
            return $this->m_oHomeParticipations;
        }
        else if( $nHomeAway === Voetbal_Game::AWAY ) {
            return $this->m_oAwayParticipations;
        }
        $oParticipations = Patterns_Factory::createCollection();
        $oParticipations->addCollection( $this->getParticipations(Voetbal_Game::HOME) );
        $oParticipations->addCollection( $this->getParticipations(Voetbal_Game::AWAY) );
        return $oParticipations;
    }

    public function getParticipation( Voetbal_Extern_Person $oPerson ): ?Voetbal_Extern_Game_Participation
    {
        foreach( $this->getParticipations() as $oParticipation ) {
            if( $oParticipation->getPlayerPeriod()->getPerson()->getId() === $oPerson->getId() ) {
                return $oParticipation;
            }
        }
        return null;
    }

    public function getEvents( int $nHomeAway = null ): Patterns_Collection
    {
        if( $nHomeAway === null ) {
            return $this->m_oEvents;
        }

        $oTeam = $nHomeAway === Voetbal_Game::HOME ? $this->getHomeTeam() : $this->getAwayTeam();

        $oEvents = Patterns_Factory::createCollection();
        foreach( $this->m_oEvents as $oEvent ) {
            if( $oEvent->getGameParticipation()->getPlayerPeriod()->getTeam() === $oTeam ) {
                $oEvents->add( $oEvent );
            }
        }
        return $oEvents;
    }

    public function convertEvent( Voetbal_Extern_Game_Event $oGameEvent ) {

        if( $oGameEvent instanceof Voetbal_Extern_Game_Event_Card ) {
            if( $oGameEvent->getCard() === Voetbal_Game::DETAIL_YELLOWCARDONE ) {
                $oGameEvent->getGameParticipation()->putYellowCard( $oGameEvent->getMinute() );
            } else if( $oGameEvent->getCard() === Voetbal_Game::DETAIL_YELLOWCARDTWO ) {
                $oGameEvent->getGameParticipation()->putYellowCard2( $oGameEvent->getMinute() );
            } else if( $oGameEvent->getCard() === Voetbal_Game::DETAIL_REDCARD ) {
                $oGameEvent->getGameParticipation()->putRedCard( $oGameEvent->getMinute() );
            }
        } else if( $oGameEvent instanceof Voetbal_Extern_Game_Event_Substitution ) {
            $oGameEvent->getGameParticipation()->putOut( $oGameEvent->getMinute() );
            $oGameEvent->getIn()->putIn( $oGameEvent->getMinute() );
        } else if( $oGameEvent instanceof Voetbal_Extern_Game_Event_Goal ) {
            $oGoal = new Voetbal_Extern_Goal( $oGameEvent->getGameParticipation(), $oGameEvent->getHome(), $oGameEvent->getAway() );
            $oGoal->putMinute( $oGameEvent->getMinute() );
            $oGoal->putOwnGoal( $oGameEvent->getOwn() );
            $oGoal->putPenalty( $oGameEvent->getPenalty() );
            if( $oGameEvent->getAssist() !== null ) {
                $oGoal->putAssistGameParticipation( $oGameEvent->getAssist() );
            }
        }
    }

    public function getGoals(): Patterns_Collection {
        return $this->m_oGoals;
    }
}

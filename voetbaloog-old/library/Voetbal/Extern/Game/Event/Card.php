<?php

/**
 * @package Voetbal
 */
class Voetbal_Extern_Game_Event_Card extends Voetbal_Extern_Game_Event
{
    /**
     * @var int
     */
    protected $m_nCard;

    public function __construct( Voetbal_Extern_Game_Participation $oGameParticipation, int $nMinute, int $nCard )
    {
        parent::__construct( $oGameParticipation, $nMinute );
        $this->m_nCard = $nCard;
        $this->putId($oGameParticipation->getId() . "_" . $nMinute . "_card_" . $nCard);
    }

    public function getGameParticipation(): Voetbal_Extern_Game_Participation
    {
        return $this->m_oGameParticipation;
    }

    public function getMinute(): int
    {
        return $this->m_nMinute;
    }

    public function getCard(): int
    {
        return $this->m_nCard;
    }


}

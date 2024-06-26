<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class Voetbal_Command_UpdateGame extends Voetbal_Command
{
    private $m_oGame;
    private $m_oGoals;
    private $m_nState;
    private $m_oStartDateTime;
    private $m_nNumber;
    private $m_arrHomeParticipations;
    private $m_arrAwayParticipations;
    private $m_arrHomeEvents;
    private $m_arrAwayEvents;
    private $m_oLocation;
    private $m_bSwitchHomeAway;

    private $m_oBus;

    public function __construct( Voetbal_Game $oGame, stdClass $oGoals, $nState )
    {
        $this->m_oGame = $oGame;
        $this->m_oGoals = $oGoals;
        $this->m_nState = $nState;
    }

    public function getGame(){ return $this->m_oGame; }
    public function getGoals(){ return $this->m_oGoals; }
    public function getState(){ return $this->m_nState; }

    public function getStartDateTime(){ return $this->m_oStartDateTime; }
    public function putStartDateTime( $oStartDateTime ){ $this->m_oStartDateTime = $oStartDateTime; }

    public function getNumber(){ return $this->m_nNumber; }
    public function putNumber( $nNumber ){ $this->m_nNumber = $nNumber; }

    public function putHomeDetails( $arrParticipations, $arrEvents )
    {
        $this->m_arrHomeParticipations = $arrParticipations;
        $this->m_arrHomeEvents = $arrEvents;
    }
    /**
        [
            {
                "player":
                {
                    "id": 3533,
                    "name": "Bram Castro"
                },
                "in": 0,
                "out": 0
            },
        ]
     */
    public function getHomeParticipations() { return $this->m_arrHomeParticipations; }
    /**
        [
            {
                "type": "yc1",
                "minute": 28,
                "player":
                {
                    "id": 214543,
                    "name": "Wout Weghorst"
                },
                "subplayer": null
            }
        ]
     */
    public function getHomeEvents() { return $this->m_arrHomeEvents; }

    public function putAwayDetails( $arrParticipations, $arrEvents )
    {
        $this->m_arrAwayParticipations = $arrParticipations;
        $this->m_arrAwayEvents = $arrEvents;
    }
    public function getAwayParticipations() { return $this->m_arrAwayParticipations; }
    public function getAwayEvents() { return $this->m_arrAwayEvents; }

    public function getLocation(){ return $this->m_oLocation; }
    public function putLocation( $oLocation ){ $this->m_oLocation = $oLocation; }

    public function switchHomeAway(){ $this->m_bSwitchHomeAway = true; }
    public function shouldSwitchHomeAway(){ return $this->m_bSwitchHomeAway; }

    public function getBus(){ return $this->m_oBus; }
    public function putBus( $oBus ){ $this->m_oBus = $oBus; }
}
<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class Voetbal_Command_UpdateFirstRoundTeams extends Voetbal_Command
{
    /**
     * @var Voetbal_Round
     */
    private $m_oRound;
    /**
     * @var array
     */
    private $m_arrStructure;
    /**
     * @var string
     */
    private $m_sEditMode;

    public function __construct( $oRound, $arrStructure, $sEditMode )
    {
        $this->m_oRound = $oRound;
        $this->m_arrStructure = $arrStructure;
        $this->m_sEditMode = $sEditMode;
    }

    public function getRound(){ return $this->m_oRound; }
    /**
     * {
     *  "number": 0,
     *  "poules": [ {
     *              "number": 0,
     *              "places": [{
     *                          "number": 0,
     *                          "team": { "id" : 1, "name" : "een testnaam" }
     *                          }]
     *           }]
     * }
     *
     * @return mixed
     */
    public function getStructure(){ return $this->m_arrStructure; }

    public function getEditMode() { return $this->m_sEditMode; }
}
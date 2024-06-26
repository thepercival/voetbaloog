<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 4-12-15
 * Time: 16:07
 */

class Voetbal_Command_SupplementTeams extends Voetbal_Command
{
    private $m_oRound;
    private $m_arrTeams = array();

    public function __construct( $oRound )
    {
        $this->m_oRound = $oRound;
    }

    public function getRound(){ return $this->m_oRound; }

    public function getTeams(){ return $this->m_arrTeams; }

    /**
     * 2 dimensional array of teams indexed by poulenr and pouleplacenr
     *
     * array(
     *      0 => array(
     *              0 => team1,
     *              1 => team2 ),
     *      1 => array(
     *              0 => team3,
     *              1 => team4 )
     * )
     *
     * @param array $arrTeams
     */
    public function putTeams( array $arrTeams ){ $this->m_arrTeams = $arrTeams; }
}
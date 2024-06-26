<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 4-12-15
 * Time: 16:07
 */

class Voetbal_Command_AddUpdatePerson extends Voetbal_Command
{
    private $m_oPerson;
    private $m_sFirstName;
    private $m_sNameInsertions;
    private $m_sLastName;
    private $m_oDateOfBirth;
    private $m_arrPlayerPeriods;
    private $m_sExternId;

    private $m_oBus;

    public function __construct( $oPerson )
    {
        $this->m_oPerson = $oPerson;
    }

    /*
        {
            externid: 6137
            name : "Wout Brama"
            playerperiods : "see addupdateplayerperiods"
        }
    */
    public function getPerson(){ return $this->m_oPerson; }

    public function getFirstName(){ return $this->m_sFirstName; }
    public function putFirstName( $sFirstName ){ $this->m_sFirstName = $sFirstName; }

    public function getNameInsertions(){ return $this->m_sNameInsertions; }
    public function putNameInsertions( $sNameInsertions ){ $this->m_sNameInsertions = $sNameInsertions; }

    public function getLastName(){ return $this->m_sLastName; }
    public function putLastName( $sLastName ){ $this->m_sLastName = $sLastName; }

    public function getDateOfBirth(){ return $this->m_oDateOfBirth; }
    public function putDateOfBirth( $oDateOfBirth ){ $this->m_oDateOfBirth = $oDateOfBirth; }

    public function getPlayerPeriods(){ return $this->m_arrPlayerPeriods; }
    public function putPlayerPeriods( $arrPlayerPeriods ){ $this->m_arrPlayerPeriods = $arrPlayerPeriods; }

    public function getExternId(){ return $this->m_sExternId; }
    public function putExternId( $sExternId ){ $this->m_sExternId = $sExternId; }

    public function getBus(){ return $this->m_oBus; }
    public function putBus( $oBus ){ $this->m_oBus = $oBus; }
}


<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class Voetbal_Command_ValidatePerson extends Voetbal_Command
{
    private $m_oPerson;

    public function __construct( Voetbal_Person $oPerson )
    {
        $this->m_oPerson = $oPerson;
    }

    public function getPerson(){ return $this->m_oPerson; }
}
<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

// All you need to do is pass an array mapping your command class names to
// your handler instances. Everything else is already setup.


class Voetbal_Command_AddCompetition extends Voetbal_Command
{
    private $m_sName;
    private $m_sAbbreviation;

    public function __construct( $sName, $sAbbreviation )
    {
        $this->m_sName = $sName;
        $this->m_sAbbreviation = $sAbbreviation;
    }

    public function getName(){ return $this->m_sName; }
    public function getAbbreviation(){ return $this->m_sAbbreviation; }
}
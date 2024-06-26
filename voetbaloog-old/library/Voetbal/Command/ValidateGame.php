<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class Voetbal_Command_ValidateGame extends Voetbal_Command
{
    private $m_oGame;

    public function __construct( Voetbal_Game $oGame )
    {
        $this->m_oGame = $oGame;
    }

    public function getGame(){ return $this->m_oGame; }
}
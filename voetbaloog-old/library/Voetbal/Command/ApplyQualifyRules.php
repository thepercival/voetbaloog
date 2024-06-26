<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 4-12-15
 * Time: 16:07
 */

class Voetbal_Command_ApplyQualifyRules extends Voetbal_Command
{
    private $m_oGame;

    public function __construct( $oGame )
    {
        $this->m_oGame = $oGame;
    }

    /**
     * @return Voetbal_Game
     */
    public function getGame(){ return $this->m_oGame; }
}
<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class Voetbal_Command_ImportGame extends Voetbal_Command
{
    /**
     * @var Voetbal_Game
     */
    private $m_oGame;
    /**
     * @var Voetbal_Extern_GameExt
     */
    private $m_oExternGame;

    private $m_oBus;

    public function __construct( Voetbal_Game $oGame, Voetbal_Extern_GameExt $oExternGame )
    {
        $this->m_oGame = $oGame;
        $this->m_oExternGame = $oExternGame;
    }

    public function getGame(){ return $this->m_oGame; }
    public function getExternGame(): Voetbal_Extern_GameExt{ return $this->m_oExternGame; }

    public function getBus(){ return $this->m_oBus; }
    public function putBus( $oBus ){ $this->m_oBus = $oBus; }
}
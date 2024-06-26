<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		VoetbalOog
 */

use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;

/**
 * @package VoetbalOog
 */
class VoetbalOog_Command_Main_Factory extends Voetbal_Command_Main_Factory
{
    private static $m_arrCommandClassToHandlerMap;

    public static function getCommandClassToHandlerMap()
    {
        if ( static::$m_arrCommandClassToHandlerMap === null ){
            static::$m_arrCommandClassToHandlerMap = array(
                "VoetbalOog_Command_UpdateRoundBetConfigs" => new VoetbalOog_Command_Handler_UpdateRoundBetConfigs(),
                // "VoetbalOog_Command_SaveBets" => new VoetbalOog_Command_Handler_SaveBets(),
                "VoetbalOog_Command_UpdateBets" => new VoetbalOog_Command_Handler_UpdateBets(),
                "VoetbalOog_Command_CopyBets" => new VoetbalOog_Command_Handler_CopyBets()
            );
        }
        return static::$m_arrCommandClassToHandlerMap;
    }
}
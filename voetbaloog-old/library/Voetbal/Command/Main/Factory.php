<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;

/**
 * @package Voetbal
 */
class Voetbal_Command_Main_Factory
{
    private static $m_arrCommandClassToHandlerMap;

    public static function getMiddleWare( $arrCommandClassToHandlerMap = [] )
    {
        $arrCompleteCommandClassToHandlerMap = array_merge( $arrCommandClassToHandlerMap, static::getCommandClassToHandlerMap() );

        return new League\Tactician\Handler\CommandHandlerMiddleware(
            new ClassNameExtractor(),
            new InMemoryLocator( $arrCompleteCommandClassToHandlerMap ),
            new Voetbal_Command_Main_Inflector()
        );
    }

    public static function getCommandClassToHandlerMap()
    {
        if ( static::$m_arrCommandClassToHandlerMap === null ){
            static::$m_arrCommandClassToHandlerMap = array(
                "Voetbal_Command_AddCompetition" => new Voetbal_Command_Handler_AddCompetition(),
                "Voetbal_Command_AddCompetitionSeason" => new Voetbal_Command_Handler_AddCompetitionSeason(),
                "Voetbal_Command_AddUpdatePerson" => new Voetbal_Command_Handler_AddUpdatePerson(),
                "Voetbal_Command_AddUpdatePlayerPeriod" => new Voetbal_Command_Handler_AddUpdatePlayerPeriod(),
                "Voetbal_Command_ApplyQualifyRules" => new Voetbal_Command_Handler_ApplyQualifyRules(),
                "Voetbal_Command_ImportGame" => new Voetbal_Command_Handler_ImportGame(),
                "Voetbal_Command_RemoveAddCSStructure" => new Voetbal_Command_Handler_RemoveAddCSStructure(),
                "Voetbal_Command_RemoveCopyCSStructure" => new Voetbal_Command_Handler_RemoveCopyCSStructure(),
                "Voetbal_Command_RemoveAddCSGames" => new Voetbal_Command_Handler_RemoveAddCSGames(),
                "Voetbal_Command_SupplementTeams" => new Voetbal_Command_Handler_SupplementTeams(),
                "Voetbal_Command_UpdateCompetitionSeason" => new Voetbal_Command_Handler_UpdateCompetitionSeason(),
                "Voetbal_Command_UpdateGame" => new Voetbal_Command_Handler_UpdateGame(),
                "Voetbal_Command_UpdateFirstRoundTeams" => new Voetbal_Command_Handler_UpdateFirstRoundTeams(),
                "Voetbal_Command_ValidateGame" => new Voetbal_Command_Handler_ValidateGame(),
                "Voetbal_Command_ValidatePerson" => new Voetbal_Command_Handler_ValidatePerson()
            );
        }
        return static::$m_arrCommandClassToHandlerMap;
    }

    /*public static function getBus()
    {
        $handlerMiddleware = FCToernooi_Command_Factory::getMiddleWare();
        $loggingMiddleware = new Voetbal_Command_Middleware_Logging( new Voetbal_Command_Middleware_Logger() );
        $transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );
        $lockingMiddleware = new LockingMiddleware();

        $commandBus = new \League\Tactician\CommandBus([$loggingMiddleware,$transactionMiddleware,$lockingMiddleware,$handlerMiddleware]);
    }*/
}
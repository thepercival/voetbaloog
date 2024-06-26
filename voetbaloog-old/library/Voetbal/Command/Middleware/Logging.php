<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 6-12-15
 * Time: 13:08
 */

use League\Tactician\Middleware;

class Voetbal_Command_Middleware_Logger
{
    public function log($info)
    {
        echo "LOG: $info" . PHP_EOL . "<br>";
    }
}

class Voetbal_Command_Middleware_Logging implements Middleware
{
    protected $logger;

    public function __construct( Voetbal_Command_Middleware_Logger $logger)
    {
        $this->logger = $logger;
    }

    public function execute($command, callable $next)
    {
        $commandClass = get_class($command);

        $this->logger->log($commandClass . " started" );
        $returnValue = $next($command);
        $this->logger->log($commandClass  . " finished");

        return $returnValue;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 6-12-15
 * Time: 8:01
 */

use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;

class Voetbal_Command_Main_Inflector implements MethodNameInflector
{
    // You can use the command and commandHandler to generate any name you
    // prefer but here, we'll always return the same one.
    public function inflect($command, $commandHandler)
    {
        return 'handle';
    }
}
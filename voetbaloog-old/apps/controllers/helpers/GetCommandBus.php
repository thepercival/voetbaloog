<?php

/**
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Apps_Helper_GetCommandBus extends Zend_Controller_Action_Helper_Abstract
{
    public function direct()
    {
        $handlerMiddleware = Voetbal_Command_Main_Factory::getMiddleWare();
        $transactionMiddleware = new Voetbal_Command_Middleware_Transaction( Zend_Registry::get("db") );
        return new \League\Tactician\CommandBus([$transactionMiddleware,$handlerMiddleware]);
    }
}
?>
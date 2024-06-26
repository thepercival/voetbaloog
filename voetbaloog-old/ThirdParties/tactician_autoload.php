<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 4-12-15
 * Time: 15:55
 */

function autoload_36a83fa87c775cda0788c61b384c4e25($class)
{
    $classes = array(
        'League\Tactician\CommandBus' => __DIR__ .'/Tactician/src/CommandBus.php',
        'League\Tactician\Handler\Locator\InMemoryLocator' => __DIR__ .'/Tactician/src/Handler/Locator/InMemoryLocator.php',
        'League\Tactician\Handler\Locator\HandlerLocator' => __DIR__ .'/Tactician/src/Handler/Locator/HandlerLocator.php',
        'League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor' => __DIR__ .'/Tactician/src/Handler/CommandNameExtractor/CommandNameExtractor.php',
        'League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor' => __DIR__ .'/Tactician/src/Handler/CommandNameExtractor/ClassNameExtractor.php',
        'League\Tactician\Handler\CommandHandlerMiddleware' => __DIR__ .'/Tactician/src/Handler/CommandHandlerMiddleware.php',
        'League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector' => __DIR__ .'/Tactician/src/Handler/MethodNameInflector/HandleClassNameInflector.php',
        'League\Tactician\Handler\MethodNameInflector\MethodNameInflector' => __DIR__ .'/Tactician/src/Handler/MethodNameInflector/MethodNameInflector.php',
        'League\Tactician\Middleware' => __DIR__ .'/Tactician/src/Middleware.php',
        'League\Tactician\Exception\CanNotInvokeHandlerException' => __DIR__ .'/Tactician/src/Exception/CanNotInvokeHandlerException.php',
        'League\Tactician\Exception\MissingHandlerException' => __DIR__ .'/Tactician/src/Exception/MissingHandlerException.php',
        'League\Tactician\Exception\Exception' => __DIR__ .'/Tactician/src/Exception/Exception.php',
        // 'League\Tactician\Plugins\TransactionMiddleware' => __DIR__ .'/Tactician/src/Plugins/TransactionMiddleware.php',
        'League\Tactician\Plugins\LockingMiddleware' => __DIR__ .'/Tactician/src/Plugins/LockingMiddleware.php',
    );

    if (!empty($classes[$class])) {
        include $classes[$class];
    };
}

spl_autoload_register('autoload_36a83fa87c775cda0788c61b384c4e25', true, true);
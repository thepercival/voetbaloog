<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 6-12-15
 * Time: 13:08
 */

use League\Tactician\Middleware;

class Voetbal_Command_Middleware_Transaction implements Middleware
{
    protected $db;

    /**
     * @var callable[]
     */
    private $queue = null;

    public function __construct( $db )
    {
        $this->db = $db;
    }

    public function execute($command, callable $next)
    {
        if ( $this->queue === null )
        {
            $this->queue = array();
            // echo "DB: transaction started".PHP_EOL."<br>";
            $this->db->beginTransaction();
        }
        $this->queue[] = $command;

        try {
            $returnValue = $next($command);
        }
        catch( Exception $e )
        {
        	// @TODO CHECK ROLLBACK ON MULTIPLE COMMANDS
            // echo "DB: transaction rollbacked, queue count = ". count( $this->queue ) . PHP_EOL."<br>";
	        // echo $e->getMessage();
            // $this->db->rollback();
            $this->queue = [];
            throw new Exception( $e->getMessage(), E_ERROR );
        }

        $command = array_shift( $this->queue );
        if ( count( $this->queue ) === 0 )
        {
            // echo "DB: transaction committed".PHP_EOL."<br>";
            $this->db->commit();
        }
        return $returnValue;
    }
}
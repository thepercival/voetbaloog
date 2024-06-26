<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Stopwatch.php 4558 2019-08-13 08:54:29Z thepercival $Rev
 *
 *
 * @package    Tools
 */

/**
 *
 * Details:
 *        m_bStarted            m_bStopped
 *	   ---------------------------------------
 *	 |        0          |          0          |    = Has not been used yet, or has been reset
 *	   ---------------------------------------
 *	 |        0          |          1          |    = Holding time, but paused
 *	   ---------------------------------------
 *	 |        1          |          0          |    = Currently running
 *	   ---------------------------------------
 *
 *	$sw = new RAD_Tools_Stopwatch();
 *	$sw->Start();
 *	$sw->Display();
 *	$sw->Stop();
 *	die();	
 *
 * @package    Tools
 */
class RAD_Tools_Stopwatch
{
	protected $m_nTimer;
	protected $m_bStarted;
	protected $m_bStopped; 


    public function __construct()
    {
        $this->m_bStarted = $this->m_bStopped = $this->m_nTimer = 0;
    }

    public function Start()
    {
        //leave the function because it is already running
        if( $this->m_bStarted ) 
        	return;
        if ( ! ( $this->m_bStopped ) ) 
        {
            $this->m_bStarted = true;
            $this->m_nTimer = microtime( true );
        }
        
        //stopwatch is currently m_bStopped, begin tracking time again
        if( $this->m_bStopped ) 
        {
            $this->m_nTimer = microtime(true) - $this->m_nTimer;
            $this->m_bStarted = true;
            $this->m_bStopped = false;
        }
    }

    public function Stop()
    {
        //make sure that it is running before you stop it
        if( !$this->m_bStopped and $this->m_bStarted ) 
        {
            $this->m_nTimer = microtime(true) - $this->m_nTimer;
            $this->m_bStopped = true;
            $this->m_bStarted = false;
        }
    }

    public function Reset()
    {
        $this->m_nTimer = $this->m_bStarted = $this->m_bStopped = false;
    }

    public function Display()
    {
        //still running, use current time.
        $nSec = null;
        if ( ! ( $this->m_bStopped ) and ( $this->m_bStarted ) ) 
        	$nSec = microtime( true) - $this->m_nTimer;
        else if ( !$this->m_bStopped and !$this->m_bStarted ) 
        	$nSec = 0;
        else 
        	$nSec = $this->m_nTimer;

        return $nSec;
    }
}

?>
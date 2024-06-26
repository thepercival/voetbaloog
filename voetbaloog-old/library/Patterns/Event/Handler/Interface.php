<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 26-10-16
 * Time: 19:58
 */

/**
 * @package    Patterns
 */
interface Patterns_Event_Handler_Interface
{
	/**
	 * @param mixed     $vtObject
	 *
	 * @return null
	 */
	public  function handle( $vtObject );
}
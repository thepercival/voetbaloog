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
interface Patterns_Event_Factory_Interface
{
	/**
	 * @param array                             $arrActions     the action
	 * @param Patterns_Event_Handler_Interface  $oEventHandler  the eventhandler
	 *
	 * @return null
	 */
	public static function addEventHandler( array $arrActions, Patterns_Event_Handler_Interface $oEventHandler );

	/**
	 * @param string    $sAction
	 * @param mixed     $vtObject
	 *
	 * @return null
	 */
	public static function handle( $sAction, $vtObject );
}
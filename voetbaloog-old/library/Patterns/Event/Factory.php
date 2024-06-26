<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 26-10-16
 * Time: 17:08
 */

/**
 * @package Patterns
 */
class Patterns_Event_Factory implements Patterns_Singleton_Interface, Patterns_Event_Factory_Interface
{
	protected static $m_oSingleton;
	protected static $m_arrEvents = array();

	/**
	 * A protected constructor; prevents direct creation of object
	 */
	protected function __construct(){}

	/**
	 * @see Patterns_Singleton_Interface::__clone()
	 */
	public function __clone()
	{
		throw new Exception("Cloning is not allowed.", E_ERROR );
	}

	/**
	 * @see Patterns_Singleton_Interface::getInstance()
	 */
	public static function getInstance()
	{
		if ( static::$m_oSingleton === null ) {
		    $calledClass = get_called_class();
			static::$m_oSingleton = new $calledClass();
        }
		return static::$m_oSingleton;
	}

	/**
	 * @see Patterns_Event_Factory_Interface::addEventHandler()
	 */
	public static function addEventHandler( array $arrActions, Patterns_Event_Handler_Interface $oEventHandler )
	{
		foreach( $arrActions as $sAction ) {
			if ( array_key_exists( $sAction, static::$m_arrEvents ) === false )
				static::$m_arrEvents[ $sAction ] = array();
			static::$m_arrEvents[ $sAction ][] = $oEventHandler;
		}
	}

	/**
	 * @see Patterns_Event_Factory_Interface::handle()
	 */
	public static function handle( $sAction, $vtObject )
	{
		if ( array_key_exists( $sAction, static::$m_arrEvents ) === false )
			return;

		foreach( static::$m_arrEvents[ $sAction ] as $oEventHandler )
			$oEventHandler->handle( $vtObject );
	}
}

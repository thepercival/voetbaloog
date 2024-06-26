<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Factory.php 4554 2019-08-12 14:37:34Z thepercival $
 * @since	  File available since Release 4.0
 * @package	Chart
 */

/**
 * @package	Chart
 */
class RAD_Chart_Factory implements RAD_Chart_Factory_Interface, Patterns_Singleton_Interface
{
	// Patterns_Singleton_Interface
	private static $m_objSingleton;

	protected function __construct(){}

	/**
	 * @see Patterns_Singleton_Interface::__clone()
	 */
	public function __clone()
	{
		trigger_error("Cloning is not allowed.", E_USER_ERROR);
	}

	/**
	 * @see Patterns_Singleton_Interface::getInstance()
	 */
	public static function getInstance()
	{
		if ( self::$m_objSingleton === null )
		{
			$MySelf = __CLASS__;
			self::$m_objSingleton = new $MySelf();
		}
		return self::$m_objSingleton;
	}

	/**
	 * @see RAD_Chart_Factory::createBar()
	 */
	public static function createBar()
	{
		return new RAD_Chart_Bar();
	}

	/**
	 * @see RAD_Chart_Factory::createColumn()
	 */
	public static function createColumn()
	{
		return new RAD_Chart_Column();
	}

	/**
	 * @see RAD_Chart_Factory::createPie()
	 */
	public static function createPie()
	{
		return new RAD_Chart_Pie();
	}
}
<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: JSON.php 3963 2013-10-15 11:47:21Z thepercival $
 *
 * @package    Object
 */

/**
 * @package Object
 */
abstract class JSON_Factory implements JSON_Factory_Interface, Patterns_Singleton_Interface
{
	protected $m_objPoolJSON;
	protected static $m_bPoolJSONEnabled = true;
    protected static $m_objSingleton;

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
		if ( static::$m_objSingleton === null )
		{
			$szCalledClassName = get_called_class();
			static::$m_objSingleton = new $szCalledClassName();
		}
		return static::$m_objSingleton;
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectsToJSON()
	 */
	public static function convertObjectsToJSON( $objObjects, $nDataFlag = null )
	{
		if ( $objObjects === null or $objObjects->count() === 0 )
			return "[]";

		$szJSON = null;
		foreach( $objObjects as $objObject )
		{
			if ( $szJSON === null )
				$szJSON = "[";
			else
				$szJSON .= ",";

			$szJSON .= static::convertObjectToJSON( $objObject, $nDataFlag );
		}
		$szJSON .= "]";
		return $szJSON;
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON()
	 */
	public static function convertObjectToJSON( $objObject, $nDataFlag = null )
	{
		throw new Exception( "Implement in child : ".get_called_class()."!", E_ERROR );
	}

	/**
	 * @see JSON_Factory_Interface::disableJSONPool()
	 */
	public static function disableJSONPool()
	{
		static::$m_bPoolJSONEnabled = false;
	}

	protected static function isInPoolJSON( $objObject )
	{
		if ( static::$m_bPoolJSONEnabled === false )
			return false;
		$objPoolJSON = static::getPoolJSON();
		return $objPoolJSON[ $objObject->getId()] !== null;
	}

	protected static function addToPoolJSON( $objObject )
	{
		$objPoolJSON = static::getPoolJSON()->add( $objObject );
	}

	protected static function getPoolJSON()
	{
		return static::getInstance()->getPoolJSONHelper();
	}

	protected function getPoolJSONHelper()
	{
		if ( $this->m_objPoolJSON === null )
			$this->m_objPoolJSON = Patterns_Factory::createCollection();
		return $this->m_objPoolJSON;
	}
}

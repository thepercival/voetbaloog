<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Factory.php 4558 2019-08-13 08:54:29Z thepercival $
 * @since	  File available since Release 4.0
 * @package	Object
 */

/**
 * @package Object
 */
abstract class Object_Factory implements Object_Factory_Interface, Patterns_Singleton_Interface
{
	protected $m_objPool;
	public static $m_bPoolEnabled = true;
	protected $m_szClassName;
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
	 * @see Object_Factory_Interface::createObject()
	 */
	public static function createObject()
	{
		$szClassName = static::getInstance()->getClassName();
		return new $szClassName();
	}

	/**
	 * @see Object_Factory_Interface::createObjectFromPool()
	 */
	public static function createObjectFromPool( $vtId )
	{
		$oInstance = static::getInstance();

		$bPoolEnabled = $oInstance->isPoolEnabled();
		$oPool = $oInstance->getPool();

		$oObject = null;
		if ( $bPoolEnabled === true and $vtId !== null )
			$oObject = $oPool[ $vtId ];

		if ( $oObject === null )
		{
			$oObject = static::createObject();
			$oObject->putId( $vtId );
			if ( $bPoolEnabled === true )
				$oPool->add( $oObject );
		}
		return $oObject;
	}

	/**
	 * @see Object_Factory_Interface::createObjects()
	 */
	public static function createObjects()
	{
		return Patterns_Factory::createCollection();
	}

	/**
	 * @see Object_Factory_Interface::isPoolEnabled()
	 */
	public static function isPoolEnabled()
	{
		return ( static::$m_bPoolEnabled === true );
	}

	/**
	 * @see Object_Factory_Interface::getPool()
	 */
	public static function getPool()
	{
		return static::getInstance()->getPoolHelper();
	}

	protected function getPoolHelper()
	{
		if ( $this->m_objPool === null )
			$this->m_objPool = static ::createObjects();
		return $this->m_objPool;
	}

	protected function getClassName()
	{
		if ( $this->m_szClassName === null )
		{
			$szClassName = get_class( $this );
			$this->m_szClassName = substr( $szClassName, 0, strpos( $szClassName, "_Factory" ) );
		}
		return $this->m_szClassName;
	}
}

<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: JSON.php 4220 2015-10-12 21:07:56Z thepercival $
 *
 * @package    Object
 */

/**
 * @package Object
 */
abstract class Object_Factory_JSON extends Object_Factory implements JSON_Factory_Interface
{
	protected $m_objPoolJSON;
	protected static $m_bPoolJSONEnabled = true;

	/**
	 * Call parent
	 */
    protected function __construct(){ parent::__construct(); }

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
	 * @see JSON_Factory_Interface::convertObjectsToJSON2()
	 */
	public static function convertObjectsToJSON2( $oObjects, $nDataFlag = null )
	{
		$arrObjects = array();

		if ( $oObjects === null or $oObjects->count() === 0 )
			return $arrObjects;


		foreach( $oObjects as $oObject )
		{
			$arrObject = static::convertObjectToJSON2( $oObject, $nDataFlag );
			if ( $arrObject !== null )
				$arrObjects[] = $arrObject;
		}

		return $arrObjects;
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON2()
	 */
	public static function convertObjectToJSON2( $objObject, $nDataFlag = null )
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

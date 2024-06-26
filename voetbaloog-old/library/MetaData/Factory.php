<?php

/**
 * MetaData_Factory.php
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Factory.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 *
 * @package    MetaData
 */

/**
 * Implements the interface MetaData_Factory_Interface.
 *
 * @package    MetaData
 */
class MetaData_Factory implements MetaData_Factory_Interface, Patterns_Singleton_Interface
{
    private static $m_objSingleton;

    protected function __construct()
    {

    }

     /**
	 * Defined by Patterns_Singleton_Interface; Prevent users to clone the instance
	 *
	 * @see Patterns_Singleton_Interface::__clone()
	 */
    public function __clone()
    {
        trigger_error("Cloning is not allowed.", E_USER_ERROR);
    }

    /**
	 * Defined by Patterns_Singleton_Interface; Prevent users to clone the instance
	 *
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
	 * Defined by MetaData_Factory_Interface; Creates a collection of ObjectProperties
	 *
	 * @see MetaData_Factory_Interface::createObjectPropertiesExt()
	 */
	public static function createObjectPropertiesExt( /* variable param list */ )
	{
		$objObjectProperties = Patterns_Factory::createCollection();
		$szClassName = func_get_arg( 0 );
		for ( $nI = 1 ; $nI < func_num_args() ; $nI++ )
		{
			$objObjectProperty = Patterns_Factory::createIdable( $szClassName."::".func_get_arg( $nI ) );
			$objObjectProperties->add( $objObjectProperty );
		}
		return $objObjectProperties;
	}

	/**
	 * @see MetaData_Factory_Interface::getValue()
	 */
	public static function getValue( $objObject, $szObjectProperty, $arrParams = array() )
	{
		$nStrPos = strpos ( $szObjectProperty, "::" );
		if ( $objObject !== null and $nStrPos !== false )
		{
			$szRealObjectProperty = self::getPropertyName( $szObjectProperty, false );

			if ( $szRealObjectProperty !== false )
			{
				$szClassName = self::getClassName( $szRealObjectProperty, false );

				if ( $szClassName !== false ) // Haal object op en roep dan getvalue aan
				{
					$objObjectTmp = null;
					if ( method_exists( $objObject, "get".$szClassName ) )
						$objObjectTmp = call_user_func( array(&$objObject, "get".$szClassName ) );

					return static::getValue( $objObjectTmp, $szRealObjectProperty, $arrParams );
				}
				else // haal object op
				{
					if ( method_exists( $objObject, "get".$szRealObjectProperty ) )
						return call_user_func_array( array(&$objObject, "get".$szRealObjectProperty ), $arrParams );
				}
			}
		}

		return null;
	}


	/**
	 * @see MetaData_Factory_Interface::putValue()
	 */
	public static function putValue( $objObject, $szObjectProperty, $vtValue )
	{
		$szObjectProperty = self::getPropertyName( $szObjectProperty );

		if ( method_exists( $objObject, "put".$szObjectProperty ) )
			return call_user_func_array( array(&$objObject, "put".$szObjectProperty ), array( $vtValue ) );
	}

	/**
	 * @see MetaData_Factory_Interface::toString()
	 */
	public static function toString( $vtVariant )
	{
		if ( $vtVariant !== null )
		{
			if ( $vtVariant instanceof Patterns_Idable_Interface )
				return $vtVariant->getId();
			elseif ( $vtVariant instanceof Patterns_Collection_Interface )
				return $vtVariant->toString();
		}
		return $vtVariant;
	}

	/**
	 * @see MetaData_Factory_Interface::toString()
	 */
	public static function getClassName( $szObjectProperty, $bThrowException = true )
	{
		$nStrPos = strpos ( $szObjectProperty, "::" );
		if ( $nStrPos === false )
		{
			if ( $bThrowException === false )
				return false;
			else
				throw new Exception("There is no :: in the objectproperty ".$szObjectProperty, E_ERROR );
		}
		return substr( $szObjectProperty, 0, $nStrPos );
	}

	/**
	 * @see MetaData_Factory_Interface::getPropertyName()
	 */
	public static function getPropertyName( $szObjectProperty, $bThrowException = true )
	{
		$nStrPos = strpos ( $szObjectProperty, "::" );
		if ( $nStrPos === false )
		{
			if ( $bThrowException === false )
				return false;
			else
				throw new Exception("There is no :: in the objectproperty ".$szObjectProperty, E_ERROR );
		}
		return substr( $szObjectProperty, $nStrPos + 2 );
	}
}
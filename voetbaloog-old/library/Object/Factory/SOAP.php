<?php
/**
 * @copyright  2007 Deltion
 * @license	http://www.deltion.nl/license/license.txt
 * @version	$Id: Factory.php 4234 2010-07-20 09:39:30Z cdunnink $
 * @link	   http://www.deltion.nl/RAPiD
 * @since	  File available since Release 4.0
 * @package	Object
 */

/**
 * @package Object
 */
abstract class Object_Factory_SOAP extends Object_Factory implements Object_Factory_SOAP_Interface, Object_Factory_SOAP_Ext_Interface
{
	/**
	 * A protected constructor; prevents direct creation of object
	 */
	protected function __construct(){
	    parent::__construct();
    }

	const CACHE_DEFAULT = 1;
	const CACHE_FALLBACK = 2;

    /**
     * @return mixed
     */
	abstract public static function createSOAPReader();

	/**
	 * returns the cache config init
	 */
	abstract protected static function getCacheConfigInit();

	protected function getClassName()
	{
		if ( $this->m_szClassName === null )
		{
			$szClassName = get_called_class();
			$this->m_szClassName = substr( $szClassName, 0, strpos( $szClassName, "_SOAP_Factory" ) );
		}
		return $this->m_szClassName;
	}

	public static function getCacheConfig( $sMethodName, $nCacheType )
	{
		$arrCacheConfig = static::getCacheConfigInit();
		if ( array_key_exists( $sMethodName, $arrCacheConfig ) === true
			and array_key_exists( $nCacheType - 1, $arrCacheConfig[ $sMethodName ] ) === true
		)
			return $arrCacheConfig[ $sMethodName ][ $nCacheType - 1];
		return -1;
	}

	/**
	 * @see Object_Factory_SOAP_Interface::createObjectsFromSOAP()
	 */
	public static function createObjectsFromSOAP( Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		$arrSOAPParams = Source_SOAP_Reader::convertParamsForSOAP( "createObjects", array( $oOptions ) );
		$arrSOAPParams = array_merge( array( static::getInstance()->getClassName() ), $arrSOAPParams );
		$sCacheId = Source_SOAP_Reader::getCacheFallBackId( "#createXmlObjects", $arrSOAPParams );
		$nCacheTime = static::getCacheConfig( "#createXmlObjects", Object_Factory_SOAP::CACHE_FALLBACK );
		$oCache = ZendExt_Cache::getCache( $nCacheTime, APPLICATION_PATH . "/cache/" );

		try
		{
			$oSOAPReader = static::createSOAPReader();
			$oObjects = $oSOAPReader->createObjects( $oOptions );

			if ( $nCacheTime > 0 )
			{
				$oCache->save( $oObjects, $sCacheId );
			}
		}
		catch( Exception $e )
		{
			$oObjects = $oCache->load( $sCacheId );
			if( $oObjects === false )
			{
				throw new Exception( $e->getMessage(), E_ERROR );
			}
		}
		return $oObjects;
	}

	/**
	 * @see Object_Factory_SOAP_Interface::createObjectFromSOAP()
	 */
	public static function createObjectFromSOAP( Construction_Option_Collection $oOptions = null )
	{
		$arrSOAPParams = Source_SOAP_Reader::convertParamsForSOAP( "createObject", array( $oOptions ) );
		$arrSOAPParams = array_merge( array( static::getInstance()->getClassName() ), $arrSOAPParams );
		$sCacheId = Source_SOAP_Reader::getCacheFallBackId( "#createXmlObject", $arrSOAPParams );
		$nCacheTime = static::getCacheConfig( "#createXmlObject", Object_Factory_SOAP::CACHE_FALLBACK );
		$oCache = ZendExt_Cache::getCache( $nCacheTime, APPLICATION_PATH . "/cache/" );

		try
		{
			$oSOAPReader = static::createSOAPReader();
			$oObject = $oSOAPReader->createObject( $oOptions );

			if ( $nCacheTime > 0 )
			{
				$oCache->save( $oObject, $sCacheId );
			}
		}
		catch( Exception $e )
		{
			$oObject = $oCache->load( $sCacheId );
			if( $oObject === false )
			{
				// var_dump( $e ); die();
				throw new Exception( $e->getMessage(), E_ERROR );
			}
		}
		return $oObject;
	}

	/**
	 * @see Object_Factory_SOAP_Ext_Interface::createObjectsFromSOAPExt()
	 */
	public static function createObjectsFromSOAPExt( $oObject, $oOptions = null, $sClassName = null ): Patterns_Collection
	{
		$arrSOAPParams = Source_SOAP_Reader::convertParamsForSOAP( "createObjectsExt", array( $oObject, $oOptions, $sClassName ) );
		$arrSOAPParams = array_merge( array( static::getInstance()->getClassName() ), $arrSOAPParams );

		$nCacheTime = static::getCacheConfig( "#createXmlObjectsExt", Object_Factory_SOAP::CACHE_FALLBACK );
		$sCacheId = Source_SOAP_Reader::getCacheFallBackId( "#createXmlObjectsExt", $arrSOAPParams );
		$oCache = ZendExt_Cache::getCache( $nCacheTime, APPLICATION_PATH . "/cache/" );

		try
		{
			$oSOAPReader = static::createSOAPReader();
			$oObjects = $oSOAPReader->createObjectsExt( $oObject, $oOptions, $sClassName );

			if ( $nCacheTime > 0 )
			{
				$oCache->save( $oObjects, $sCacheId );
			}
		}
		catch( Exception $e )
		{
			$oObjects = $oCache->load( $sCacheId );
			if( $oObjects === false )
			{
				throw new Exception( $e->getMessage(), E_ERROR );
			}
		}
		return $oObjects;
	}
}
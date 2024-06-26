<?php
/**
 * @copyright  2007 Deltion
 * @license	http://www.deltion.nl/license/license.txt
 * @version	$Id: Interface.php 2204 2009-02-20 08:08:12Z cdunnink $
 * @link	   http://www.deltion.nl/ROC
 * @since	  File available since Release 4.0
 * @package	Source
 */

/**
 * @package Source
 */
class Source_SOAP_Reader extends \SoapClient implements Source_Reader_Interface, Source_Reader_Ext_Interface
{
	protected $nTimeout =  null;
	protected $m_arrSOAPParams;
    protected $m_objFactory;

	public function __construct( $oFactory, $oConfig, array $arrSoapOptions = array() )
  	{
  		$this->m_objFactory = $oFactory;

  		if( intval( $oConfig->timeout ) > 0 )
			$this->nTimeout = intval( $oConfig->timeout );

		parent::__construct( $oConfig->url, $arrSoapOptions );
  	}

	public function __doRequest( $request, $location, $action, $version, $one_way = 0 )
	{
		$sThisMethodName = substr( $action, strpos( $action, "#" ) );
		// if ( APPLICATION_ENV === 'development' )
		// {
			// var_dump( get_class( $this->m_objFactory ) . " " . $sThisMethodName );
			// $writer = new Zend_Log_Writer_Stream( APPLICATION_PATH . DIRECTORY_SEPARATOR . 'rapidsoap.log');
			// $logger = new Zend_Log($writer);
			// $logger->log( get_class( $this->m_objFactory ) . " " . $sThisMethodName, Zend_Log::INFO);
		// }

		$nCacheTimeDefault = $this->m_objFactory->getCacheConfig( $sThisMethodName, Object_Factory_SOAP::CACHE_DEFAULT );
		$sCacheIdDefault = "roosterssoap_" . Object_Factory_SOAP::CACHE_DEFAULT . md5( $request );
		$oCacheDefault = ZendExt_Cache::getCache( $nCacheTimeDefault, APPLICATION_PATH . "/cache/" );

		$oResponse = null;
		$bFromCache = false;

		if ( $nCacheTimeDefault > 0 and APPLICATION_ENV === 'production' )
		{
			// $sw2 = null;
			// if ( APPLICATION_ENV === 'development' ) {
				// $sw2 = new RAD_Tools_Stopwatch();
				// $sw2->Start();
			// }

			$sResponse = $oCacheDefault->load( $sCacheIdDefault );
			if( $sResponse !== false )
			{
				$oResponse = unserialize( $sResponse );
				$bFromCache = true;

				// if ( APPLICATION_ENV === 'development' ) {
					// var_dump( "from cache in " . $sw2->Display() );
					// $sw2->Stop();
				// }
			}
		}

		if ( $oResponse === null and $bFromCache === false )
		{
			$oResponse = $this->doRequestHelper( $request, $location, $action, $version, $one_way, $sThisMethodName );

			if ( $nCacheTimeDefault > 0 )
			{
				$sResponse = serialize( $oResponse );

				if ( $nCacheTimeDefault > 0 )
				{
					$oCacheDefault->save( $sResponse, $sCacheIdDefault );
				}
			}
		}

		if( $one_way !== 0 )
			return $oResponse;
		return $oResponse;
	}

	protected function doRequestHelper( $request, $location, $action, $version, $one_way, $sThisMethodName )
	{
		// $sw = null;
		// if ( APPLICATION_ENV === 'development' ) {
			// $sw = new RAD_Tools_Stopwatch();
			// $sw->Start();
		// }

		$oCurl = curl_init( $location );
		curl_setopt( $oCurl, CURLOPT_VERBOSE, FALSE );
		curl_setopt( $oCurl, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $oCurl, CURLOPT_POST, TRUE );
		curl_setopt( $oCurl, CURLOPT_POSTFIELDS, $request );
		curl_setopt( $oCurl, CURLOPT_HEADER, FALSE );
		curl_setopt( $oCurl, CURLOPT_HTTPHEADER, array( "Content-Type: text/xml" ) );
		curl_setopt( $oCurl, CURLOPT_SSL_VERIFYPEER, FALSE );
		if( $this->nTimeout !== null )
			curl_setopt( $oCurl, CURLOPT_TIMEOUT, $this->nTimeout );

		//$s = new RAD_Tools_Stopwatch();
	   	//$s->Start();
		$oResponse = curl_exec( $oCurl );

		if( curl_errno( $oCurl ) !== 0 )
		{
			throw new Exception( "Curl error " . curl_error( $oCurl ) . " ." );
		}
		curl_close( $oCurl );

		// if ( APPLICATION_ENV === 'development' ) {
			// var_dump( $sw->Display() );
			// $sw->Stop();
		// }

		if( $one_way === 0 )
			return $oResponse;
	}

  	/**
	 * @see Source_Reader_Interface::getObjectPropertiesToRead()
	 */
	public function getObjectPropertiesToRead(): Patterns_Collection
	{
		throw new Exception( "This function is not implemented(".__FILE__.",".__LINE__.")", E_ERROR );
	}

	/**
  	 * @see Source_Reader_Interface::createObjects()
  	 */
	public function createObjects( Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		$sClassNameTmp = get_class( $this->m_objFactory );
		$arrSOAPParams = array( substr( $sClassNameTmp, 0, strpos( $sClassNameTmp, "_SOAP_Factory" ) ) );
		$arrSOAPParamsExt = static::convertParamsForSOAP( "createObjects", array( $oOptions ) );
		$this->m_arrSOAPParams = array_merge( $arrSOAPParams, $arrSOAPParamsExt );

		$sXml = $this->__soapCall( "createXmlObjects", $this->m_arrSOAPParams );

		return Source_XML_Reader::createObjectFromXMLString( $sXml );
	}

	/**
	 * @see Source_Reader_Interface::createObject()
	 */
	public function createObject( Construction_Option_Collection $oOptions = null )
	{
		$sClassNameTmp = get_class( $this->m_objFactory );
		$arrSOAPParams = array( substr( $sClassNameTmp, 0, strpos( $sClassNameTmp, "_SOAP_Factory" ) ) );
		$arrSOAPParamsExt = static::convertParamsForSOAP( "createObject", array( $oOptions ) );
		$this->m_arrSOAPParams = array_merge( $arrSOAPParams, $arrSOAPParamsExt );

		$sXml = $this->__soapCall( "createXmlObject", $this->m_arrSOAPParams );

		return Source_XML_Reader::createObjectFromXMLString( $sXml );
	}

	/**
	 * @see Source_Reader_Ext_Interface::createObjectsExt()
	 */
    public function createObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): Patterns_Collection
	{
		$sClassNameTmp = get_class( $this->m_objFactory );
		$arrSOAPParams = array( substr( $sClassNameTmp, 0, strpos( $sClassNameTmp, "_SOAP_Factory" ) ) );
		$arrSOAPParamsExt = static::convertParamsForSOAP( "createObjectsExt", array( $oObject, $oOptions, $sClassName ) );

		$this->m_arrSOAPParams = array_merge( $arrSOAPParams, $arrSOAPParamsExt );

		$sXml = $this->__soapCall( "createXmlObjectsExt", $this->m_arrSOAPParams );

		return Source_XML_Reader::createObjectFromXMLString( $sXml );
	}

	/**
	 * @see Source_SOAP_Reader_Interface::getCacheFallBackId()
	 */
	public static function getCacheFallBackId( $sMethodName, $arrSOAPParams )
	{
		return "roosterssoap_"
				. Object_Factory_SOAP::CACHE_FALLBACK
				. md5( $sMethodName . implode( ";", $arrSOAPParams ) );

	}

	/**
	 * @see Source_SOAP_Reader_Interface::convertParamsForSOAP()
	 */
	public static function convertParamsForSOAP( $sMethodName, $arrParams )
	{
		if ( $sMethodName === "createObjects" or $sMethodName === "createObject" )
		{
			$arrSOAPParams = array();

			$oOptions = $arrParams[0];

			$sOptions = null;
			if( $oOptions instanceof Construction_Option_Collection )
				$sOptions = Construction_Factory::convertObjectsToXML( $oOptions );
			else
				$sOptions = $oOptions;
			$arrSOAPParams[] = $sOptions;

			return $arrSOAPParams;
		}
		elseif ( $sMethodName === "createObjectsExt" )
		{
			$arrSOAPParams = array();

			$oObject = $arrParams[0];
			$oOptions = $arrParams[1];
			$sClassName =  $arrParams[2];

			$sObject = null;
			if( $oObject === null )
			{
				$sObject = "";
			}
			else if( $oObject instanceof Patterns_Collection )
			{
				$sObject = "<Collection>";
				foreach( $oObject as $oObjectIt )
				{
					$sFactory = get_class( $oObjectIt ) . "_Factory";
					$sObject .= $sFactory::convertObjectToXML( $oObjectIt );
				}
				$sObject .= "</Collection>";
			}
			else
			{
				$sFactory = get_class( $oObject ) . "_Factory";
				//if ( $sFactory instanceof XML_Factory_Interface)
				//{
					$sObject = $sFactory::convertObjectToXML( $oObject );
				/*}
				else
				{
					$sObject = "";
				}*/
			}
			$arrSOAPParams[] = $sObject;


			$sOptions = null;
			if( $oOptions instanceof Construction_Option_Collection )
				$sOptions = Construction_Factory::convertObjectsToXML( $oOptions );
			else
				$sOptions = $oOptions;
			$arrSOAPParams[] = $sOptions;

			$arrSOAPParams[] = $sClassName;

			return $arrSOAPParams;
		}
		throw new Exception( "Could not find method for converting!", E_ERROR );
	}
}
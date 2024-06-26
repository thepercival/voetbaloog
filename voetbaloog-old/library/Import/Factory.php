<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Factory.php 4559 2019-08-13 09:57:58Z thepercival $
 * @since	  File available since Release 4.0
 * @package	Import
 */

/**
 * @package	Import
 */
class Import_Factory implements Patterns_Singleton_Interface
{
	private static $m_objSingleton;
	public static $m_szExternPrefix = "extern-";  	// string

	protected function __construct()
	{
	}

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
	 * @param string $sExternId
	 * @return string
     * @see Import_Factory_Interface::getIdFromExternId()
     */
    public static function getIdFromExternId( $sExternId )
    {
        return substr ( $sExternId, strlen( static::$m_szExternPrefix ) );
    }

    /**
     * @see Import_Factory_Interface::convertProperties()
     */
    public static function convertProperties( &$arrObjects, $arrPropertiesToConvert )
    {
        foreach( $arrObjects as $oObject )
        {
            foreach($oObject as $vtKey => $vtValue) {
                if ( array_key_exists( $vtKey, $arrPropertiesToConvert ) ) {
                    $fncConvertProp = $arrPropertiesToConvert[$vtKey];
                    $fncConvertProp( $oObject, $vtValue );
                    unset( $oObject->$vtKey );
                }
                if ( is_array( $vtValue ) ) {
                    static::convertProperties( $vtValue, $arrPropertiesToConvert );
                }
                if ( is_object( $vtValue ) ) {
                    $arrTmp = array( $vtValue );
                    static::convertProperties( $arrTmp, $arrPropertiesToConvert );
                }
            }
        }
    }

}
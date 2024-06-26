<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Factory.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package Auth
 */
class RAD_Auth_Action_Factory extends Object_Factory_Db
{
	protected static $m_objSingleton;

    /**
	 * Call parent
	 */
    protected function __construct(){ parent::__construct(); }

	public static function createXMLReader( $objXML )
	{
		$objXMLReader = new RAD_Auth_Action_XML_Reader( self::getInstance() );
		$objXMLReader->putSource( $objXML );
		return $objXMLReader;
	}

	public static function createObjectsFromXML( $objXML, $oOptions = null )
	{
		$objReader = self::createXMLReader( $objXML );
		return $objReader->createObjects( $oOptions );
	}
}

<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 756 2014-03-02 08:13:49Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pool_Payment_Factory extends Object_Factory_Db_JSON implements VoetbalOog_Pool_Payment_Factory_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see VoetbalOog_Pool_Payment_Factory_Interface::createDefault()
	 */
	public static function createDefault( $oPool )
	{
		$oPayments = static::createObjects();

		$oPayment = static::createObject();
		$oPayment->putId("__NEW__1");
		$oPayment->putPool( $oPool );
		$oPayment->putPlace( 1 );
		$oPayment->putTimesStake( -1 );
		$oPayments->add( $oPayment );

		$oPayment = static::createObject();
		$oPayment->putId("__NEW__2");
		$oPayment->putPool( $oPool );
		$oPayment->putPlace( 2 );
		$oPayment->putTimesStake( 2 );
		$oPayments->add( $oPayment );

		$oPayment = static::createObject();
		$oPayment->putId("__NEW__3");
		$oPayment->putPool( $oPool );
		$oPayment->putPlace( 3 );
		$oPayment->putTimesStake( 1 );
		$oPayments->add( $oPayment );

		return $oPayments;
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON()
	 */
	public static function convertObjectToJSON( $oObject, $nDataFlag = null )
	{
		if ( $oObject === null )
			return "null";

		if ( static::isInPoolJSON( $oObject ) )
			return $oObject->getId();
		static::addToPoolJSON( $oObject );

		return
		"{".
			"\"Id\":".$oObject->getId().",".
			"\"Place\":".$oObject->getPlace().",".
			"\"TimesStake\":".$oObject->getTimesStake().
		"}";
	}
}
<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Association_Factory extends Object_Factory_Db_JSON
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON2()
	 */
	public static function convertObjectToJSON2( $oObject, $nDataFlag = null )
	{
		if ( $oObject === null )
			return null;

		if ( static::isInPoolJSON( $oObject ) )
			return $oObject->getId();
		static::addToPoolJSON( $oObject );

		$arrJSON = array(
			"id" => $oObject->getId(),
			"description" => $oObject->getDescription(),
			"name" => $oObject->getName()
		);
		if ( ( $nDataFlag & Voetbal_JSON::$nAssociation_Teams ) === Voetbal_JSON::$nAssociation_Teams )
			$arrJSON["teams"] = Voetbal_Team_Factory::convertObjectsToJSON2( $oObject->getTeams(), $nDataFlag );


		return $arrJSON;
	}
}
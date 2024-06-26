<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 740 2014-02-25 19:08:21Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Round_Factory extends Object_Factory_Db_JSON
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	public static function getDefaultNrOfPoules() {
		return array(
			2 => array( "nrofpoules" => 1 ), 3 => array( "nrofpoules" => 1 ),
			4 => array( "nrofpoules" => 1 ), 5 => array( "nrofpoules" => 1 ),
			6 => array( "nrofpoules" => 2 ), 7 => array( "nrofpoules" => 1 ),
			8 => array( "nrofpoules" => 2 ), 9 => array( "nrofpoules" => 2 ),
			10 => array( "nrofpoules" => 2 ), 11 => array( "nrofpoules" => 2 ),
			12 => array( "nrofpoules" => 3 ), 13 => array( "nrofpoules" => 3 ),
			14 => array( "nrofpoules" => 3 ), 15 => array( "nrofpoules" => 3 ),
			16 => array( "nrofpoules" => 4 ), 17 => array( "nrofpoules" => 4 ),
			18 => array( "nrofpoules" => 4 ), 19 => array( "nrofpoules" => 4 ),
			20 => array( "nrofpoules" => 4 ), 21 => array( "nrofpoules" => 5 ),
			22 => array( "nrofpoules" => 5 ), 23 => array( "nrofpoules" => 5 ),
			24 => array( "nrofpoules" => 5 ), 25 => array( "nrofpoules" => 5 ),
			26 => array( "nrofpoules" => 6 ), 27 => array( "nrofpoules" => 6 ),
			28 => array( "nrofpoules" => 7 ), 29 => array( "nrofpoules" => 6 ),
			30 => array( "nrofpoules" => 6 ), 31 => array( "nrofpoules" => 7 ),
			32 => array( "nrofpoules" => 8 )
		);
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

		$sJSON =
		"{".
			"\"Id\":".$oObject->getId().",".
			"\"Name\":\"".$oObject->getName()."\",".
			"\"Number\":".$oObject->getNumber().",".
			"\"Type\":".$oObject->getType().",".
			"\"SemiCompetition\":".( $oObject->getSemiCompetition() ? "true" : "false" ).",".
			"\"CompetitionSeason\":".Voetbal_CompetitionSeason_Factory::convertObjectToJSON( $oObject->getCompetitionSeason(), $nDataFlag )
		;

		if ( ( Voetbal_JSON::$nRound_Poules & $nDataFlag ) === Voetbal_JSON::$nRound_Poules )
			$sJSON .= ",\"Poules\":".Voetbal_Poule_Factory::convertObjectsToJSON( $oObject->getPoules(), $nDataFlag );

		$sJSON .= ",\"FromQualifyRules\" : ". Voetbal_QualifyRule_Factory::convertObjectsToJSON( $oObject->getFromQualifyRules(), $nDataFlag );
		$sJSON .= ",\"ToQualifyRules\" : ". Voetbal_QualifyRule_Factory::convertObjectsToJSON( $oObject->getToQualifyRules(), $nDataFlag );

		return $sJSON."}";
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON()
	 */
	public static function convertObjectToJSON2( $oObject, $nDataFlag = null )
	{
		if ( $oObject === null )
			return null;

		if ( static::isInPoolJSON( $oObject ) ) {
			// return $oObject->getId();
			return array(
				"cacheid" => $oObject->getId(),
				"class" => "round"
			);

		}
		static::addToPoolJSON( $oObject );

		$arrJSON = array(
			"class" => "round",
			"id" => $oObject->getId(),
			"name" => $oObject->getName(),
			"number" => $oObject->getNumber(),
			"type" => $oObject->getType(),
			"semicompetition" => $oObject->getSemiCompetition(),
			"competitionseason" => Voetbal_CompetitionSeason_Factory::convertObjectToJSON2( $oObject->getCompetitionSeason(), $nDataFlag ),
		);

		if ( ( Voetbal_JSON::$nRound_Poules & $nDataFlag ) === Voetbal_JSON::$nRound_Poules )
			$arrJSON["poules"] = Voetbal_Poule_Factory::convertObjectsToJSON2( $oObject->getPoules(), $nDataFlag );

		$arrJSON["fromqualifyrules"] = Voetbal_QualifyRule_Factory::convertObjectsToJSON2( $oObject->getFromQualifyRules(), $nDataFlag );
		$arrJSON["toqualifyrules"] = Voetbal_QualifyRule_Factory::convertObjectsToJSON2( $oObject->getToQualifyRules(), $nDataFlag );

		return $arrJSON;
	}
}
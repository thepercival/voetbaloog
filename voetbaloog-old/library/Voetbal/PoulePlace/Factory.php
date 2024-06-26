<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 772 2014-03-04 20:03:28Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_PoulePlace_Factory extends Object_Factory_Db_JSON implements Voetbal_PoulePlace_Factory_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see Voetbal_PoulePlace_Factory_Interface::createObjectsRanked()
	 */
	public static function createObjectsRanked()
	{
		return new Voetbal_PoulePlace_Collection();
	}

	/**
	 * @see Voetbal_PoulePlace_Factory_Interface::createObjectByExternTeamId()
	 */
	public static function createObjectByExternTeamId( $oCompetitionSeason, $sTeamExternId )
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter("Voetbal_Team::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix . $sTeamExternId );
		$oHomeTeam = Voetbal_Team_Factory::createObjectFromDatabase( $oOptions );

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter("Voetbal_PoulePlace::Team", "EqualTo", $oHomeTeam );
		$oOptions->addFilter("Voetbal_Round::Number", "EqualTo", 0 );
		$oOptions->addFilter("Voetbal_Round::CompetitionSeason", "EqualTo", $oCompetitionSeason );
		return Voetbal_PoulePlace_Factory::createObjectFromDatabase( $oOptions );
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
			"\"Number\":".$oObject->getNumber().",".
			// "\"Poule\":".Voetbal_Poule_Factory::convertObjectToJSON( $oObject->getPoule(), $nDataFlag ).",".
			"\"Poule\":".$oObject->getPoule()->getId().",".
			"\"Team\":".Voetbal_Team_Factory::convertObjectToJSON( $oObject->getTeam(), $nDataFlag ).",".
			"\"PenaltyPoints\":".$oObject->getPenaltyPoints().","
		;

		$sVal = null;
		if ( $oObject->getFromQualifyRule() === null )
			$sVal = "null";
		else {
			$sVal = Voetbal_QualifyRule_Factory::convertObjectToJSON( $oObject->getFromQualifyRule()->getQualifyRule(), $nDataFlag );
		}
		$sJSON .= "\"FromQualifyRule\":".$sVal.",";

		$sVal = null;
		if ( $oObject->getToQualifyRule() === null )
			$sVal = "null";
		else {
			$sVal = Voetbal_QualifyRule_Factory::convertObjectToJSON( $oObject->getToQualifyRule()->getQualifyRule(), $nDataFlag );
		}
		$sJSON .= "\"ToQualifyRule\":".$sVal;

		return $sJSON . "}";
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON2()
	 */
	public static function convertObjectToJSON2( $oObject, $nDataFlag = null )
	{
		if ( $oObject === null )
			return null;

		if ( static::isInPoolJSON( $oObject ) ) {
			// return $oObject->getId();
			return array(
				"cacheid" => $oObject->getId(),
				"class" => "pouleplace"
			);

		}
		static::addToPoolJSON( $oObject );

		$arrVal = array(
			"class" => "pouleplace",
			"id" => $oObject->getId(),
			"number" => $oObject->getNumber(),
			"poule" => Voetbal_Poule_Factory::convertObjectToJSON2( $oObject->getPoule(), $nDataFlag ),
			// "poule" => $oObject->getPoule()->getId(),
			"team" => Voetbal_Team_Factory::convertObjectToJSON2( $oObject->getTeam(), $nDataFlag ),
			"penaltypoints" => $oObject->getPenaltyPoints()
		);

		$vtVal = null;
		if ( $oObject->getFromQualifyRule() === null )
			$vtVal = null;
		else {
			$vtVal = Voetbal_QualifyRule_Factory::convertObjectToJSON2( $oObject->getFromQualifyRule()->getQualifyRule(), $nDataFlag );
		}
		$arrVal["fromqualifyrule"] = $vtVal;

		$vtVal = null;
		if ( $oObject->getToQualifyRule() === null )
			$vtVal = null;
		else {
			$vtVal = Voetbal_QualifyRule_Factory::convertObjectToJSON2( $oObject->getToQualifyRule()->getQualifyRule(), $nDataFlag );
		}
		$arrVal["toqualifyrule"] = $vtVal;

		return $arrVal;
	}
}
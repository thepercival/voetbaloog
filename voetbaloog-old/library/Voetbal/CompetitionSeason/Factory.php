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
class Voetbal_CompetitionSeason_Factory extends Object_Factory_Db_JSON implements Voetbal_CompetitionSeason_Factory_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see Voetbal_CompetitionSeason_Factory_Interface::createObjectsFromDatabaseCustom()
	 */
	public static function createObjectsFromDatabaseCustom( $bStarted, $bEnded, Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		return static::createDbReader()->createObjectsCustom( $bStarted, $bEnded, $oOptions );
	}

	/**
	 * @see Voetbal_CompetitionSeason_Factory_Interface::createObjectsFromDatabaseWithTeams()
	 */
	public static function createObjectsFromDatabaseWithTeams( Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		return static::createDbReader()->createObjectsWithTeams( $oOptions );
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
			"\"Abbreviation\":\"".$oObject->getAbbreviation()."\",".
			"\"Name\":\"".$oObject->getName()."\",".
			"\"Public\":".( $oObject->getPublic() ? "true" : "false" ).",".
			"\"PromotionRule\":".$oObject->getPromotionRule().",".
			"\"NrOfMinutesGame\":".$oObject->getNrOfMinutesGame().",".
			"\"NrOfMinutesExtraTime\":".$oObject->getNrOfMinutesExtraTime().",".
			"\"WinPointsAfterGame\":".$oObject->getWinPointsAfterGame().",".
			"\"WinPointsAfterExtraTime\":".$oObject->getWinPointsAfterExtraTime().",".
			"\"ImageName\":\"".$oObject->getImageName()."\"";

		if ( ( $nDataFlag & Voetbal_JSON::$nCompetitionSeason_Topscorers ) === Voetbal_JSON::$nCompetitionSeason_Topscorers )
			$sJSON .= ",\"Topscorers\":".Voetbal_Person_Factory::convertObjectsToJSON( $oObject->getTopscorers(), $nDataFlag );

		if ( ( $nDataFlag & Voetbal_JSON::$nCompetitionSeason_Rounds ) === Voetbal_JSON::$nCompetitionSeason_Rounds )
			$sJSON .= ",\"Rounds\":".Voetbal_Round_Factory::convertObjectsToJSON( $oObject->getRounds(), $nDataFlag );

		if ( ( $nDataFlag & Voetbal_JSON::$nCompetitionSeason_TeamsInTheRace ) === Voetbal_JSON::$nCompetitionSeason_TeamsInTheRace )
			$sJSON .= ",\"TeamsInTheRace\":".Voetbal_Team_Factory::convertObjectsToJSON( $oObject->getTeamsInTheRace(), $nDataFlag );

		return $sJSON."}";
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
				"class" => "competitionseason"
			);

		}
		static::addToPoolJSON( $oObject );

		$arrJSON = array(
			"class" => "competitionseason",
			"id" => $oObject->getId(),
			"abbreviation" => $oObject->getAbbreviation(),
			"name" => $oObject->getName(),
			"public" => $oObject->getPublic(),
			"promotionrule" => $oObject->getPromotionRule(),
			"nrofminutesgame" => $oObject->getNrOfMinutesGame(),
			"nrofminutesextratime" => $oObject->getNrOfMinutesExtraTime(),
			"winpointsaftergame" => $oObject->getWinPointsAfterGame(),
			"winpointsafterextratime" => $oObject->getWinPointsAfterExtraTime(),
			"imagename" => $oObject->getImageName(),
			"association" => Voetbal_Association_Factory::convertObjectToJSON2( $oObject->getAssociation(), $nDataFlag )
		);

		if ( ( $nDataFlag & Voetbal_JSON::$nCompetitionSeason_Topscorers ) === Voetbal_JSON::$nCompetitionSeason_Topscorers )
			$arrJSON["topscorers"] = Voetbal_Person_Factory::convertObjectsToJSON2( $oObject->getTopscorers(), $nDataFlag );

		if ( ( $nDataFlag & Voetbal_JSON::$nCompetitionSeason_Rounds ) === Voetbal_JSON::$nCompetitionSeason_Rounds )
			$arrJSON["rounds"] = Voetbal_Round_Factory::convertObjectsToJSON2( $oObject->getRounds(), $nDataFlag );

		if ( ( $nDataFlag & Voetbal_JSON::$nCompetitionSeason_TeamsInTheRace ) === Voetbal_JSON::$nCompetitionSeason_TeamsInTheRace )
			$arrJSON["teamsintherace"] = Voetbal_Team_Factory::convertObjectsToJSON2( $oObject->getTeamsInTheRace(), $nDataFlag );

		return $arrJSON;
	}
}
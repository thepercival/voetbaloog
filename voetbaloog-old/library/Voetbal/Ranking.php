<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Ranking.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Ranking implements Patterns_Singleton_Interface
{
    protected static $m_objSingleton;

	protected static $m_nPromotionRule;
	protected static $m_nGameStates = Voetbal_Factory::STATE_PLAYED;
	protected static $m_arrDefinitions = null;
	protected static $m_arrFunctions = null;
	public static $m_bSubtractPenaltyPoints = true;

	protected static $m_nPoulePlacesWithMostPoints = 1;
	protected static $m_nPoulePlacesWithFewestGamesPlayed = 2;
	protected static $m_nPoulePlacesWithBestGoalDifference = 3;
	protected static $m_nPoulePlacesWithMostGoalsScored = 4;
	protected static $m_nBestPoulePlacesAgainstEachOther = 5;

	CONST PROMOTION_RULE_WC = 1;
	CONST PROMOTION_RULE_EC = 2;

	/**
	 * A protected constructor; prevents direct creation of object
	 */
	protected function __construct(){}

	/**
	 * @see Patterns_Singleton_Interface::__clone()
	 */
	public function __clone()
	{
		throw new Exception('Cloning is not allowed.', E_ERROR );
	}

	/**
	 * @see Patterns_Singleton_Interface::getInstance()
	 */
	public static function getInstance()
	{
		if ( static::$m_objSingleton === null )
		{
			$sCalledClassName = get_called_class();
			static::$m_objSingleton = new $sCalledClassName();
		}
		return static::$m_objSingleton;
	}

	public static function putPromotionRule( $nPromotionRule )
	{
		static::$m_nPromotionRule = $nPromotionRule;
	}

	public static function putGameStates( $nGameStates )
	{
		static::$m_nGameStates = $nGameStates;
	}

	public static function updatePoulePlaceRankings( $oGames, $oPoulePlaces /* compare pouleplaces from different poules */ )
	{
		$oPoulePlacesToProcess = null;
		if ( $oPoulePlaces === null and $oGames !== null )
			$oPoulePlacesToProcess = static::getPoulePlaces( $oGames );
		else if ( $oPoulePlaces !== null) {
			$oPoulePlacesToProcess = Voetbal_PoulePlace_Factory::createObjects();
			$oPoulePlacesToProcess->addCollection( $oPoulePlaces );
		}

		$nRanking = 1;
		static::rankingHelper( $oPoulePlacesToProcess, $oGames, $nRanking );
	}

	protected static function getPoulePlaces( $oGames )
	{
		$oPoulePlaces = Voetbal_PoulePlace_Factory::createObjects();

		foreach( $oGames as $oGame )
		{
			$oPoulePlaces->add( $oGame->getHomePoulePlace() );
			$oPoulePlaces->add( $oGame->getAwayPoulePlace() );
		}
		return $oPoulePlaces;
	}

	public static function getPoulePlacesByRanking( $oGames, $oPoulePlaces /* compare pouleplaces from different poules */ )
	{
		if ( $oPoulePlaces === null and $oGames !== null )
			$oPoulePlaces = static::getPoulePlaces( $oGames );

		$oPoulePlacesRanked = Voetbal_PoulePlace_Factory::createObjectsRanked();

		$oPoulePlacesRanked->addCollection( $oPoulePlaces );
		$oPoulePlacesRanked->uasort(
			function ( $oPoulePlaceA, $oPoulePlaceB )
			{
				return ( $oPoulePlaceA->getRanking() < $oPoulePlaceB->getRanking() ? -1 : 1 );
			}
		);

		return $oPoulePlacesRanked;
	}

	protected static function rankingHelper( $oPoulePlaces, $oGames, $nRanking )
	{
		if ( $oPoulePlaces->count() < 1 )
			return;

		$oBestPoulePlaces = static::getBestPoulePlaces( $oPoulePlaces, $oGames );

		foreach ( $oBestPoulePlaces as $oPoulePlace )
		{
			$oPoulePlace->putRanking( $nRanking++ );
			$oPoulePlaces->remove( $oPoulePlace );
		}
		static::rankingHelper( $oPoulePlaces, $oGames, $nRanking );
	}

	public static function getBestPoulePlaces( $oPoulePlaces, $oGames = null, $bSkip = false )
	{
    $nrOfStartingPoulePlaces = count($oPoulePlaces);
		$arrFunctions = static::getPromotionRuleFunctions();
		$arrDefinitions = static::getDefinitions();
		foreach( $arrFunctions as $nFunction )
		{
			if ( $nFunction === static::$m_nBestPoulePlacesAgainstEachOther and ( $bSkip === true or $oGames === null ) ) {
				continue;
      }

			if ( $nFunction === static::$m_nBestPoulePlacesAgainstEachOther ) {
				static::$m_bSubtractPenaltyPoints = false;
        if( count($oPoulePlaces) === $nrOfStartingPoulePlaces) {
          continue;
        }
      }
			$fnFunction = $arrDefinitions[ $nFunction ];

			$oPoulePlaces = $fnFunction( $oPoulePlaces, $oGames );

			if ( $oPoulePlaces->count() < 2 ) {
        break;
      }
		}
		static::$m_bSubtractPenaltyPoints = true;
    if ( $oPoulePlaces->count() > 1 ) {
        static::doManualSorting($oPoulePlaces);
    }
		return $oPoulePlaces;
	}

  private static function doManualSorting($oPoulePlaces): void {
      $seasonName = $oPoulePlaces->first()->getPoule()->getRound()->getCompetitionSeason()->getSeason()->getName();
      if( $seasonName === "2024" and static::allFromSamePoule(2, $oPoulePlaces)) {
          $oPoulePlaces->uasort(
              function ( $oPoulePlaceA, $oPoulePlaceB )
              {
                  // Denmark before Slovenie, because less yellow cards
                  return ( $oPoulePlaceA->getTeam()->getName() < $oPoulePlaceB->getTeam()->getName() ? -1 : 1 );
              }
          );
      }
  }

  private static function allFromSamePoule(int $pouleNr, $oPoulePlaces): bool {
    foreach( $oPoulePlaces as $oPoulePlace) {
      if( $oPoulePlace->getPoule()->getNumber() !== $pouleNr) {
        return false;
      }
    }
    return true;
  }

	public static function getPromotionRuleDescriptions()
	{
		return Patterns_Factory::createValuables(
			static::PROMOTION_RULE_WC,
				"1. Meeste aantal punten in alle wedstrijden<br>".
				"2. Doelsaldo in alle wedstrijden<br>".
				"3. Aantal goals gemaakt in alle wedstrijden<br>".
				"4. Meeste aantal punten in onderlinge duels<br>".
				"5. Doelsaldo in onderlinge duels<br>".
				"6. Aantal goals gemaakt in onderlinge duels",
			static::PROMOTION_RULE_EC,
				"1. Meeste aantal punten in alle wedstrijden<br>".
				"2. Meeste aantal punten in onderlinge duels<br>".
				"3. Doelsaldo in onderlinge duels<br>".
				"4. Aantal goals gemaakt in onderlinge duels<br>".
				"5. Doelsaldo in alle wedstrijden<br>".
				"6. Aantal goals gemaakt in alle wedstrijden"
		);
	}

	protected static function getPromotionRuleFunctions()
	{
		if ( static::$m_arrFunctions === null )
		{
			static::$m_arrFunctions = array();

			static::$m_arrFunctions[ static::PROMOTION_RULE_WC ] = array();
			{
				static::$m_arrFunctions[ static::PROMOTION_RULE_WC ][] = static::$m_nPoulePlacesWithMostPoints;
				static::$m_arrFunctions[ static::PROMOTION_RULE_WC ][] = static::$m_nPoulePlacesWithFewestGamesPlayed;
				static::$m_arrFunctions[ static::PROMOTION_RULE_WC ][] = static::$m_nPoulePlacesWithBestGoalDifference;
				static::$m_arrFunctions[ static::PROMOTION_RULE_WC ][] = static::$m_nPoulePlacesWithMostGoalsScored;
				static::$m_arrFunctions[ static::PROMOTION_RULE_WC ][] = static::$m_nBestPoulePlacesAgainstEachOther;
			}

			static::$m_arrFunctions[ static::PROMOTION_RULE_EC ] = array();
			{
				static::$m_arrFunctions[ static::PROMOTION_RULE_EC ][] = static::$m_nPoulePlacesWithMostPoints;
				static::$m_arrFunctions[ static::PROMOTION_RULE_EC ][] = static::$m_nPoulePlacesWithFewestGamesPlayed;
				static::$m_arrFunctions[ static::PROMOTION_RULE_EC ][] = static::$m_nBestPoulePlacesAgainstEachOther;
				static::$m_arrFunctions[ static::PROMOTION_RULE_EC ][] = static::$m_nPoulePlacesWithBestGoalDifference;
				static::$m_arrFunctions[ static::PROMOTION_RULE_EC ][] = static::$m_nPoulePlacesWithMostGoalsScored;
			}
		}

		if ( array_key_exists( static::$m_nPromotionRule, static::$m_arrFunctions ) === false )
			throw new Exception( "Unknown qualifying rule", E_ERROR );

		return static::$m_arrFunctions[ static::$m_nPromotionRule ];
	}

	protected static function getDefinitions()
	{
		if ( static::$m_arrDefinitions === null )
		{
			static::$m_arrDefinitions = array();

			static::$m_arrDefinitions[ static::$m_nPoulePlacesWithMostPoints ] =
				function ( $oPoulePlaces, $oGames )
				{
					$nMostPoints = null;
					$oMostPointsPoulePlaces = Voetbal_PoulePlace_Factory::createObjects();
					foreach ( $oPoulePlaces as $oPoulePlace )
					{
						$nPoints = $oPoulePlace->getPoints( $oGames );
						if ( Voetbal_Ranking::$m_bSubtractPenaltyPoints == true )
							$nPoints -= $oPoulePlace->getPenaltyPoints();

						if ( $nMostPoints === null or $nPoints === $nMostPoints )
						{
							$nMostPoints = $nPoints;
							$oMostPointsPoulePlaces->add( $oPoulePlace );
						}
						elseif( $nPoints > $nMostPoints )
						{
							$nMostPoints = $nPoints;
							$oMostPointsPoulePlaces->flush();
							$oMostPointsPoulePlaces->add( $oPoulePlace );
						}
					}
					return $oMostPointsPoulePlaces;
				};
			static::$m_arrDefinitions[ static::$m_nPoulePlacesWithFewestGamesPlayed ] =
				function( $oPoulePlaces, $oGames )
				{
					$nFewestGamesPlayed = -1;
					$oFewestGamesPoulePlaces = Voetbal_PoulePlace_Factory::createObjects();
					foreach ( $oPoulePlaces as $oPoulePlace )
					{
						$nGamesPlayed = $oPoulePlace->getNrOfPlayedGames( $oGames );
						if ( $nFewestGamesPlayed === -1 or $nGamesPlayed === $nFewestGamesPlayed)
						{
							$nFewestGamesPlayed = $nGamesPlayed;
							$oFewestGamesPoulePlaces->add( $oPoulePlace );
						}
						elseif( $nGamesPlayed < $nFewestGamesPlayed )
						{
							$nFewestGamesPlayed = $nGamesPlayed;
							$oFewestGamesPoulePlaces->flush();
							$oFewestGamesPoulePlaces->add( $oPoulePlace );
						}
					}

					return $oFewestGamesPoulePlaces;
				};
			static::$m_arrDefinitions[ static::$m_nPoulePlacesWithBestGoalDifference ] =
				function( $oPoulePlaces, $oGames )
				{
					$nBestGoalDifference = null;
					$oBestGoalDifferencePoulePlaces = Voetbal_PoulePlace_Factory::createObjects();
					foreach ( $oPoulePlaces as $oPoulePlace )
					{
						$nGoalDifference = $oPoulePlace->getGoalDifference( $oGames );
						if ( $nBestGoalDifference === null )
						{
							$nBestGoalDifference = $nGoalDifference;
							$oBestGoalDifferencePoulePlaces->add( $oPoulePlace );
						}
						else
						{
							if ( $nGoalDifference === $nBestGoalDifference )
								$oBestGoalDifferencePoulePlaces->add( $oPoulePlace );
							elseif( $nGoalDifference > $nBestGoalDifference )
							{
								$nBestGoalDifference = $nGoalDifference;
								$oBestGoalDifferencePoulePlaces->flush();
								$oBestGoalDifferencePoulePlaces->add( $oPoulePlace );
							}
						}
					}

					return $oBestGoalDifferencePoulePlaces;
				};
			static::$m_arrDefinitions[ static::$m_nPoulePlacesWithMostGoalsScored ] =
				function( $oPoulePlaces, $oGames )
				{
					$nMostGoalsScored = 0;
					$oMostGoalsScoredPoulePlaces = Voetbal_PoulePlace_Factory::createObjects();
					foreach ( $oPoulePlaces as $sPoulePlaceId => $oPoulePlace )
					{
						$nGoalsScored = $oPoulePlace->getNrOfGoalsScored( $oGames );
						if ( $nGoalsScored === $nMostGoalsScored )
							$oMostGoalsScoredPoulePlaces->add( $oPoulePlace );
						elseif( $nGoalsScored > $nMostGoalsScored )
						{
							$nMostGoalsScored = $nGoalsScored;
							$oMostGoalsScoredPoulePlaces->flush();
							$oMostGoalsScoredPoulePlaces->add( $oPoulePlace );
						}
					}

					return $oMostGoalsScoredPoulePlaces;
				};
			static::$m_arrDefinitions[ static::$m_nBestPoulePlacesAgainstEachOther ] =
				function( $oPoulePlaces, $oGames )
				{
					$oGamesAgainstEachOther = Voetbal_Game_Factory::createObjects();
					{
						foreach ( $oGames as $oGame )
						{
							if ( ( $oGame->getState() & static::$m_nGameStates ) === $oGame->getState()
								and $oPoulePlaces[ $oGame->getHomePoulePlace()->getId() ] !== null
								and $oPoulePlaces[ $oGame->getAwayPoulePlace()->getId() ] !== null
							)
								$oGamesAgainstEachOther->add( $oGame );
						}
					}
					return Voetbal_Ranking::getBestPoulePlaces( $oPoulePlaces, $oGamesAgainstEachOther, true );
				};
		}
		return static::$m_arrDefinitions;
	}
}

?>

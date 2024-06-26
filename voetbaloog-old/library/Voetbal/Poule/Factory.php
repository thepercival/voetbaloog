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
class Voetbal_Poule_Factory extends Object_Factory_Db_JSON implements Voetbal_Poule_Factory_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see Voetbal_Poule_Factory_Interface::getNrOfGames()
	 */
	public static function getNrOfGames( $nNrOfPoulePlaces, $bSemiCompetition )
	{
		return static::getNrOfGameRounds( $nNrOfPoulePlaces, $bSemiCompetition ) * static::getNrOfGamesPerRound( $nNrOfPoulePlaces );
	}

    /**
     * @see Voetbal_Poule_Factory_Interface::getNrOfGamesPerRound()
     */
    public static function getNrOfGameRounds( $nNrOfPoulePlaces, $bSemiCompetition )
    {
        $nNrOfGameRounds = $nNrOfPoulePlaces;
        if( ( $nNrOfPoulePlaces % 2 ) === 0 ) {
            $nNrOfGameRounds--;
        }
        if ( $bSemiCompetition === false ){
            $nNrOfGameRounds *= 2;
        }
        return $nNrOfGameRounds;
    }

    /**
     * @see Voetbal_Poule_Factory_Interface::getNrOfGamesPerRound()
     */
    public static function getNrOfGamesPerRound( $nNrOfPoulePlaces )
    {
        if( ( $nNrOfPoulePlaces % 2 ) === 1 ) {
            $nNrOfPoulePlaces--;
        }
        return (int) ( $nNrOfPoulePlaces / 2 );
    }

	/**
	 * @see Voetbal_Poule_Factory_Interface::getPouleName()
	 */
	public static function getPouleName( $nPreviousNrOfGames, $nPouleNumber, $nNrOfPoules, $nNrOfTeams )
	{
		if ( ( $nNrOfTeams / $nNrOfPoules ) > 2.0 ) // count forwards
		{
			return "poule "	. chr( ord( "a" ) + $nPouleNumber );
		}
		else // count backwards
		{
			return "666";
		}
		throw new Exception("unknown poulename", E_ERROR );
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
            "\"Number\":".$oObject->getNumber().",".
			"\"Name\":\"".$oObject->getName()."\",".
			"\"Round\":".Voetbal_Round_Factory::convertObjectToJSON( $oObject->getRound(), $nDataFlag ).",".
			"\"Places\":".Voetbal_PoulePlace_Factory::convertObjectsToJSON( $oObject->getPlaces(), $nDataFlag ).",".
			"\"Games\":".Voetbal_Game_Factory::convertObjectsToJSON( $oObject->getGames(), $nDataFlag ).
		"}";
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
				"class" => "poule"
			);

		}
		static::addToPoolJSON( $oObject );

		return array(
			"class" => "poule",
			"id" => $oObject->getId(),
            "number" => $oObject->getNumber(),
			"name" => $oObject->getName(),
			"round" => Voetbal_Round_Factory::convertObjectToJSON2( $oObject->getRound(), $nDataFlag ),
			"places" => Voetbal_PoulePlace_Factory::convertObjectsToJSON2( $oObject->getPlaces(), $nDataFlag ),
			"games" => Voetbal_Game_Factory::convertObjectsToJSON2( $oObject->getGames( true ), $nDataFlag )
		);
	}
}
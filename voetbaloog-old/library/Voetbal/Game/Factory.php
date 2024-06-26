<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 994 2015-02-25 11:36:52Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Game_Factory extends Object_Factory_Db_JSON implements Voetbal_Game_Factory_Interface, Object_Factory_Db_Ext_Interface, Object_Factory_Db_Ext_Nr_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see Voetbal_Game_Factory_Interface::createObjectExt()
	 */
	public static function createObjectExt( Agenda_DateTime $oDateTime, Voetbal_PoulePlace $oHomePP, Voetbal_PoulePlace $oAwayPP, $sExternId = null, $nNumber = 0, $nViewOrder = 0 ): Voetbal_Game
	{
		$oGame = Voetbal_Game_Factory::createObject();

		$oGame->putId( $oHomePP->getId() . "-" .$oAwayPP->getId() );
		$oGame->putStartDateTime( $oDateTime );
		$oGame->putHomePoulePlace( $oHomePP );
		$oGame->putAwayPoulePlace( $oAwayPP );
		$oGame->putHomeGoals( -1 );
		$oGame->putAwayGoals( -1 );
		$oGame->putHomeGoalsExtraTime( -1 );
		$oGame->putAwayGoalsExtraTime( -1 );
		$oGame->putHomeGoalsPenalty( -1 );
		$oGame->putAwayGoalsPenalty( -1 );
		$oGame->putHomeNrOfCorners( -1 );
		$oGame->putAwayNrOfCorners( -1 );
		$oGame->putState( Voetbal_Factory::STATE_SCHEDULED );
		$oGame->putViewOrder( $nViewOrder );
		$oGame->putNumber( $nNumber );
		$oGame->putExternId( $sExternId );

		return $oGame;
	}

    /**
     * @param Voetbal_Extern_Game $oExternGame
     * @return Voetbal_Game|null
     */
    public static function createObjectFromDatabaseByExtern( Voetbal_Extern_Game $oExternGame  ): ?Voetbal_Game
    {
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter("Voetbal_Game::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix . $oExternGame->getId() );
        return static::createDbReader()->createObjects( $oOptions )->first();
    }

	/**
	 * @see Object_Factory_Db_Ext_Interface::createObjectsFromDatabaseExt()
	 */
    public static function createObjectsFromDatabaseExt( $oObject, Construction_Option_Collection $oOptions = null, string $sClassName = null ): Patterns_Collection
	{
		return static::createDbReader()->createObjectsExt( $oObject, $oOptions, $sClassName );
	}

	/**
	 * @see Object_Factory_Db_Ext_Nr_Interface::getNrOfObjectsFromDatabaseExt()
	 */
    public static function getNrOfObjectsFromDatabaseExt( $oObject, Construction_Option_Collection $oOptions = null, string $sClassName = null ): int
	{
		return static::createDbReader()->getNrOfObjectsExt( $oObject, $oOptions, $sClassName );
	}

	/**
	 * @see Voetbal_Game_Factory_Interface::createObjectFromDatabaseCustom()
	 */
	public static function createObjectFromDatabaseCustom( $oPoule, $nGameNumber, $oPlayer, $nState = null )
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_PoulePlace::Team", "EqualTo", $oPlayer->getProvider() );
		$oOptions->addFilter( "Voetbal_PoulePlace::Poule", "EqualTo", $oPoule );
		$oPoulePlace = Voetbal_PoulePlace_Factory::createObjectFromDatabase( $oOptions );

		$oOptions = Construction_Factory::createOptions();
		if ( $nState !== null ) {
			$oOptions->addFilter( "Voetbal_Game::State", "EqualTo", $nState );
		}
		$oOptions->addFilter( "Voetbal_Poule::Id", "EqualTo", $oPoule );
		$oOptions->addFilter( "Voetbal_Game::Number", "EqualTo", $nGameNumber );
		$oOptionsOr = Construction_Factory::createOptions();
		{
			$oOptionsHome = Construction_Factory::createOptions();
			{
				$oOptionsHome->putId("__HOME__");
				$oOptionsHome->addFilter( "Voetbal_Game::HomePoulePlace", "EqualTo", $oPoulePlace );
				$oOptionsOr->add( $oOptionsHome );
			}
			$oOptionsAway = Construction_Factory::createOptions();
			{
				$oOptionsAway->putId("__AWAY__");
				$oOptionsAway->addFilter( "Voetbal_Game::AwayPoulePlace", "EqualTo", $oPoulePlace );
				$oOptionsOr->add( $oOptionsAway );
			}
		}
		$oOptions->add( $oOptionsOr );
		return Voetbal_Game_Factory::createObjectFromDatabase( $oOptions );
	}

	/**
	 * @see Voetbal_Game_Factory_Interface::getNumberRange()
	 */
	public static function getNumberRange( $oPoule, $nState, $oStartDateTime = null, $oEndDateTime = null ): ?RAD_Range
	{
		return static::createDbReader()->getNumberRange( $oPoule, $nState, $oStartDateTime, $oEndDateTime );
	}

    /**
     * @see Voetbal_Game_Factory_Interface::getStateGameRounds()
     */
    public static function getStateGameRounds( Voetbal_Poule $oPoule )
    {
        return static::createDbReader()->getStateGameRounds( $oPoule );
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

		$sJsStartTimeStamp = "null";
		$oStartDateTime = $oObject->getStartDateTime();
		if ( $oStartDateTime !== null )
			$sJsStartTimeStamp = $oStartDateTime->getTimeStamp() * 1000;

		$sJSON =
		"{".
			"\"Id\":".$oObject->getId().",".
			"\"HomePoulePlace\":".Voetbal_PoulePlace_Factory::convertObjectToJSON( $oObject->getHomePoulePlace(), $nDataFlag ).",".
			"\"AwayPoulePlace\":".Voetbal_PoulePlace_Factory::convertObjectToJSON( $oObject->getAwayPoulePlace(), $nDataFlag ).",".
			"\"HomeGoals\":".$oObject->getHomeGoals().",".
			"\"AwayGoals\":".$oObject->getAwayGoals().",".
			"\"HomeGoalsExtraTime\":".$oObject->getHomeGoalsExtraTime().",".
			"\"AwayGoalsExtraTime\":".$oObject->getAwayGoalsExtraTime().",".
			"\"HomeGoalsPenalty\":".$oObject->getHomeGoalsPenalty().",".
			"\"AwayGoalsPenalty\":".$oObject->getAwayGoalsPenalty().",".
			"\"Location\":".Voetbal_Location_Factory::convertObjectToJSON( $oObject->getLocation(), $nDataFlag ).",".
			"\"State\":".$oObject->getState().",".
			"\"ViewOrder\":".$oObject->getViewOrder().",".
			"\"StartDateTime\":".$sJsStartTimeStamp

		;

		if ( ( $nDataFlag & Voetbal_JSON::$nGame_Participations ) === Voetbal_JSON::$nGame_Participations )
			$sJSON .= ",\"Participations\":".Voetbal_Game_Participation_Factory::convertObjectsToJSON( $oObject->getParticipations(), $nDataFlag );

		if ( ( $nDataFlag & Voetbal_JSON::$nGame_Goals ) === Voetbal_JSON::$nGame_Goals )
			$sJSON .= ",\"Goals\":".Voetbal_Goal_Factory::convertObjectsToJSON( $oObject->getGoals(), $nDataFlag );

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
				"class" => "game"
			);

		}
		static::addToPoolJSON( $oObject );

		return array(
			"class" => "game",
			"id" => $oObject->getId(),
			"homepouleplace" => Voetbal_PoulePlace_Factory::convertObjectToJSON2( $oObject->getHomePoulePlace(), $nDataFlag ),
			"awaypouleplace" => Voetbal_PoulePlace_Factory::convertObjectToJSON2( $oObject->getAwayPoulePlace(), $nDataFlag ),
			"homegoals" => $oObject->getHomeGoals(),
			"awaygoals" => $oObject->getAwayGoals(),
			"homegoalsextratime" => $oObject->getHomeGoalsExtraTime(),
			"awaygoalsextratime" => $oObject->getAwayGoalsExtraTime(),
			"homegoalspenalty" => $oObject->getHomeGoalsPenalty(),
			"awaygoalspenalty" => $oObject->getAwayGoalsPenalty(),
			"location" => Voetbal_Location_Factory::convertObjectToJSON2( $oObject->getLocation(), $nDataFlag ),
			"state" => $oObject->getState(),
			"vieworder" => $oObject->getViewOrder(),
			"startdatetime" => $oObject->getStartDateTime() !== null ? $oObject->getStartDateTime()->getTimeStamp() * 1000 : null,
			"valid" => $oObject->isValid(),
			"poule" => Voetbal_Poule_Factory::convertObjectToJSON2( $oObject->getPoule(), $nDataFlag )
		);
	}
}
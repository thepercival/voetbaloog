<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Bet_Factory extends Object_Factory_Db_JSON implements VoetbalOog_Bet_Factory_Interface, Object_Factory_Db_Ext_Nr_Interface
{
	protected static $m_objSingleton;
	protected static $m_sSessionUserId;
	protected static $m_oNow;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see VoetbalOog_Bet_Factory_Interface::createScore()
	 */
	public static function createScore()
	{
		return new VoetbalOog_Bet_Score();
	}

	/**
	 * @see VoetbalOog_Bet_Factory_Interface::createResult()
	 */
	public static function createResult()
	{
		return new VoetbalOog_Bet_Result();
	}

	/**
	 * @see VoetbalOog_Bet_Factory_Interface::createQualify()
	 */
	public static function createQualify()
	{
		return new VoetbalOog_Bet_Qualify();
	}

	/**
	 * @see Object_Factory_Interface::createObject()
	 */
	public static function createObject()
	{
		throw new Exception("Is abstract class",E_ERROR);
	}

	/**
	 * @see VoetbalOog_Bet_Factory_Interface::createObjectsForPoolUserFromDatabase()
	 */
	public static function createObjectsForPoolUserFromDatabase( $oPoolUser )
	{
		return static::createDbReader()->createObjectsForPoolUser( $oPoolUser );
	}

	/**
	 * @see VoetbalOog_Bet_Factory_Interface::getStreaksFromDatabase()
	 */
	public static function getStreaksFromDatabase( $nBetType, $bCorrect, $oOptions = null )
	{
		return static::createDbReader()->getStreaks( $nBetType, $bCorrect, $oOptions );
	}

	/**
	 * @see VoetbalOog_Bet_Factory_Interface::getQualifyingFromDatabase()
	 */
	public static function getQualifyingFromDatabase( $bCorrect, $oOptions = null )
	{
		return static::createDbReader()->getQualifying( $bCorrect, $oOptions );
	}

	/**
	 * @see VoetbalOog_Bet_Factory_Interface::getNrOfObjectsFromDatabaseExt()
	 */
    public static function getNrOfObjectsFromDatabaseExt( $oObject, Construction_Option_Collection $oOptions = null, string $sClassName = null ): int
	{
		return static::createDbReader()->getNrOfObjectsExt( $oObject, $oOptions, $sClassName );
	}

	/**
	 * @see VoetbalOog_Bet_Factory_Interface::getPoints()
	 */
	public static function getPoints( $oPoolUser, $oRound = null )
	{
		return static::createDbReader()->getPoints( $oPoolUser, $oRound );
	}

	/**
	 * @see VoetbalOog_Bet_Factory_Interface::getResult()
	 */
	public static function getResult( $nHomeGoals, $nAwayGoals )
	{
		return ( $nHomeGoals > $nAwayGoals ) ? 1 : ( ( $nHomeGoals < $nAwayGoals ) ? -1 : 0 );
	}

	/**
	* @see VoetbalOog_Bet_Factory_Interface::putSessionUser()
	*/
	public static function putSessionUser( $oUser )
	{
		if ( $oUser !== null )
			static::$m_sSessionUserId = $oUser->getId();
	}

	protected static function _getNow()
	{
		if ( static::$m_oNow === null )
			static::$m_oNow = Agenda_Factory::createDateTime();
		return static::$m_oNow;
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectsToJSON()
	 */
	public static function convertObjectsToJSON( $oObjects, $nDataFlag = null )
	{
		if ( $oObjects === null or $oObjects->count() === 0 )
			return "null";

		$sJSON = "[";
		foreach( $oObjects as $oObject )
		{
			if ( $oObject instanceof Patterns_Collection
				and $oObject instanceof Patterns_Idable_Interface
			)
			{
				if ( $sJSON !== "[" )
					$sJSON .= ",";
				$sJSON .= "{\"RoundBetConfigId\":".$oObject->getId().",";
				$sJSON .= "\"Bets\":".static::convertObjectsToJSON( $oObject, $nDataFlag );
				$sJSON .= "}";
			}
			else
			{
				if ( $sJSON !== "[" )
					$sJSON .= ",";

				$sJSON .= static::convertObjectToJSON( $oObject, $nDataFlag );
			}
		}
		$sJSON .= "]";
		return $sJSON;
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

		if ( static::$m_sSessionUserId !== $oObject->getPoolUser()->getUser()->getId()
			and static::_getNow() <= $oObject->getDeadLine()
		)
			return "null";

		$sJSON =
		"{".
			"\"Id\":".$oObject->getId().",".
			"\"PoolUser\":".VoetbalOog_Pool_User_Factory::convertObjectToJSON( $oObject->getPoolUser(), $nDataFlag ).",".
			"\"RoundBetConfig\":".VoetbalOog_Round_BetConfig_Factory::convertObjectToJSON( $oObject->getRoundBetConfig(), $nDataFlag ).",".
			"\"BetType\":".$oObject->getRoundBetConfig()->getBetType().",".
			"\"Correct\":".( $oObject->getCorrect() ? "true" : "false" ).",";

		if ( $oObject instanceof VoetbalOog_Bet_Qualify )
		{
			$sJSON .=
				"\"PoulePlace\":".Voetbal_PoulePlace_Factory::convertObjectToJSON( $oObject->getPoulePlace(), $nDataFlag ).",".
				"\"Team\":".Voetbal_Team_Factory::convertObjectToJSON( $oObject->getTeam(), $nDataFlag ).",".
				"\"IdExtra\":".$oObject->getPoulePlace()->getId();
		}
		else if ( $oObject instanceof VoetbalOog_Bet_Score )
		{
			$sJSON .=
				"\"Game\":".Voetbal_Game_Factory::convertObjectToJSON( $oObject->getGame(), $nDataFlag ).",".
				"\"HomeGoals\":".$oObject->getHomeGoals().",".
				"\"AwayGoals\":".$oObject->getAwayGoals().",".
				"\"IdExtra\":".$oObject->getGame()->getId();
		}
		else if ( $oObject instanceof VoetbalOog_Bet_Result )
		{
			$sJSON .=
				"\"Game\":".Voetbal_Game_Factory::convertObjectToJSON( $oObject->getGame(), $nDataFlag ).",".
				"\"Result\":".$oObject->getResult().",".
				"\"IdExtra\":".$oObject->getGame()->getId();
		}

		return $sJSON."}";
	}
}
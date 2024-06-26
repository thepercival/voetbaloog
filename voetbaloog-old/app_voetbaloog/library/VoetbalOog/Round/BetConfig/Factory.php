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
class VoetbalOog_Round_BetConfig_Factory extends Object_Factory_Db_JSON implements Object_Factory_Db_Ext_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see Object_Factory_Db_Interface::createObjectFromDatabase()
	 */
	public static function createObjectFromDatabase( $vtOptions = null )
	{
		$oObject = parent::createObjectFromDatabase( $vtOptions );
		if ( $oObject instanceof Patterns_ObservableObject_Collection_Idable )
			return $oObject->first();
		return $oObject;
	}

	/**
	 * @see Object_Factory_Db_Ext_Interface::createObjectsFromDatabaseExt()
	 */
    public static function createObjectsFromDatabaseExt( $oObject, Construction_Option_Collection $oOptions = null, string $sClassName = null ): Patterns_Collection
	{
		return static::createDbReader()->createObjectsExt( $oObject, $oOptions, $sClassName );
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
				$sJSON .= "{\"Id\":".$oObject->getId().",";
				$sJSON .= "\"BetConfigsTmp\":".static::convertObjectsToJSON( $oObject, $nDataFlag );
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

		$sJSON =
		"{".
			"\"Id\":".$oObject->getId().",".
			"\"Points\":".$oObject->getPoints().",".
			"\"BetType\":".$oObject->getBetType().",".
			"\"BetTime\":".$oObject->getBetTime().",".
			"\"Round\":".Voetbal_Round_Factory::convertObjectToJSON( $oObject->getRound() );
			//"\"Pool\":".VoetbalOog_Pool_Factory::convertObjectToJSON( $oObject->getPool() );

		if ( ( VoetbalOog_JSON::$nRoundBetConfig_SameTeams & $nDataFlag ) === VoetbalOog_JSON::$nRoundBetConfig_SameTeams ){
			$oSameTeams = VoetbalOog_Team_Factory::createSameObjectsFromDatabase( $oObject );
			$sJSON .= ",\"SameTeams\":".Voetbal_Team_Factory::convertObjectsToJSON( $oSameTeams, $nDataFlag );
		}

		return $sJSON."}";


	}
}
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
class VoetbalOog_Pool_Factory extends Object_Factory_Db_JSON implements VoetbalOog_Pool_Factory_Interface, Object_Factory_Db_Ext_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see Object_Factory_Db_Ext_Interface::createObjectsFromDatabaseExt()
	 */
    public static function createObjectsFromDatabaseExt( $oObject, Construction_Option_Collection $oOptions = null, string $sClassName = null ): Patterns_Collection
	{
		return static::createDbReader()->createObjectsExt( $oObject, $oOptions, $sClassName );
	}

	/**
	 * @see VoetbalOog_Pool_Factory_Interface::createObjectsWithSameRoundBetConfigFromDatabase()
	 */
	public static function createObjectsWithSameRoundBetConfigFromDatabase( $oPoolUser, $oOptions = null )
	{
		return static::createDbReader()->createObjectsWithSameRoundBetConfig( $oPoolUser, $oOptions );
	}

	/**
	* @VoetbalOog_Pool_Factory_Interface::createObjectsAvailable()
	*/
	public static function createObjectsAvailable( $oCompetitionSeason, $oUser )
	{
		if ( $oUser === null or $oCompetitionSeason === null )
			throw new Exception( "params cannot be null!", E_ERROR );

		// haal poolnamen op van vorige comepetitieseizoen waar de gebruiker aan heeft meegedaan
		$oObjects = VoetbalOog_Pool_Factory::createObjects();
		{
			// Haal alle poolnamen op van het compitieseizoen
			$arrNames = array();
			{
				$oOptions = Construction_Factory::createOptions();
				$oOptions->addFilter( "Voetbal_CompetitionSeason::Id", "EqualTo", $oCompetitionSeason );
				$oPoolsTmp = VoetbalOog_Pool_Factory::createObjectsFromDatabase( $oOptions );
				foreach( $oPoolsTmp as $oPoolTmp )
				{
					if ( array_key_exists( $oPoolTmp->getName(), $arrNames ) === false )
						$arrNames[ $oPoolTmp->getName() ] = true;
				}
			}

			$oOptions = Construction_Factory::createOptions();
			$oOptions->addFilter( "Voetbal_CompetitionSeason::Id", "NotEqualTo", $oCompetitionSeason );
			$oOptions->addOrder( "VoetbalOog_Pool::Name", false );
			$oOptions->addFilter( "VoetbalOog_Pool_User::User", "EqualTo", $oUser );
			$oObjectsTmp = static::createObjectsFromDatabaseExt( $oUser, $oOptions );

			foreach( $oObjectsTmp as $oObjectTmp )
			{
				if ( array_key_exists( $oObjectTmp->getName(), $arrNames ) === false )
				{
					$oObjects->add( $oObjectTmp );
					$arrNames[ $oObjectTmp->getName() ] = true;
				}
			}
		}
		return $oObjects;
	}

	/**
	* @see VoetbalOog_Pool_Db_Reader_Interface::isNameAvailable()
	*/
	public static function isNameAvailable( $oCompetitionSeason, $oUser, $sName )
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "VoetbalOog_Pool::CompetitionSeason", "EqualTo", $oCompetitionSeason );
		$oOptions->addFilter( "VoetbalOog_Pool::Name", "EqualTo", $sName );
		$oPools = static::createObjectsFromDatabase( $oOptions );
		if ( $oPools->count() > 0 )
			return false;

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter( "Voetbal_CompetitionSeason::Id", "NotEqualTo", $oCompetitionSeason );
		$oOptions->addFilter( "VoetbalOog_Pool::Name", "EqualTo", $sName );
		$oOptions->addFilter( "VoetbalOog_Pool_User::User", "NotEqualTo", $oUser );

		$oPoolsTmp = VoetbalOog_Pool_Factory::createObjectsFromDatabaseExt( $oUser, $oOptions );
		if ( $oPoolsTmp->count() > 0 )
			return false;

		return true;
	}

    /**
     * @see VoetbalOog_Pool_Factory_Interface::getAllTimeRanking()
     */
    public static function getAllTimeRanking( $oPool )
    {
        $oNow = Agenda_Factory::createDateTime();
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "Voetbal_Season::StartDateTime", "SmallerThan", $oNow );
        $oOptions->addFilter( "VoetbalOog_Pool::Name", "EqualTo", $oPool->getName() );
        $oOptions->addOrder( "Voetbal_Season::StartDateTime", false );
        $oPools = VoetbalOog_Pool_Factory::createObjectsFromDatabase( $oOptions );

        $oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjects();
        foreach ( $oPools as $oPoolIt ) {
            $oCompetitionSeasons->add( $oPoolIt->getCompetitionSeason() );
        }

        $arrAllTimeRankTotals = array();
        $arrAllTimeRankUsers = array();

        foreach ( $oPools as $oPoolIt )
        {
            $oCompetitionSeason = $oPoolIt->getCompetitionSeason();
            if ( $oCompetitionSeasons[ $oCompetitionSeason->getId() ] === null )
                continue;

            $oRankedPoolUsers = $oPoolIt->getUsers( true );
            $oPoolUsersPerRank = VoetbalOog_Pool_Factory::convertRankedObjects( $oRankedPoolUsers );

            $nPoolUsersToProcess = $oRankedPoolUsers->count();
            foreach ( $oPoolUsersPerRank as $nRank => $oPoolUsers )
            {
                $nPoints = $nPoolUsersToProcess - $oPoolUsers->count();
                foreach ( $oPoolUsers as $oPoolUser )
                {
                    $oUser = $oPoolUser->getUser();
                    if ( array_key_exists( $oUser->getId(), $arrAllTimeRankTotals ) === false )
                    {
                        $arrAllTimeRankTotals[$oUser->getId()] = array();
                        $arrAllTimeRankTotals[$oUser->getId()]["points"] = 0;
                        $arrAllTimeRankTotals[$oUser->getId()]["nrofwins"] = 0;
                        $arrAllTimeRankUsers[$oUser->getId()] = array();
                        $arrAllTimeRankUsers[$oUser->getId()]["name"] = $oUser->getName();
                    }
                    $arrAllTimeRankUsers[$oUser->getId()][$oCompetitionSeason->getId()] = array( "points" => $nPoints, "poolid" => $oPoolIt->getId(), "pooluserid" => $oPoolUser->getId() );

                    $arrAllTimeRankTotals[$oUser->getId()]["points"] += $nPoints;
                    if ( $nRank === 1 )
                        $arrAllTimeRankTotals[$oUser->getId()]["nrofwins"]++;

                    $nPoolUsersToProcess--;
                }
            }
        }

        arsort( $arrAllTimeRankTotals );

        return array( $oCompetitionSeasons, $arrAllTimeRankTotals, $arrAllTimeRankUsers );
    }

    /**
     * @see VoetbalOog_Pool_Factory_Interface::convertRankedObjects()
     */
    public static function convertRankedObjects( $oPoolUsers )
    {
        $oPoolUsersConverted = VoetbalOog_Pool_User_Factory::createObjects();
        foreach( $oPoolUsers as $oPoolUser )
        {
            $nRank = $oPoolUser->getRanking();
            if ( $oPoolUsersConverted[ $nRank ] === null ) {
                $oPoolUsersConverted->add( Patterns_Factory::createIdableCollection( Patterns_Factory::createIdable( $nRank ) ) );
            }
            $oPoolUsersConverted[ $nRank ]->add( $oPoolUser );
        }
        return $oPoolUsersConverted;
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
			"\"Stake\":".$oObject->getStake().",".
			"\"NrOfAvailableBets\":". json_encode( $oObject->getNrOfAvailableBets( null, true) );

		if ( ( $nDataFlag & VoetbalOog_JSON::$nPool_CompetitionSeason ) === VoetbalOog_JSON::$nPool_CompetitionSeason )
			$sJSON .= ",\"CompetitionSeason\":".Voetbal_CompetitionSeason_Factory::convertObjectToJSON( $oObject->getCompetitionSeason(), $nDataFlag );

		if ( ( $nDataFlag & VoetbalOog_JSON::$nPool_BetConfigs ) === VoetbalOog_JSON::$nPool_BetConfigs )
			$sJSON .= ",\"BetConfigs\":".VoetbalOog_Round_BetConfig_Factory::convertObjectsToJSON( $oObject->getBetConfigs(), $nDataFlag );

		if ( ( $nDataFlag & VoetbalOog_JSON::$nPool_Users ) === VoetbalOog_JSON::$nPool_Users )
			$sJSON .= ",\"Users\":".VoetbalOog_Pool_User_Factory::convertObjectsToJSON( $oObject->getUsers( true ), $nDataFlag );

		if ( ( $nDataFlag & VoetbalOog_JSON::$nPool_Payments ) === VoetbalOog_JSON::$nPool_Payments )
			$sJSON .= ",\"Payments\":".VoetbalOog_Pool_Payment_Factory::convertObjectsToJSON( $oObject->getPayments(), $nDataFlag );

		return $sJSON."}";
	}
}
<?php

/**                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                *
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 1202 2020-05-02 09:37:15Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Bet_Db_Reader extends Source_Db_Reader implements VoetbalOog_Bet_Db_Reader_Interface, Source_Reader_Ext_Nr_Interface
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );

		$this->addPersistance( VoetbalOog_Pool_User_Factory::createDbPersistance() );
		$this->addPersistance( VoetbalOog_Pool_Factory::createDbPersistance() );
		$this->addPersistance( VoetbalOog_Round_BetConfig_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Round_Factory::createDbPersistance() );
	}

	protected function getSelectFrom( $bCount = false )
	{
		$oSelect = parent::getSelectFrom( $bCount );

		$sTableRoundBetConfigs = VoetbalOog_Round_BetConfig_Db_Persistance::getTable()->getName();
		$sTableRounds = Voetbal_Round_Db_Persistance::getTable()->getName();
		$sTablePoolUsers = VoetbalOog_Pool_User_Db_Persistance::getTable()->getName();
		$sTablePools = VoetbalOog_Pool_Db_Persistance::getTable()->getName();

		$oSelect
			->joinLeft(array( $sTableRoundBetConfigs ), $this->getTableName().".RoundBetConfigId = ".$sTableRoundBetConfigs.".Id", array("BetType") )
			->joinLeft(array( $sTableRounds ), $sTableRoundBetConfigs.".RoundId = ".$sTableRounds.".Id", array() )
			->joinLeft(array( $sTablePoolUsers ), $this->getTableName().".UsersPerPoolId = ".$sTablePoolUsers.".Id", array() )
			->joinLeft(array( $sTablePools ), $sTablePoolUsers. ".PoolId = ".$sTablePools.".Id", array() )
		;
		return $oSelect;
	}

	/**
	 * @see Source_Reader_Ext_Nr_Interface::getNrOfObjectsExt()
	 */
    public function getNrOfObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): int
	{
		if ( $oObject === null )
			throw new Exception( "Object can not be null", E_ERROR );

		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		$oSelect = $this->getSelectFrom( true );

		if ( $oObject instanceof VoetbalOog_Pool_User )
		{
			$oOptions->addFilter( "VoetbalOog_Bet::PoolUser", "EqualTo", $oObject );

			// start : differs from parent
			$sTableRoundBetConfigs = VoetbalOog_Round_BetConfig_Db_Persistance::getTable()->getName();
			$oSelect->where(
				"NOT ( " . $sTableRoundBetConfigs . ".BetType = " . VoetbalOog_Bet_Result::$nId .
				" AND EXISTS( " .
				"	SELECT 	*" .
				"	FROM 	RoundBetConfigs RBCSUB" .
				" 	WHERE 	RBCSUB.PoolId = ".$sTableRoundBetConfigs.".PoolId".
				"	AND 	RBCSUB.RoundId = ".$sTableRoundBetConfigs.".RoundId".
				"	AND 	RBCSUB.BetType = " . VoetbalOog_Bet_Score::$nId .
				") )" );
			// end : differs from parent
		}
		else
		{
			$sDescription = ( $oObject !== null ? "Object of instance " . get_class( $oObject ) . " is not correct" : "Object is empty" );
			throw new Exception( $sDescription . " in " . __METHOD__, E_ERROR );
		}

		$this->addWhereOrderBy( $oSelect, $oOptions );

		// var_dump( $this->m_arrBindVars ); echo $oSelect; die();
		return $this->getNrOfObjectsHelper( $oSelect );
	}

	/**
	 * @see VoetbalOog_Bet_Db_Reader_Interface::createObjects()
	 */
	public function getStreaks( $nBetType, $bCorrect, $oOptions = null )
	{
		$this->addPersistance( Voetbal_Game_Factory::createDbPersistance() );

		$oOptions->addOrder("VoetbalOog_Pool_User::User", false );
		$oOptions->addOrder("Voetbal_Game::StartDateTime", false );
		$oOptions->addOrder("Voetbal_Game::ViewOrder", false );

		$oOptions->addFilter("Voetbal_Game::State", "EqualTo", Voetbal_Factory::STATE_PLAYED );
		$oOptions->addFilter("VoetbalOog_Round_BetConfig::BetType", "EqualTo", $nBetType );

		$sColumn =
		"(
			SELECT COUNT(*)
			FROM Bets BetsAlias JOIN UsersPerPool UPPAlias ON BetsAlias.UsersPerPoolId = UPPAlias.Id
								JOIN RoundBetConfigs RBCAlias ON BetsAlias.RoundBetConfigId = RBCAlias.Id
								LEFT JOIN Pools PAlias ON UPPAlias.PoolId = PAlias.Id
								INNER JOIN Games GamesAlias ON BetsAlias.GameId = GamesAlias.Id
			WHERE 	BetsAlias.Correct <> Bets.Correct
			AND		(
						GamesAlias.StartDateTime < Games.StartDateTime
						OR (GamesAlias.StartDateTime = Games.StartDateTime
							AND GamesAlias.ViewOrder < Games.ViewOrder
						)
					)
			AND		PAlias.Name = Pools.Name
			AND		UPPAlias.UserId = UsersPerPool.UserId
			AND 	GamesAlias.State = Games.State
			AND 	RBCAlias.BetType = RoundBetConfigs.BetType
 		) as RunGroup";

		$oSelect = $this->getQuery( $oOptions );
		$oSelect->reset( Zend_Db_Select::COLUMNS );
		$oSelect->columns( array( "Games.StartDateTime", "Bets.Correct", "UsersExt.Id", "UsersExt.LoginName", "Games.ViewOrder", $sColumn ) );
		$oSelect
			->join( array( "Games" => "Games"), "Bets.GameId = Games.Id", array() )
			//->join( array( "UPPAlias2" => "UsersPerPool"), "Bets.UsersPerPoolId = UPPAlias2.Id", array() )
			->join( array( "UsersExt" => "UsersExt"), "UsersPerPool.UserId = UsersExt.Id", array() )
		;

		$sSql =
			"SELECT Id
			,		LoginName
			,		Correct
			, 		MIN(StartDateTime) as StreakStartDateTime
			, 		MAX(StartDateTime) as StreakEndDateTime
			,		COUNT(*) AS NrOfGames
			FROM 	( ".$oSelect." ) A
			GROUP BY LoginName, Correct, RunGroup";

		$nCorrect = $bCorrect ? 1 : 0;
		$sSql =
			"SELECT	*
			FROM 	( ".$sSql." ) B
			WHERE	Correct = ".$nCorrect."
			ORDER BY NrOfGames DESC, StreakStartDateTime
			LIMIT 5";

		$oCollection = Patterns_Factory::createCollection();

		try
		{
			$stmt = $this->m_objDatabase->prepare( $sSql );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			$nI = 0;
			while ( $row = $stmt->fetch() )
			{
				$oTimeSlot = Agenda_Factory::createTimeSlot( $row["StreakStartDateTime"], $row["StreakEndDateTime"] );
				$vtValue = array( $row["Id"], $row["LoginName"], $row["NrOfGames"], $oTimeSlot );

				$oValuable = Patterns_Factory::createValuable( $nI++, $vtValue );

				$oCollection->add( $oValuable );
		 	}
		}
		catch ( Exception $e)
		{
			throw new Exception( $e->getMessage().", For Query: ". $sSql, E_ERROR );
		}
		return $oCollection;
	}

	/**
	 * @see VoetbalOog_Bet_Db_Reader_Interface::getQualifying()
	 */
	public function getQualifying( $bCorrect, $oOptions = null )
	{
		$this->addPersistance( Voetbal_Round_Factory::createDbPersistance() );

		$oOptions->addFilter("VoetbalOog_Round_BetConfig::BetType", "EqualTo", VoetbalOog_Bet_Qualify::$nId );

		$oSelect = $this->getQuery( $oOptions );
		$oSelect->reset( Zend_Db_Select::COLUMNS );
		$oSelect->columns( array(
								"MAX(RoundId) as RoundId",
								"UsersExt.Id as UserId",
								"MAX(LoginName) as LoginName",
								"SUM(Correct) as NrOfCorrect",
								"COUNT(".$this->getTableName().".Id) as NrOfBets" ) );
		$oSelect->group( array( "UsersPerPoolId", "RoundId") );
		$oSelect->join( array( "UsersExt" => "UsersExt"), "UsersPerPool.UserId = UsersExt.Id", array() );

		$sSortOrder = "ASC";
		$nNrOfPlaces = 4;
		if ( $bCorrect )
		{
			$sSortOrder = "DESC";
			$nNrOfPlaces = 	1;
		}

		$sSql =
			"SELECT 	*
			FROM 		( ".$oSelect." ) A
			WHERE		NrOfBets > ".$nNrOfPlaces."
			ORDER BY	NrOfBets DESC, NrOfCorrect ".$sSortOrder;

		$oAllValuables = Patterns_Factory::createCollection();

		try
		{
			$stmt = $this->m_objDatabase->prepare( $sSql );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			$nI = 0;
			$nPreviousNrOfBets = null;
			$oValuables = null;

			while ( $row = $stmt->fetch() )
			{
				$nNrOfBets = $row["NrOfBets"];
				if ( $nPreviousNrOfBets === null or $nPreviousNrOfBets <> $nNrOfBets )
				{
					$oValuables = new Patterns_Collection_Idable();
					$oValuables->putId( $nNrOfBets );
					$oAllValuables->add( $oValuables );
				}
				else if ( ( $nI % 5 ) === 0 )
					continue;

				$oRound = Voetbal_Round_Factory::createObjectFromDatabase( $row["RoundId"] );
				$vtValue = array( $row["UserId"], $row["LoginName"], $row["NrOfCorrect"], $oRound );

				$oValuable = Patterns_Factory::createValuable( $nI++, $vtValue );

				$oValuables->add( $oValuable );

				$nPreviousNrOfBets = $nNrOfBets;
		 	}
		}
		catch ( Exception $e )
		{
			throw new Exception( $e->getMessage().", For Query: ". $sSql, E_ERROR );
		}
		return $oAllValuables;
	}

	public function getPoints( $oPoolUser )
	{
		$arrPoints = array( 0 => 0 );

		$oCompetitionSeason = $oPoolUser->getPool()->getCompetitionSeason();

		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter("VoetbalOog_Bet::PoolUser", "EqualTo", $oPoolUser );
		$oOptions->addFilter("Voetbal_Round::CompetitionSeason", "EqualTo", $oCompetitionSeason );

		$oOptions->addFilter("VoetbalOog_Bet::Correct", "EqualTo", true );

		$oSelect = $this->getQuery( $oOptions );
		$oSelect->reset( Zend_Db_Select::COLUMNS );
		$oSelect->columns( array( "SUM( Points ) as Points", "RoundBetConfigs.RoundId" ) );
		$oSelect->group( array( "RoundBetConfigs.RoundId") );

		try
		{
			$stmt = $this->m_objDatabase->prepare( $oSelect );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			while ( $row = $stmt->fetch() ) {
				$arrPoints[0] += ((int) $row["Points"] );
				$arrPoints[ $row["RoundId"] ] = (int) $row["Points"];
			}
		}
		catch ( Exception $e )
		{
			throw new Exception( $e->getMessage().", For Query: ". $oSelect, E_ERROR );
		}
		return $arrPoints;
	}

	/**
	 * @see VoetbalOog_Bet_Db_Reader_Interface::createObjectsForPoolUser()
	 */
	public function createObjectsForPoolUser( $oPoolUser )
	{
		$oOptions = Construction_Factory::createOptions();
		$oOptions->addFilter("VoetbalOog_Bet::PoolUser", "EqualTo", $oPoolUser );
		$oOptions->addOrder( "VoetbalOog_Bet::RoundBetConfig", false );
		$oSelect = $this->getQuery( $oOptions );

		return $this->createObjectsHelperPerRoundBetConfig( $oSelect );
	}

	protected function createObjectsHelperPerRoundBetConfig( $oSelect )
	{
		$oObjects = $this->m_objFactory->createObjects();

		$oPool = $this->m_objFactory->getPool();

		try
		{
			$nPreviousRoundBetConfigId = null;
			$oRoundBetConfigBets = null;

			$stmt = $this->m_objDatabase->prepare( $oSelect );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			while ( $row = $stmt->fetch() )
			{
				$nRoundBetConfigId = $row[ "RoundBetConfigId" ];
				if ( $nRoundBetConfigId !== $nPreviousRoundBetConfigId )
				{
					$oRoundBetConfigBets = new Patterns_ObservableObject_Collection_Idable();
					$oRoundBetConfigBets->putId( $nRoundBetConfigId );
					$oObjects->add( $oRoundBetConfigBets );
				}

				$nId = $row["Id"];
				$oObject = $oPool[ $nId ];
				if ( $oObject === null )
				{
					$oObject = $this->createObjectFromRow( $row, null );
					$oPool->add( $oObject );
				}
		 		$oRoundBetConfigBets->add( $oObject );

		 		$nPreviousRoundBetConfigId = $nRoundBetConfigId;
			}
		}
		catch ( Exception $e)
		{
			throw new Exception( $e->getMessage().", For Query: ".$oSelect, E_ERROR );
		}

		return $oObjects;
	}

	/**
	 * @see Source_Db_Reader_Interface::createObjectFromRow()
	 */
	protected function createObjectFromRow( $row, $oObjectProperties )
	{
		$nBetType = $row["BetType"];
		$nId = $row["Id"];

		if ( $nBetType === VoetbalOog_Bet_Qualify::$nId )
			return $this->createQualify( $nId, $row );
		if ( $nBetType === VoetbalOog_Bet_Result::$nId )
			return $this->createResult( $nId, $row );
		if ( $nBetType === VoetbalOog_Bet_Score::$nId )
			return $this->createScore( $nId, $row );

		throw new Exception( "BetType unknown!", E_ERROR );
	}

	protected function createQualify( $nId, $row )
	{
		$oBet = VoetbalOog_Bet_Factory::createQualify();
		$oBet->putId( $nId );
		$oBet->putPoolUser( $row["UsersPerPoolId"] );
		$oBet->putCorrect( $row["Correct"] === 1 );
		$oBet->putRoundBetConfig( $row["RoundBetConfigId"] );
		$oBet->putPoulePlace( $row["PoulePlaceId"] );
		$oBet->putTeam( $row["TeamId"] );
		return $oBet;
	}

	protected function createResult( $nId, $row )
	{
		$oBet = VoetbalOog_Bet_Factory::createResult();
		$oBet->putId( $nId );
		$oBet->putPoolUser( $row["UsersPerPoolId"] );
		$oBet->putCorrect( $row["Correct"] === 1 );
		$oBet->putRoundBetConfig( $row["RoundBetConfigId"] );
		$oBet->putGame( $row["GameId"] );
		$oBet->putResult( $row["Result"] );
		return $oBet;
	}

	protected function createScore( $nId, $row )
	{
		$oBet = VoetbalOog_Bet_Factory::createScore();
		$oBet->putId( $nId );
		$oBet->putPoolUser( $row["UsersPerPoolId"] );
		$oBet->putCorrect( $row["Correct"] === 1 );
		$oBet->putRoundBetConfig( $row["RoundBetConfigId"] );
		$oBet->putGame( $row["GameId"] );
		$oBet->putHomeGoals( $row["HomeGoals"] );
		$oBet->putAwayGoals( $row["AwayGoals"] );
		return $oBet;
	}
}
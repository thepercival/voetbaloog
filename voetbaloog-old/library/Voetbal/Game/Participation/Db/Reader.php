<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 988 2015-01-21 19:33:58Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Game_Participation_Db_Reader extends Source_Db_Reader implements Voetbal_Game_Participation_Db_Reader_Interface, Source_Reader_Ext_Interface
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );

		$this->addPersistance( Voetbal_Team_Membership_Player_Factory::createDbPersistance() );
	}

	protected function getSelectFrom( $bCount = false )
	{
		$oSelect = parent::getSelectFrom( $bCount );
		$sTablePlayerPeriods = Voetbal_Team_Membership_Player_Db_Persistance::getTable()->getName();
		$oSelect->joinLeft(array( $sTablePlayerPeriods ), $this->getTableName() . ".PlayerPeriodId = ".$sTablePlayerPeriods.".Id", array() );
		return $oSelect;
	}

	/**
	 * @see Source_Db_Reader_Interface::getDetails()
	 */
	public function getDetails( Voetbal_Team_Membership_Player $oPlayerMembership, Construction_Option_Collection $oOptions ): Patterns_Collection
	{
		$nTeamId = $oPlayerMembership->getProvider()->getId();

		$arrColumns = array(
				"Games.Number AS GameNumber",
				"IF(HPP.TeamId = ".$nTeamId.", HomeGoals,AwayGoals) AS GoalsTeam",
				"IF(HPP.TeamId = ".$nTeamId.", AwayGoals,HomeGoals) AS GoalsAgainstTeam",
				$this->getTableName() . ".GameId",
				$this->getTableName(). ".YellowCardOne", $this->getTableName(). ".YellowCardTwo",
				$this->getTableName(). ".RedCard",
				"Goals.Minute", "Goals.Penalty", "Goals.OwnGoal"
		);

		$oSelect = $this->m_objDatabase->select();

		$this->addPersistance( Voetbal_PoulePlace_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Game_Factory::createDbPersistance() );

		$oOptions->addFilter( "Voetbal_Game::State", "EqualTo", Voetbal_Factory::STATE_PLAYED );
		$oOptions->addFilter( "Voetbal_Game::StartDateTime", "GreaterThan", $oPlayerMembership->getStartDateTime() );
		if ( $oPlayerMembership->getEndDateTime() !== null )
			$oOptions->addFilter( "Voetbal_Game::StartDateTime", "SmallerThan", $oPlayerMembership->getEndDateTime() );

		$sTableGoals = Voetbal_Goal_Db_Persistance::getTable()->getName();
		$sTableGames = Voetbal_Game_Db_Persistance::getTable()->getName();
		$sTablePoulePlaces = Voetbal_PoulePlace_Db_Persistance::getTable()->getName();

		$oSelect
			->from(array( $sTableGames ), array() )
			->joinLeft(array( $this->getTableName() => $this->getTableName() ), $sTableGames.".Id = ".$this->getTableName().".GameId and ".$this->getTableName().".PlayerPeriodId = ".$oPlayerMembership->getId(), array() )
			->joinLeft(array( $sTableGoals ), $sTableGoals.".PlayerPeriodsPerGameId = ".$this->getTableName().".Id", array() )
			->joinLeft(array( "HPP" => $sTablePoulePlaces ), $sTableGames.".HomePoulePlaceId = "."HPP".".Id", array() )
			->joinLeft(array( "APP" => $sTablePoulePlaces), $sTableGames.".AwayPoulePlaceId = "."APP".".Id", array() )
			->join(array( $sTablePoulePlaces ), $sTableGames.".HomePoulePlaceId = ".$sTablePoulePlaces.".Id", array() )
			->where("HPP.TeamId = ".$nTeamId." OR APP.TeamId = ".$nTeamId )
		;

		$oSelect->columns( $arrColumns );
		$oSelect->order( "GameNumber");

		$this->addWhereOrderBy( $oSelect, $oOptions );

		$oDetails = Patterns_Factory::createCollection();

		try
		{
			echo  "".$oSelect; var_dump( $this->m_arrBindVars ); die();
			$stmt = $this->m_objDatabase->prepare( $oSelect );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			while ( $row = $stmt->fetch() ) {
				// check if gamenumber exists in gamedetails, if so only add goal
				$oGameDetails = $oDetails[ $row["GameNumber"] ];

				if ( $oGameDetails === null )
				{
					$oIdable = Patterns_Factory::createIdable( $row["GameNumber"] );
					$oGameDetails = Patterns_Factory::createIdableCollection( $oIdable );
					$oDetails->add( $oGameDetails );

					$oGameDetails->add( Patterns_Factory::createValuable("state", ( $row["GameId"] !== null ? Voetbal_Factory::STATE_PLAYED : Voetbal_Factory::STATE_SCHEDULED ) ) );
					$oGameDetails->add( Patterns_Factory::createValuable("goalsteam", $row["GoalsTeam"] ) );
					$oGameDetails->add( Patterns_Factory::createValuable("goalsagainstteam", $row["GoalsAgainstTeam"] ) );
					$oGameDetails->add( Patterns_Factory::createValuable("yellowcardone", $row["YellowCardOne"] ) );
					$oGameDetails->add( Patterns_Factory::createValuable("yellowcardtwo", $row["YellowCardTwo"] ) );
					$oGameDetails->add( Patterns_Factory::createValuable("redcard", $row["RedCard"] ) );
					$oGameDetails->add( Patterns_Factory::createValuable("goals", array() ) );
				}
				if ( $row["Minute"] !== null ) {
					$arrGoals = $oGameDetails["goals"]->getValue();
					$arrGoals[] = array( "minute" => $row["Minute"], "penalty" => $row["Penalty"], "own" => $row["OwnGoal"] );
					$oGameDetails["goals"]->putValue( $arrGoals );
				}
			}
		}
		catch ( Exception $e )
		{
			throw new Exception( $e->getMessage().", For Query: ". $oSelect, E_ERROR );
		}
		return $oDetails;
	}

	/**
	 * @see Source_Db_Reader_Interface::getDetails()
	 */
	public function getDetailsTotals( Voetbal_Team_Membership_Player $oPlayerMembership, Construction_Option_Collection $oOptions ): Patterns_Collection
	{
		$nTeamId = $oPlayerMembership->getProvider()->getId();

		$arrColumns = array(
				"Games.Number AS GameNumber",
                "Games.StartDateTime AS StartDateTime",
				"IF(HPP.TeamId = ".$nTeamId.", HomeGoals,AwayGoals) AS GoalsTeam",
				"IF(HPP.TeamId = ".$nTeamId.", AwayGoals,HomeGoals) AS GoalsAgainstTeam",
				"IF( ".$this->getTableName().".GameId IS NULL, 0, ".Voetbal_Factory::STATE_PLAYED." ) AS State",
				"IFNULL( ( SELECT SUM( IF( `Minute` > 0, 1, 0 ) ) FROM Goals WHERE PlayerPeriodsPerGameId = ".$this->getTableName().".Id ), 0 ) AS Goals",
				"IFNULL( ( SELECT SUM( IF( `Penalty` > 0, 1, 0 ) ) FROM Goals WHERE PlayerPeriodsPerGameId = ".$this->getTableName().".Id ), 0 ) AS Penalties",
				"IFNULL( ( SELECT SUM( IF( `OwnGoal` > 0, 1, 0 ) ) FROM Goals WHERE PlayerPeriodsPerGameId = ".$this->getTableName().".Id ), 0 ) AS OwnGoals",
                "IFNULL( ( SELECT SUM( IF( `Minute` > 0, 1, 0 ) ) FROM Goals WHERE AssistPlayerPeriodsPerGameId = ".$this->getTableName().".Id ), 0 ) AS Assists",
				"IF( YellowCardOne > 0, IF( YellowCardTwo > 0, 2, 1 ), 0 ) AS YellowCards",
				"IF( RedCard > 0, 1, 0 ) AS RedCard"
		);

		$oSelect = $this->m_objDatabase->select();

		$this->addPersistance( Voetbal_PoulePlace_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Game_Factory::createDbPersistance() );

		$oOptions->addFilter( "Voetbal_Game::State", "EqualTo", Voetbal_Factory::STATE_PLAYED );
		$oOptions->addFilter( "Voetbal_Game::StartDateTime", "GreaterThan", $oPlayerMembership->getStartDateTime() );
		if ( $oPlayerMembership->getEndDateTime() !== null )
			$oOptions->addFilter( "Voetbal_Game::StartDateTime", "SmallerThan", $oPlayerMembership->getEndDateTime() );

		$sTableGames = Voetbal_Game_Db_Persistance::getTable()->getName();
		$sTablePoulePlaces = Voetbal_PoulePlace_Db_Persistance::getTable()->getName();

		$oSelect
			->from(array( $sTableGames ), array() )
			->joinLeft(array( $this->getTableName() => $this->getTableName() ), $sTableGames.".Id = ".$this->getTableName().".GameId and ".$this->getTableName().".PlayerPeriodId = ".$oPlayerMembership->getId(), array() )
			->joinLeft(array( "HPP" => $sTablePoulePlaces ), $sTableGames.".HomePoulePlaceId = "."HPP".".Id", array() )
			->joinLeft(array( "APP" => $sTablePoulePlaces), $sTableGames.".AwayPoulePlaceId = "."APP".".Id", array() )
			->join(array( $sTablePoulePlaces ), $sTableGames.".HomePoulePlaceId = ".$sTablePoulePlaces.".Id", array() )
			->where("HPP.TeamId = ".$nTeamId." OR APP.TeamId = ".$nTeamId )
		;

		$oSelect->columns( $arrColumns );
		$oSelect->order( "GameNumber");

		$this->addWhereOrderBy( $oSelect, $oOptions );

		$oDetails = Patterns_Factory::createCollection();

		try
		{
			// echo  "".$oSelect; var_dump( $this->m_arrBindVars ); die();
			$stmt = $this->m_objDatabase->prepare( $oSelect );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			while ( $row = $stmt->fetch() ) {
				$row["Playerid"] = $oPlayerMembership->getId();
                $row["StartDateTime"] = Agenda_Factory::createDateTime($row["StartDateTime"]);
				$oDetails->add( Patterns_Factory::createValuable( $row["GameNumber"], $row ) );
			}
		}
		catch ( Exception $e )
		{
			throw new Exception( $e->getMessage().", For Query: ". $oSelect, E_ERROR );
		}
		return $oDetails;
	}

	/**
	 * @see Source_Reader_Ext_Interface::createObjectsExt()
	 */
    public function createObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): Patterns_Collection
	{
		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		$oSelect = $this->getSelectFrom();

		if ( $oObject instanceof Voetbal_Poule or $sClassName === "Voetbal_Poule" )
		{
			$this->addPersistance( Voetbal_Poule_Factory::createDbPersistance() );
			$this->addPersistance( Voetbal_Game_Factory::createDbPersistance() );

			$sTableGames = Voetbal_Game_Db_Persistance::getTable()->getName();
			$sTablePoulePlaces = Voetbal_PoulePlace_Db_Persistance::getTable()->getName();
			$sTablePoules = Voetbal_Poule_Db_Persistance::getTable()->getName();
			$sTableRounds = Voetbal_Round_Db_Persistance::getTable()->getName();
			$sTableCompSeasons = Voetbal_CompetitionSeason_Db_Persistance::getTable()->getName();

			if ( $oObject !== null )
				$oOptions->addFilter( "Voetbal_Poule::Id", "EqualTo", $oObject );

			$oSelect
				->join(array($sTableGames), $sTableGames.".Id = ".$this->getTableName().".GameId", array() )
				->joinLeft(array("HomePoulePlaces" => $sTablePoulePlaces), $sTableGames.".HomePoulePlaceId = HomePoulePlaces.Id", array() )
				->joinLeft(array("AwayPoulePlaces" => $sTablePoulePlaces), $sTableGames.".AwayPoulePlaceId = AwayPoulePlaces.Id", array() )
				->joinLeft(array( $sTablePoules ), "HomePoulePlaces.PouleId = Poules.Id", array() )
				->joinLeft(array( $sTableRounds ), $sTablePoules.".RoundId = ".$sTableRounds.".Id", array() )
				->joinLeft(array( $sTableCompSeasons ), $sTableRounds.".CompetitionsPerSeasonId = ".$sTableCompSeasons.".Id", array() )
			;
		}
		else
			throw new Exception( "No classname set!", E_ERROR );

		$this->addWhereOrderBy( $oSelect, $oOptions );
		// echo  "".$oSelect; var_dump( $this->m_arrBindVars ); die();
		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}
}
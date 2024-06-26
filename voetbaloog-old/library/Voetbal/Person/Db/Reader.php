<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 985 2015-01-16 16:27:04Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Person_Db_Reader extends Source_Db_Reader implements Voetbal_Person_Db_Reader_Interface, Source_Reader_Ext_Interface
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );
	}

	/**
	 * @see Source_Reader_Ext_Interface::createObjectsExt()
	 */
    public function createObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): Patterns_Collection
	{
		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		$oSelect = $this->m_objDatabase->select();

		if ( $oObject instanceof Voetbal_Team or $sClassName === "Voetbal_Team" )
		{
			$this->addPersistance( Voetbal_Team_Membership_Player_Factory::createDbPersistance() );

			$oOptions->addFilter( "Voetbal_Team_Membership_Player::Provider", "EqualTo", $oObject );

			$sTablePlayerPeriods = Voetbal_Team_Membership_Player_Db_Persistance::getTable()->getName();

			$oSelect->distinct()
			->from(array( $this->getTableName() ) )
			->join(array( $sTablePlayerPeriods ), $this->getTableName().".Id = ".$sTablePlayerPeriods.".PersonId", array() )
			;
		}
		else if ( $oObject instanceof Voetbal_Team_Membership_Player or $sClassName === "Voetbal_Team_Membership_Player" )
		{
            $this->addPersistance( Voetbal_Team_Membership_Player_Factory::createDbPersistance() );
            $this->addPersistance( Voetbal_Team_Factory::createDbPersistance() );

            if ( $oObject !== null )
                $oOptions->addFilter( "Voetbal_Team_Membership_Player::Id", "EqualTo", $oObject );

            $sTablePlayerPeriods = Voetbal_Team_Membership_Player_Db_Persistance::getTable()->getName();
            $sTableTeams = Voetbal_Team_Db_Persistance::getTable()->getName();

            $oSelect->distinct()
                ->from(array( $this->getTableName() ) )
                ->join(array( $sTablePlayerPeriods ), $this->getTableName().".Id = ".$sTablePlayerPeriods.".PersonId", array() )
                ->join(array( $sTableTeams ), $sTableTeams.".Id = ".$sTablePlayerPeriods.".TeamId", array() )
            ;
		}
        else if ( $oObject instanceof Voetbal_Team_Membership or $sClassName === "Voetbal_Team_Membership" )
        {
            $sTablePlayerPeriods = Voetbal_Team_Membership_Player_Db_Persistance::getTable()->getName();

            $oSelect->distinct()
                ->from(array( $this->getTableName() ) )
                ->where( "Id NOT IN ( SELECT PersonId FROM ". $sTablePlayerPeriods." WHERE PersonId = ".$this->getTableName().".Id )" )
            ;
        }
		else
			throw new Exception( "No classname set!", E_ERROR );

		$this->addWhereOrderBy( $oSelect, $oOptions );

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}

	/**
	 * @see Voetbal_Person_Db_Reader_Interface::getTopscorers()
	 */
	public function getTopscorers( Construction_Option_Collection $oOptions = null )
	{
		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		$this->addPersistance( Voetbal_Game_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Goal_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_CompetitionSeason_Factory::createDbPersistance() );

		$oOptions->addFilter("Voetbal_Game::State", "EqualTo", Voetbal_Factory::STATE_PLAYED );
		$oOptions->addFilter("Voetbal_Goal::OwnGoal", "EqualTo", false );

		$sTableGoals = Voetbal_Goal_Db_Persistance::getTable()->getName();
        $sTableGameParticipations = Voetbal_Game_Participation_Db_Persistance::getTable()->getName();
		$sTablePlayerPeriods = Voetbal_Team_Membership_Player_Db_Persistance::getTable()->getName();
		$sTableGames = Voetbal_Game_Db_Persistance::getTable()->getName();
		$sTablePersons = Voetbal_Person_Db_Persistance::getTable()->getName();
		$sTablePoulePlaces = Voetbal_PoulePlace_Db_Persistance::getTable()->getName();
		$sTablePoules = Voetbal_Poule_Db_Persistance::getTable()->getName();
		$sTableRounds = Voetbal_Round_Db_Persistance::getTable()->getName();
		$sTableCompSeasons = Voetbal_CompetitionSeason_Db_Persistance::getTable()->getName();

		$oSelect = $this->m_objDatabase->select();
		$oSelect->from(array( $sTableGoals ), array() )
            ->join(array( $sTableGameParticipations ), $sTableGoals.".PlayerPeriodsPerGameId = ".$sTableGameParticipations.".Id", array() )
			->join(array( $sTablePlayerPeriods ), $sTableGameParticipations.".PlayerPeriodId = ".$sTablePlayerPeriods.".Id", array() )
			->join(array( $sTablePersons ), $sTablePlayerPeriods.".PersonId = ".$sTablePersons.".Id" )
			->join(array( $sTableGames ), $sTableGameParticipations.".GameId = ".$sTableGames.".Id", array() )
			->join(array( "HomePoulePlaces" => $sTablePoulePlaces ), $sTableGames.".HomePoulePlaceId = HomePoulePlaces.Id", array() )
			->join(array( "AwayPoulePlaces" => $sTablePoulePlaces ), $sTableGames.".AwayPoulePlaceId = AwayPoulePlaces.Id", array() )
			->join(array( $sTablePoules ), "HomePoulePlaces.PouleId = ".$sTablePoules.".Id", array() )
			->join(array( $sTableRounds ), $sTablePoules.".RoundId = ".$sTableRounds.".Id", array() )
			->join(array( $sTableCompSeasons ), $sTableRounds.".CompetitionsPerSeasonId = ".$sTableCompSeasons.".Id", array() )
		;

		$oSelect->columns( array( "COUNT(*) AS NrOfGoals" ) );

		$oSelect->group( array( "Persons.Id" ) );
		$oSelect->order( array( "NrOfGoals DESC" ) );

		$this->addWhereOrderBy( $oSelect, $oOptions );

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}

	/**
	 * @see Source_Db_Reader_Interface::createObjectFromRow()
	 */
	public function createObjectFromRow( $row, $oObjectProperties )
	{
		$oObject = parent::createObjectFromRow( $row, $oObjectProperties );

		if ( array_key_exists( "NrOfGoals", $row ) )
			$oObject->putNrOfGoalsTmp( $row["NrOfGoals"] );

		return $oObject;
	}
}
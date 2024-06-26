<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 851 2014-04-05 09:45:19Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_CompetitionSeason_Db_Reader extends Source_Db_Reader implements Voetbal_CompetitionSeason_Db_Reader_Interface
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );

		$this->addPersistance( Voetbal_Competition_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Season_Factory::createDbPersistance() );
	}

	/**
	 * @see Source_Db_Reader_Interface::getQuery()
	 */
    public function getQuery( Construction_Option_Collection $oOptions = null ): Zend_Db_Select
	{
		$sTableCompetitions = Voetbal_Competition_Db_Persistance::getTable()->getName();
		$sTableSeasons = Voetbal_Season_Db_Persistance::getTable()->getName();

		$oSelect = parent::getQuery( $oOptions );
		$oSelect->joinLeft(array( $sTableCompetitions ), $this->getTableName().".CompetitionId = ".$sTableCompetitions.".Id", array() );
		$oSelect->joinLeft(array( $sTableSeasons ), $this->getTableName().".SeasonId = ".$sTableSeasons.".Id", array() );
		return $oSelect;
	}

	/**
	 * Er moet een ronde zijn met nummer 0
	 * Deze ronde moet pouleplekken hebben en deze ronde mag geen pouleplekken hebben zonder teams
	 *
	 * @see Voetbal_CompetitionSeason_Db_Reader_Interface::createObjectsWithTeams()
	 */
	public function createObjectsWithTeams( Construction_Option_Collection  $oOptions = null ): Patterns_Collection
	{
		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		$oSelect = $this->getQuery( $oOptions );

		$oSelect->where(
			"EXISTS (
				SELECT * FROM Rounds WHERE Number = 0 AND CompetitionsPerSeasonId = ".$this->getTableName().".Id
				AND	( EXISTS ( SELECT * FROM PoulePlaces PP JOIN Poules P ON PP.PouleId = P.Id WHERE P.RoundId = Rounds.Id ) )
				AND ( NOT EXISTS ( SELECT * FROM PoulePlaces PP JOIN Poules P ON PP.PouleId = P.Id WHERE P.RoundId = Rounds.Id AND PP.TeamId IS NULL ) )
			 )"
		);

		$this->addWhereOrderBy( $oSelect, $oOptions );

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}

	/**
	 * 1 Als er minimaal 1 wedstrijd is
	 *
	 * @see Voetbal_CompetitionSeason_Db_Reader_Interface::createObjectsCustom()
	 */
	public function createObjectsCustom( $bStarted, $bEnded, Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		$sTableGames = Voetbal_Game_Db_Persistance::getTable()->getName();
		$sTablePoulePlaces = Voetbal_PoulePlace_Db_Persistance::getTable()->getName();
		$sTablePoules = Voetbal_Poule_Db_Persistance::getTable()->getName();
		$sTableRounds = Voetbal_Round_Db_Persistance::getTable()->getName();

		$oSelect = $this->getQuery( $oOptions );

		$oGameSelect = $this->m_objDatabase->select();
		$oGameSelect
			->from(array( $sTableGames ) )
			->joinLeft(array("HomePoulePlaces" => $sTablePoulePlaces ), $sTableGames.".HomePoulePlaceId = HomePoulePlaces.Id", array() )
			->joinLeft(array("AwayPoulePlaces" => $sTablePoulePlaces ), $sTableGames.".AwayPoulePlaceId = AwayPoulePlaces.Id", array() )
			->joinLeft(array( $sTablePoules ), "HomePoulePlaces.PouleId = ".$sTablePoules.".Id", array() )
			->joinLeft(array( $sTableRounds ), $sTablePoules.".RoundId = ".$sTableRounds.".Id", array() )
			->where( $sTableRounds.".CompetitionsPerSeasonId = ".$this->getTableName().".Id" )
			;
		$oSelect->where( "EXISTS(".$oGameSelect.")");

		if ( $bStarted !== null or $bEnded !== null )
		{
			if ( $bEnded !== null )
			{
				$oDeadline = Agenda_Factory::createDateTime()->modify("+4 hours");
				$sOperator = ( $bEnded === true ) ? ">" : "<";
				$oSelect->where(
					$this->toSqlString( $oDeadline )." ".$sOperator."
					(
						SELECT 		MAX( StartDateTime )
						FROM 		".$sTableGames."
									JOIN ".$sTablePoulePlaces." ON ".$sTableGames.".HomePoulePlaceId = ".$sTablePoulePlaces.".Id
									JOIN ".$sTablePoules." ON ".$sTablePoulePlaces.".PouleId = ".$sTablePoules.".Id
									JOIN ".$sTableRounds." ON Poules.RoundId = ".$sTableRounds.".Id
						WHERE 		".$sTableRounds.".CompetitionsPerSeasonId = ".$this->getTableName().".Id
						GROUP BY 	".$sTableRounds.".CompetitionsPerSeasonId
					)
				" );

			}
			if ( $bStarted !== null )
			{
				$oDeadline = Agenda_Factory::createDateTime();
				$sOperator = ( $bStarted === true ) ? ">" : "<";
				$oSelect->where(
					$this->toSqlString( $oDeadline )." ".$sOperator."
					(
						SELECT 		MIN( StartDateTime )
						FROM 		".$sTableGames."
									JOIN ".$sTablePoulePlaces." ON ".$sTableGames.".HomePoulePlaceId = ".$sTablePoulePlaces.".Id
									JOIN ".$sTablePoules." ON ".$sTablePoulePlaces.".PouleId = ".$sTablePoules.".Id
									JOIN ".$sTableRounds." ON Poules.RoundId = ".$sTableRounds.".Id
						WHERE 		".$sTableRounds.".CompetitionsPerSeasonId = ".$this->getTableName().".Id
						GROUP BY 	".$sTableRounds.".CompetitionsPerSeasonId
					)
				" );
			}
		}

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}

	public function getLastPouleRoundNumber( $oCompetitionSeason )
	{
		$nDbType = $this->getDbType();
		$this->m_arrBindVars[] = $this->toSqlString( $oCompetitionSeason, true );

		$sSelect = null;
		try
		{
			$sSelect = "
				select Number from (
						select RoundId, Number, max( Amount ) MaxAmount, CompetitionsPerSeasonId from (
								select RoundId, PouleId, count(*) as Amount
								from PoulePlaces PP join Poules P ON P.Id = PP.PouleId
								group by RoundId, PouleId
						) AS TeamsPerPoule
						join Rounds on Rounds.Id = TeamsPerPoule.RoundId
						group by RoundId
				) as MaxTeamsPerPoule
				where CompetitionsPerSeasonId = ".Source_Db::getParamName( $nDbType, 0 )."
				and MaxAmount > 2
			";
			$stmt = $this->m_objDatabase->prepare( $sSelect );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			if( $row = $stmt->fetch() )
				return $row["Number"];
		}
		catch ( Exception $e )
		{
			throw new Exception( $e->getMessage().", For Query: ".$sSelect, E_ERROR );
		}
		return 0;
	}
}
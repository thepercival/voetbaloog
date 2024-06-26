<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 996 2015-02-25 12:13:26Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Game_Db_Reader extends Source_Db_Reader implements Voetbal_Game_Db_Reader_Interface, Source_Reader_Ext_Interface, Source_Reader_Ext_Nr_Interface
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );

		$this->addPersistance( Voetbal_PoulePlace_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Poule_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Round_Factory::createDbPersistance() );
	}

	protected function getSelectFrom( $bCount = false )
	{
		$oSelect = parent::getSelectFrom( $bCount );

		$sTablePoulePlaces = Voetbal_PoulePlace_Db_Persistance::getTable()->getName();
		$sTablePoules = Voetbal_Poule_Db_Persistance::getTable()->getName();
		$sTableRounds = Voetbal_Round_Db_Persistance::getTable()->getName();
		$sTableCompSeasons = Voetbal_CompetitionSeason_Db_Persistance::getTable()->getName();

		$oSelect
			->joinLeft(array("HomePoulePlaces" => $sTablePoulePlaces), $this->getTableName().".HomePoulePlaceId = HomePoulePlaces.Id", array() )
			->joinLeft(array("AwayPoulePlaces" => $sTablePoulePlaces), $this->getTableName().".AwayPoulePlaceId = AwayPoulePlaces.Id", array() )
			->joinLeft(array( $sTablePoules ), "HomePoulePlaces.PouleId = Poules.Id", array() )
			->joinLeft(array( $sTableRounds ), $sTablePoules.".RoundId = ".$sTableRounds.".Id", array() )
			->joinLeft(array( $sTableCompSeasons ), $sTableRounds.".CompetitionsPerSeasonId = ".$sTableCompSeasons.".Id", array("CSId" => $sTableCompSeasons.".Id" ) );

		return $oSelect;
	}

	/**
	* @see Source_Reader_Ext_Interface::createObjectsExt()
	*/
    public function createObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): Patterns_Collection
	{
		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		$oSelect = $this->m_objDatabase->select();

		if ( $sClassName === "Voetbal_Team_Membership" )
		{
			$this->addPersistance( Voetbal_Team_Membership_Player_Factory::createDbPersistance() );
			$this->addPersistance( Voetbal_CompetitionSeason_Factory::createDbPersistance() );

			if ( $oObject !== null and $oObject instanceof Voetbal_Team_Membership_Player )
				$oOptions->addFilter( "Voetbal_Team_Membership_Player::Id", "EqualTo", $oObject );

			$sTablePlayerPeriods = Voetbal_Team_Membership_Player_Db_Persistance::getTable()->getName();
			$sTableGameParticipations = Voetbal_Game_Participation_Db_Persistance::getTable()->getName();
			$sTablePoulePlaces = Voetbal_PoulePlace_Db_Persistance::getTable()->getName();
			$sTablePoules = Voetbal_Poule_Db_Persistance::getTable()->getName();
			$sTableRounds = Voetbal_Round_Db_Persistance::getTable()->getName();
			$sTableCompetitionSeasons = Voetbal_CompetitionSeason_Db_Persistance::getTable()->getName();

			$oSelect->distinct()
				->from(array( $sTableGameParticipations ), array() )
				->join(array( $this->getTableName() => $this->getTableName() ), $sTableGameParticipations.".GameId = ".$this->getTableName().".Id" )
				->join(array( $sTablePlayerPeriods ), $sTableGameParticipations.".PlayerPeriodId = ".$sTablePlayerPeriods.".Id", array() )
				->joinLeft(array('HomePoulePlaces' => $sTablePoulePlaces ), $this->getTableName().'.HomePoulePlaceId = HomePoulePlaces.Id', array() )
				->joinLeft(array('AwayPoulePlaces' => $sTablePoulePlaces ), $this->getTableName().'.AwayPoulePlaceId = AwayPoulePlaces.Id', array() )
				->joinLeft(array( $sTablePoules ), "HomePoulePlaces.PouleId = ".$sTablePoules.".Id", array() )
				->joinLeft(array( $sTableRounds ), $sTablePoules.".RoundId = ".$sTableRounds.".Id", array() )
				->joinLeft(array( $sTableCompetitionSeasons ), $sTableRounds.".CompetitionsPerSeasonId = ".$sTableCompetitionSeasons.".Id", array() )
			;
		}
		else if ( $sClassName === "Voetbal_Team" )
		{
			$oSelect = $this->getQuery( $oOptions );

			if ( $oObject !== null and $oObject instanceof Voetbal_Team )
			{
				$oSelect->where(
					"HomePoulePlaces.TeamId = ".$oObject->getId().
					" or AwayPoulePlaces.TeamId = ".$oObject->getId()
				);
			}
		}
		else
			throw new Exception( "No classname set!", E_ERROR );

		$this->addWhereOrderBy( $oSelect, $oOptions );

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}

	/**
	 * @see Source_Reader_Ext_Nr_Interface::getNrOfObjectsExt()
	 */
	public function getNrOfObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): int
	{
		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		$oSelect = $this->m_objDatabase->select();

		if ( $sClassName === "Voetbal_Team_Membership" )
		{
			$this->addPersistance( Voetbal_Team_Membership_Player_Factory::createDbPersistance() );
			$this->addPersistance( Voetbal_CompetitionSeason_Factory::createDbPersistance() );

			if ( $oObject !== null and $oObject instanceof Voetbal_Team_Membership_Player )
				$oOptions->addFilter( "Voetbal_Team_Membership_Player::Id", "EqualTo", $oObject );

			$sTablePlayerPeriods = Voetbal_Team_Membership_Player_Db_Persistance::getTable()->getName();
			$sTableGameParticipations = Voetbal_Game_Participation_Db_Persistance::getTable()->getName();
			$sTablePoulePlaces = Voetbal_PoulePlace_Db_Persistance::getTable()->getName();
			$sTablePoules = Voetbal_Poule_Db_Persistance::getTable()->getName();
			$sTableRounds = Voetbal_Round_Db_Persistance::getTable()->getName();
			$sTableCompetitionSeasons = Voetbal_CompetitionSeason_Db_Persistance::getTable()->getName();

			$oSelect->distinct()
			        ->from(array( $sTableGameParticipations ), array() )
			        ->join(array( $this->getTableName() => $this->getTableName() ), $sTableGameParticipations.".GameId = ".$this->getTableName().".Id" )
			        ->join(array( $sTablePlayerPeriods ), $sTableGameParticipations.".PlayerPeriodId = ".$sTablePlayerPeriods.".Id", array() )
			        ->joinLeft(array('HomePoulePlaces' => $sTablePoulePlaces ), $this->getTableName().'.HomePoulePlaceId = HomePoulePlaces.Id', array() )
			        ->joinLeft(array('AwayPoulePlaces' => $sTablePoulePlaces ), $this->getTableName().'.AwayPoulePlaceId = AwayPoulePlaces.Id', array() )
			        ->joinLeft(array( $sTablePoules ), "HomePoulePlaces.PouleId = ".$sTablePoules.".Id", array() )
			        ->joinLeft(array( $sTableRounds ), $sTablePoules.".RoundId = ".$sTableRounds.".Id", array() )
			        ->joinLeft(array( $sTableCompetitionSeasons ), $sTableRounds.".CompetitionsPerSeasonId = ".$sTableCompetitionSeasons.".Id", array() )
			;
		}
		else if ( $sClassName === "Voetbal_Team" )
		{
			$oSelect = $this->getQuery( $oOptions );

			if ( $oObject !== null and $oObject instanceof Voetbal_Team )
			{
				$oSelect->where(
					"HomePoulePlaces.TeamId = ".$oObject->getId().
					" or AwayPoulePlaces.TeamId = ".$oObject->getId()
				);
			}
		}
		else
			throw new Exception( "No classname set!", E_ERROR );

		$this->addWhereOrderBy( $oSelect, $oOptions );

		$oCountSelect = $this->m_objDatabase->select();
		$oCountSelect->from( $oSelect, array( static::COUNT_COLUMN => new Zend_Db_Expr("COUNT(*)") ) );
		// var_dump( $this->m_arrBindVars ); echo $oCountSelect; die();
		return $this->getNrOfObjectsHelper( $oCountSelect );
	}

	/**
	 * @inheritDoc
	 */
	public function getNumberRange( Voetbal_Poule $oPoule, int $nStates = null, Agenda_DateTime $oStartDateTime = null, Agenda_DateTime $oEndDateTime = null ): ?RAD_Range
	{
		$oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "Voetbal_Poule::Id", "EqualTo", $oPoule );
        if ( $nStates !== null ) {
			$oOptions->addFilter( "Voetbal_Game::State", "BinaryIn", $nStates );
        }
		if ( $oStartDateTime !== null ) {
			$oOptions->addFilter( "Voetbal_Game::StartDateTime", "GreaterThan", $oStartDateTime );
        }
		if ( $oEndDateTime !== null ) {
			$oOptions->addFilter( "Voetbal_Game::StartDateTime", "SmallerThan", $oEndDateTime );
        }
		$oSelect = $this->getQuery( $oOptions );
		$oSelect->reset( Zend_Db_Select::COLUMNS );
		$oSelect->columns( array( "MIN( ".$this->getTableName().".Number ) AS MinNumber", "MAX( ".$this->getTableName().".Number ) AS MaxNumber" ) );

		try
		{
			$stmt = $this->m_objDatabase->prepare( $oSelect );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			if( $row = $stmt->fetch() ) {
                if( $row["MinNumber"] !== null && $row["MaxNumber"] !== null ) {
                    return new RAD_Range( $row["MinNumber"], $row["MaxNumber"] );
                }
			}
            return null;
		}
		catch ( Exception $e )
		{
			throw new Exception( $e->getMessage().", For Query: ".(string) $oSelect, E_ERROR );
		}
	}

    public function getStateGameRounds( Voetbal_Poule $oPoule )
    {
        $nNrOfGamesPerRound = Voetbal_Poule_Factory::getNrOfGamesPerRound( $oPoule->getPlaces()->count() );

        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "Voetbal_Poule::Id", "EqualTo", $oPoule );
        $oOptions->addFilter( "Voetbal_Game::State", "EqualTo", Voetbal_Factory::STATE_PLAYED );

        $oSelect = $this->getQuery( $oOptions );
        $oSelect->reset( Zend_Db_Select::COLUMNS );
        $oSelect->columns( array( $this->getTableName() . ".Number", "IF(COUNT(*) = ".$nNrOfGamesPerRound.", ".Voetbal_Factory::STATE_PLAYED.",IF(COUNT(*) = 0, ".Voetbal_Factory::STATE_SCHEDULED.",".Voetbal_Factory::STATE_INPROGRESS.")) AS State" ) );
        $oSelect->group( array( $this->getTableName() . ".Number" ) );

        $arrStateGameRounds = [];
        try
        {
            $stmt = $this->m_objDatabase->prepare( $oSelect );
            $stmt->execute( $this->m_arrBindVars );
            $this->m_arrBindVars = array();

            while ( $row = $stmt->fetch() )
            {
                $arrStateGameRounds[$row["Number"]] = $row["State"];
            }
        }
        catch ( Exception $e )
        {
            throw new Exception( $e->getMessage().", For Query: ".(string) $oSelect, E_ERROR );
        }
        return $arrStateGameRounds;
    }

	/**
	 * @see Source_Db_Reader_Interface::createObjectFromRow()
	 */
	public function createObjectFromRow( $row, $oObjectProperties )
	{
		$oObject = parent::createObjectFromRow( $row, $oObjectProperties );

		if ( array_key_exists( "CSId", $row ) )
			$oObject->putCompetitionSeason( (int) $row[ "CSId"] );

		return $oObject;
	}
}
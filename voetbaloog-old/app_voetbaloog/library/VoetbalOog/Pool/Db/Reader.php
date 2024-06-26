<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pool_Db_Reader extends Source_Db_Reader implements VoetbalOog_Pool_Db_Reader_Interface, Source_Reader_Ext_Interface
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );

		$this->addPersistance( Voetbal_CompetitionSeason_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Competition_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Season_Factory::createDbPersistance() );
	}

	/**
	 * @see Reader_Interface::getObjectPropertiesToRead()
	 */
	public function getObjectPropertiesToRead(): Patterns_Collection
	{
		if ( $this->m_objObjectProperties === null )
		{
			$this->m_objObjectProperties = Patterns_Factory::createCollection();
			$oObjectPropertiesToAdd = MetaData_Factory::createObjectPropertiesExt( "VoetbalOog_Pool", "Id", "Name", "CompetitionSeason", "Stake" );
			$this->m_objObjectProperties->addCollection( $oObjectPropertiesToAdd );
		}

		return $this->m_objObjectProperties;
	}

	/**
	 * @see Source_Db_Reader_Interface::getQuery()
	 */
	public function getQuery( Construction_Option_Collection $oOptions = null ): Zend_Db_Select
	{
		$sTableCompSeasons = Voetbal_CompetitionSeason_Db_Persistance::getTable()->getName();
		$sTableCompetitions = Voetbal_Competition_Db_Persistance::getTable()->getName();
		$sTableSeasons = Voetbal_Season_Db_Persistance::getTable()->getName();

		$oSelect = parent::getQuery( $oOptions );
		$oSelect
			->joinLeft(array( $sTableCompSeasons ), $this->getTableName().".CompetitionsPerSeasonId = ".$sTableCompSeasons.".Id", array() )
			->joinLeft(array( $sTableCompetitions ), $sTableCompSeasons.".CompetitionId = ".$sTableCompetitions.".Id", array() )
			->joinLeft(array( $sTableSeasons ), $sTableCompSeasons.".SeasonId = ".$sTableSeasons.".Id", array() );
		return $oSelect;
	}

	/**
	 * @see Source_Reader_Ext_Interface::createObjectsExt()
	 */
    public function createObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): Patterns_Collection
	{
		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		if ( $oObject instanceof RAD_Auth_User_Interface )
		{
			$this->addPersistance( VoetbalOog_Pool_User_Factory::createDbPersistance() );

			$oSelect = $this->m_objDatabase->select();

			$sTablePoolUsers = VoetbalOog_Pool_User_Db_Persistance::getTable()->getName();
			$sTableCompSeasons = Voetbal_CompetitionSeason_Db_Persistance::getTable()->getName();
			$sTableCompetitions = Voetbal_Competition_Db_Persistance::getTable()->getName();
			$sTableSeasons = Voetbal_Season_Db_Persistance::getTable()->getName();

			$oSelect
				->from(array( $sTablePoolUsers ), array() )
				->join(array( $this->getTableName() => $this->getTableName()), $sTablePoolUsers.".PoolId = ".$this->getTableName().".Id" )
				->joinLeft(array( $sTableCompSeasons ), $this->getTableName().".CompetitionsPerSeasonId = ".$sTableCompSeasons.".Id", array() )
				->joinLeft(array( $sTableCompetitions ), $sTableCompSeasons.".CompetitionId = ".$sTableCompetitions.".Id", array() )
				->joinLeft(array( $sTableSeasons ), $sTableCompSeasons.".SeasonId = ".$sTableSeasons.".Id", array() )
				->where( $sTablePoolUsers.".UserId = ".$this->toSqlString( $oObject ) );
		}
		else
			throw new Exception( "incorrect params userext", E_ERROR );

		$this->addWhereOrderBy( $oSelect, $oOptions );

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}

	/**
	 * @see VoetbalOog_Pool_Db_Reader_Interface::createObjectsWithSameRoundBetConfig()
	 */
	public function createObjectsWithSameRoundBetConfig( $oPoolUser, $oOptions = null )
	{
		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		$oPool = $oPoolUser->getPool();

		$this->addPersistance( VoetbalOog_Pool_User_Factory::createDbPersistance() );
		$oOptions->addFilter( "VoetbalOog_Pool::CompetitionSeason", "EqualTo", $oPool->getCompetitionSeason() );

		$sTablePoolUsers = VoetbalOog_Pool_User_Db_Persistance::getTable()->getName();
		$sTableRoundBetConfigs = VoetbalOog_Round_BetConfig_Db_Persistance::getTable()->getName();

		$oSelect = $this->m_objDatabase->select();
		$oSelect
			->from(array("RBC" => $sTableRoundBetConfigs ), array() )
			->distinct()
			->join(array( $this->getTableName() => $this->getTableName()), "RBC.PoolId = ".$this->getTableName().".Id" )
			->where(
				"EXISTS (
					SELECT	*
					FROM	".$sTableRoundBetConfigs." RBCSub
					WHERE	RBCSub.RoundId = RBC.RoundId
					AND		RBCSub.BetType = RBC.BetType
					AND		RBCSub.BetTime = RBC.BetTime
					AND		RBCSub.Id <> RBC.Id
					AND 	RBCSub.PoolId <> RBC.PoolId
				)" )
			->where(
				"EXISTS (
					SELECT	*
					FROM	".$sTablePoolUsers."
					WHERE	PoolId = ".$this->getTableName().".Id
					AND		UserId = ". $this->toSqlString( $oPoolUser->getUser() ) ."
				)"
			);

		$this->addWhereOrderBy( $oSelect, $oOptions );

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}
}
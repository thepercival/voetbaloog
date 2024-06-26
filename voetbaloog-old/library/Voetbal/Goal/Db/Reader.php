<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 924 2014-08-29 11:01:33Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Goal_Db_Reader extends Source_Db_Reader implements Source_Reader_Ext_Interface
{
	public function __construct($oFactory)
	{
		parent::__construct($oFactory);

		$this->addPersistance(Voetbal_Game_Participation_Factory::createDbPersistance());
		$this->addPersistance(Voetbal_Team_Membership_Player_Factory::createDbPersistance());
	}

	/**
	 * @see Source_Db_Reader_Interface::getQuery()
	 */
	protected function getSelectFrom( $bCount = false )
	{
		$oSelect = parent::getSelectFrom( $bCount );

		$sTableGameParticipations = Voetbal_Game_Participation_Db_Persistance::getTable()->getName();
		$sTablePlayerMemberships = Voetbal_Team_Membership_Player_Db_Persistance::getTable()->getName();

		$oSelect
			->join(array($sTableGameParticipations), $this->getTableName() . ".PlayerPeriodsPerGameId = " . $sTableGameParticipations . ".Id", array())
			->join(array($sTablePlayerMemberships), $sTableGameParticipations . ".PlayerPeriodId = " . $sTablePlayerMemberships . ".Id", array());

		return $oSelect;
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

			$sTableGameParticipations = Voetbal_Game_Participation_Db_Persistance::getTable()->getName();
			$sTableGames = Voetbal_Game_Db_Persistance::getTable()->getName();
			$sTablePoulePlaces = Voetbal_PoulePlace_Db_Persistance::getTable()->getName();
			$sTablePoules = Voetbal_Poule_Db_Persistance::getTable()->getName();
			$sTableRounds = Voetbal_Round_Db_Persistance::getTable()->getName();
			$sTableCompSeasons = Voetbal_CompetitionSeason_Db_Persistance::getTable()->getName();

			if ( $oObject !== null )
				$oOptions->addFilter( "Voetbal_Poule::Id", "EqualTo", $oObject );

			$oSelect
				->join(array($sTableGames), $sTableGames.".Id = ".$sTableGameParticipations.".GameId", array() )
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
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
class VoetbalOog_Pool_User_Db_Reader extends Source_Db_Reader implements VoetbalOog_Pool_User_Db_Reader_Interface
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );

		$this->addPersistance( VoetbalOog_Pool_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_CompetitionSeason_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Competition_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Season_Factory::createDbPersistance() );
		$this->addPersistance( VoetbalOog_User_Factory::createDbPersistance() );
	}

  	/**
	 * @see Source_Db_Reader_Interface::getQuery()
	 */
    public function getQuery( Construction_Option_Collection $oOptions = null ): Zend_Db_Select
	{
		$sTablePools = VoetbalOog_Pool_Db_Persistance::getTable()->getName();
		$sTableCompSeasons = Voetbal_CompetitionSeason_Db_Persistance::getTable()->getName();
		$sTableCompetitions = Voetbal_Competition_Db_Persistance::getTable()->getName();
		$sTableSeasons = Voetbal_Season_Db_Persistance::getTable()->getName();
		$sTableUsers = VoetbalOog_User_Db_Persistance::getTable()->getName();

		$oSelect = parent::getQuery( $oOptions );
		$oSelect->join(array( $sTablePools ), $this->getTableName().".PoolId = ".$sTablePools.".Id", array() );
		$oSelect->join(array( $sTableCompSeasons ), $sTablePools . ".CompetitionsPerSeasonId = ".$sTableCompSeasons.".Id", array() );
		$oSelect->join(array( $sTableCompetitions ), $sTableCompSeasons . ".CompetitionId = ".$sTableCompetitions.".Id", array() );
		$oSelect->join(array( $sTableSeasons ), $sTableCompSeasons . ".SeasonId = ".$sTableSeasons.".Id", array() );
		$oSelect->join(array( $sTableUsers ), $this->getTableName() . ".UserId = ".$sTableUsers.".Id", array() );

		// @TODO move to bets CDK
		/* 
		$oSelect->columns( array(
								"( ".
								"SELECT SUM( Points ) ".
								"FROM 	".$sTableBets." ".
								"JOIN ".$sTableRoundBetConfigs." ON ".$sTableBets.".RoundBetConfigId = ".$sTableRoundBetConfigs.".Id ".
								"WHERE	".$sTableBets.".Correct = 1 ".
								"AND	".$sTableBets.".UsersPerPoolId = ".$this->getTableName().".Id ".
								") as Points" ) );
		$sTableBets = VoetbalOog_Bet_Db_Persistance::getTable()->getName();
		$sTableRoundBetConfigs = VoetbalOog_Round_BetConfig_Db_Persistance::getTable()->getName();
		*/
		
		return $oSelect;
	}	
}
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
class VoetbalOog_Pool_Payment_Db_Reader extends Source_Db_Reader
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );

		$this->addPersistance( VoetbalOog_Pool_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_CompetitionSeason_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Season_Factory::createDbPersistance() );
	}

	/**
	 * @see Source_Db_Reader_Interface::getQuery()
	 */
    public function getQuery( Construction_Option_Collection $oOptions = null ): Zend_Db_Select
	{
		$sTablePools = VoetbalOog_Pool_Db_Persistance::getTable()->getName();
		$sTableCompSeasons = Voetbal_CompetitionSeason_Db_Persistance::getTable()->getName();
		$sTableSeasons = Voetbal_Season_Db_Persistance::getTable()->getName();

		$oSelect = parent::getQuery( $oOptions );
		$oSelect
			->join(array( $sTablePools ), $this->getTableName().".PoolId = ".$sTablePools.".Id", array() )
			->join(array( $sTableCompSeasons ), $sTablePools.".CompetitionsPerSeasonId = ".$sTableCompSeasons.".Id", array() )
			->join(array($sTableSeasons ), $sTableCompSeasons.".SeasonId = ".$sTableSeasons.".Id", array() );

		return $oSelect;
	}
}
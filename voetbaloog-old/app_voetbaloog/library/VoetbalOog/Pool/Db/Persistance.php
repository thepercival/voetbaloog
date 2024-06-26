<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 760 2014-03-02 08:28:46Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
final class VoetbalOog_Pool_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "Pools";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["VoetbalOog_Pool::Id"] = $oTable->createColumn( "Id" );
		$this["VoetbalOog_Pool::Name"] = $oTable->createColumn( "Name" );
		$this["VoetbalOog_Pool::CompetitionSeason"] = $oTable->createColumn( "CompetitionsPerSeasonId" );
		$this["VoetbalOog_Pool::Picture"] = $oTable->createColumn( "Picture" );
		$this["VoetbalOog_Pool::Stake"] = $oTable->createColumn( "Stake" );
	}
}
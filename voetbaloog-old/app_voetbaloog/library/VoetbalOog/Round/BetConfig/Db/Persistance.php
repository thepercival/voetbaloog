<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 580 2013-11-20 15:28:51Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
final class VoetbalOog_Round_BetConfig_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "RoundBetConfigs";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["VoetbalOog_Round_BetConfig::Id"] = $oTable->createColumn( "Id" );
		$this["VoetbalOog_Round_BetConfig::BetType"] = $oTable->createColumn( "BetType" );
		$this["VoetbalOog_Round_BetConfig::Round"] = $oTable->createColumn( "RoundId" );
		$this["VoetbalOog_Round_BetConfig::BetTime"] = $oTable->createColumn( "BetTime" );
		$this["VoetbalOog_Round_BetConfig::Points"] = $oTable->createColumn( "Points" );
		$this["VoetbalOog_Round_BetConfig::Pool"] = $oTable->createColumn( "PoolId" );
	}
}
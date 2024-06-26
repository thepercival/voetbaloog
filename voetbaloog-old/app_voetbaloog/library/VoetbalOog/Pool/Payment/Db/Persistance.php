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
final class VoetbalOog_Pool_Payment_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "PaymentsPerPool";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["VoetbalOog_Pool_Payment::Id"] = $oTable->createColumn( "Id" );
		$this["VoetbalOog_Pool_Payment::Pool"] = $oTable->createColumn( "PoolId" );
		$this["VoetbalOog_Pool_Payment::Place"] = $oTable->createColumn( "Place" );
		$this["VoetbalOog_Pool_Payment::TimesStake"] = $oTable->createColumn( "TimesStake" );
	}
}
<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 929 2014-08-31 18:12:20Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
final class Voetbal_Goal_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "Goals";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["Voetbal_Goal::Id"] = $oTable->createColumn( "Id" );
		$this["Voetbal_Goal::GameParticipation"] = $oTable->createColumn( "PlayerPeriodsPerGameId" );
		$this["Voetbal_Goal::Minute"] = $oTable->createColumn( "Minute" );
		$this["Voetbal_Goal::OwnGoal"] = $oTable->createColumn( "OwnGoal" );
		$this["Voetbal_Goal::Penalty"] = $oTable->createColumn( "Penalty" );
        $this["Voetbal_Goal::AssistGameParticipation"] = $oTable->createColumn( "AssistPlayerPeriodsPerGameId" );
	}
}
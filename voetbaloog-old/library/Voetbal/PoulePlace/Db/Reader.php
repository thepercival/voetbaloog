<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 627 2013-12-15 20:18:35Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_PoulePlace_Db_Reader extends Source_Db_Reader
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );

		$this->addPersistance( Voetbal_Poule_Factory::createDbPersistance() );
		$this->addPersistance( Voetbal_Round_Factory::createDbPersistance() );
	}

	/**
	 * @see Source_Db_Reader::getSelectFrom()
	 */
	protected function getSelectFrom( $bCount = false )
	{
		$oSelect = parent::getSelectFrom( $bCount );

		$sTablePoules = Voetbal_Poule_Db_Persistance::getTable()->getName();
		$sTableRounds = Voetbal_Round_Db_Persistance::getTable()->getName();

		$oSelect
			->joinLeft(array( $sTablePoules ), $this->getTableName() . ".PouleId = ".$sTablePoules.".Id", array() )
			->joinLeft(array( $sTableRounds ), $sTablePoules.".RoundId = ".$sTableRounds.".Id", array() )
		;

		return $oSelect;
	}
}
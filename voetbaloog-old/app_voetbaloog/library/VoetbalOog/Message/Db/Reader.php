<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 1199 2019-08-13 11:22:19Z thepercival $
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Message_Db_Reader extends Source_Db_Reader
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );

		$this->addPersistance( VoetbalOog_Pool_User_Factory::createDbPersistance() );
		$this->addPersistance( VoetbalOog_Pool_Factory::createDbPersistance() );
	}

	/**
	 * @see Source_Db_Reader_Interface::getQuery()
	 */
	public function getQuery( Construction_Option_Collection $oOptions = null ): Zend_Db_Select
	{
		$sTablePools = VoetbalOog_Pool_Db_Persistance::getTable()->getName();
		$sTablePoolUsers = VoetbalOog_Pool_User_Db_Persistance::getTable()->getName();

		$oSelect = parent::getQuery( $oOptions );
		$oSelect
			->join(array( $sTablePoolUsers ), $this->getTableName().".UsersPerPoolId = ".$sTablePoolUsers.".Id", array() )
			->join(array( $sTablePools ), $sTablePoolUsers.".PoolId = ".$sTablePools.".Id", array() )
			;

		return $oSelect;
	}
}
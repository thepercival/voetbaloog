<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 627 2013-12-15 20:18:35Z thepercival $
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Team_Db_Reader extends Source_Db_Reader implements VoetbalOog_Team_Db_Reader_Interface
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );
	}

	/**
	 * @see Voetbal_Team_Db_Reader_Interface::createSameObjects()
	 */
	public function createSameObjects( $oRoundBetConfig )
	{
        $oOptions = Construction_Factory::createOptions();
		$oSelect = $this->getQuery( $oOptions );

		$sTablePoolUsers = VoetbalOog_Pool_User_Db_Persistance::getTable()->getName();
		$sTableBets = VoetbalOog_Bet_Db_Persistance::getTable()->getName();

		$oSelect->where(
				"( SELECT COUNT(*) FROM ".$sTablePoolUsers." WHERE PoolId = ".$oRoundBetConfig->getPool()->getId()." ) " .
				" = " .
				" ( SELECT COUNT(*) FROM ".$sTableBets." WHERE RoundBetConfigId = ".$oRoundBetConfig->getId()." And Teamid = Teams.id ) "
		);

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}
}
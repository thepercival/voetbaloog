<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 984 2015-01-16 12:26:46Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class VoetbalOog_Team_Factory extends Object_Factory_Db implements VoetbalOog_Team_Factory_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see VoetbalOog_Team_Factory_Interface::createSameObjectsFromDatabase()
	 */
	public static function createSameObjectsFromDatabase( $oRoundBetConfig )
	{
		return static::createDbReader()->createSameObjects( $oRoundBetConfig );
	}

	protected function createDbReaderHelper()
	{
		if ( $this->m_objReader === null ) {
			$this->m_objReader = new VoetbalOog_Team_Db_Reader( Voetbal_Team_Factory::getInstance() );
		}
		return $this->m_objReader;
	}
}
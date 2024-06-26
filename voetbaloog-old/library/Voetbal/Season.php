<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Season.php 894 2014-08-14 20:07:54Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Season extends Agenda_TimeSlot implements Voetbal_Season_Interface, Import_Importable_Interface
{
	// Voetbal_Season_Interface
	protected $m_sName;				// string

	use Import_Importable_Trait;

	CONST MAX_NAME_LENGTH = 9;

	/**
	 * @return Voetbal_Season
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return $this->m_sName;
	}

	/**
	 * @inheritDoc
	 */
	public function putName( $sName )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Season::Name", $this->m_sName, $sName );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sName = $sName;
	}

	/**
	 * @see Voetbal_Season_Interface::getAbbreviation()
	 */
	public function getAbbreviation()
	{
		$sName = $this->getName();
		if ( strlen( $sName ) === 9 and strpos( $sName,"/" ) === 4 )
		{
			return substr( $sName, 2, 2 ) . "/" . substr( $sName, 7, 2 );
		}
		if ( strlen( $sName ) === 4 ) {
			return substr( $sName, 2, 2 );
		}
		return $sName;
	}
}
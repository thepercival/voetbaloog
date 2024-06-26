<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Referee.php 776 2014-03-05 08:37:12Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Referee implements Voetbal_Referee_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
	// Voetbal_Referee_Interface
	protected $m_sName;				// string
	protected $m_oCompetitions;		// Collection

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

	/**
	 * @see Voetbal_Referee_Interface::getName()
	 */
	public function getName()
	{
		return $this->m_sName;
	}

	/**
	 * @see Voetbal_Referee_Interface::putName()
	 */
	public function putName( $sName )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Referee::Name", $this->m_sName, $sName );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sName = $sName;
	}
}
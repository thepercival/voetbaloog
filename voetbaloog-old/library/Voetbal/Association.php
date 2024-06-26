<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Association.php 776 2014-03-05 08:37:12Z thepercival $
* @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Association implements Voetbal_Association_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
	// Voetbal_Association_Interface
	protected $m_sName;				// string
	protected $m_sDescription;		// string
	protected $m_oTeams;			// Collection

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

	CONST MAX_NAME_LENGTH = 20;

	/**
	 *
	 * @see Voetbal_Association_Interface::getName()
	 */
	public function getName()
	{
		return $this->m_sName;
	}

	/**
	 * @see Voetbal_Association_Interface::putName()
	 */
	public function putName( $sName )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Association::Name", $this->m_sName, $sName );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sName = $sName;
	}

	/**
	 * @see Voetbal_Association_Interface::getDescription()
	 */
	public function getDescription()
	{
		return $this->m_sDescription;
	}

	/**
	 * @see Voetbal_Association_Interface::putDescription()
	 */
	public function putDescription( $sDescription )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Association::Description", $this->m_sDescription, $sDescription );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sDescription = $sDescription;
	}

	/**
	 * @see Voetbal_Association_Interface::getTeams()
	 */
	public function getTeams(): Patterns_Collection
	{
		if ( $this->m_oTeams === null )
		{
			$oFilters = Construction_Factory::createOptions();
			$oFilters->addFilter( "Voetbal_Team::Association", "EqualTo", $this );
			$oFilters->addOrder( "Voetbal_Team::Name", false );
			$this->m_oTeams = Voetbal_Team_Factory::createObjectsFromDatabase( $oFilters );
		}
		return $this->m_oTeams;
	}
}
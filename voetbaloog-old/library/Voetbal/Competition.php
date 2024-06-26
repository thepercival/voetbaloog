<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Competition.php 776 2014-03-05 08:37:12Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Competition implements Voetbal_Competition_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface, Import_Importable_Interface
{
	// Voetbal_Competition_Interface
	protected $m_sName;					// string
	protected $m_sAbbreviation;			// string
	protected $m_oCompetitionSeasons;	// Collection

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait, Import_Importable_Trait;

	CONST MAX_NAME_LENGTH = 20;

	/**
	 * @see Voetbal_Competition_Interface::getName()
	 */
	public function getName()
	{
		return $this->m_sName;
	}

	/**
	 * @see Voetbal_Competition_Interface::putName()
	 */
	public function putName( $sName )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Competition::Name", $this->m_sName, $sName );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sName = $sName;
	}

	/**
	 * @see Voetbal_Competition_Interface::getAbbreviation()
	 */
	public function getAbbreviation()
	{
		return $this->m_sAbbreviation;
	}

	/**
	 * @see Voetbal_Competition_Interface::putAbbreviation()
	 */
	public function putAbbreviation( $sAbbreviation )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Competition::Abbreviation", $this->m_sAbbreviation, $sAbbreviation );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sAbbreviation = $sAbbreviation;
	}

	/**
	 * @see Voetbal_Competition_Interface::getSeasons()
	 */
	public function getSeasons(): Patterns_Collection
	{
		if ( $this->m_oCompetitionSeasons === null )
		{
			$oFilters = Construction_Factory::createOptions();
			$oFilters->addFilter( "Voetbal_CompetitionSeason::Competition", "EqualTo", $this );
			$oFilters->addOrder( "Voetbal_Season::StartDateTime", true );
			$oFilters->addOrder( "Voetbal_Season::Name", false );
			$this->m_oCompetitionSeasons = Voetbal_CompetitionSeason_Factory::createObjectsFromDatabase( $oFilters );
		}
		return $this->m_oCompetitionSeasons;
	}
}
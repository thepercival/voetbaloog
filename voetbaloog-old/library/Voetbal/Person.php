<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Person.php 974 2014-12-28 19:00:27Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Person extends RAD_Person implements Voetbal_Person_Interface, Import_Importable_Interface, Patterns_Validatable_Interface
{
	protected $m_nNrOfGoalsTmp;			// int
	// Voetbal_Person_Interface
	protected $m_sGender;				// string

	use Patterns_Validatable_Trait, Import_Importable_Trait;

	/**
	 * @return Voetbal_Person
	 */
	public function __construct()
	{
		parent::__construct();
		$this->m_nNrOfGoalsTmp = -1;
	}

	/**
	* @see RAD_Person_Interface::getCallType()
	*/
	public function getCallType()
	{
		return RAD_Person::CALLTYPE_LASTNAME;
	}

	/**
	* @see Voetbal_Person_Interface::getGender()
	*/
	public function getGender()
	{
		return $this->m_sGender;
	}

	/**
	 * @see Voetbal_Person_Interface::putGender()
	 */
	public function putGender( $sGender )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Person::Gender", $this->m_sGender, $sGender );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sGender = $sGender;
	}

	/**
	* @see Voetbal_Person_Interface::getPlayerMemberships()
	*/
	public function getPlayerMemberships( $vtDateTimeSlot = null ): MemberShip_Collection
	{
		$oOptions = Construction_Factory::createOptions();
		if ( $vtDateTimeSlot !== null )
			$oOptions = Construction_Factory::createFiltersForTimeSlots( "Voetbal_Team_Membership_Player", $vtDateTimeSlot, Agenda_TimeSlot::EXCLUDE_NONE, true );
		$oOptions->addFilter( "Voetbal_Team_Membership_Player::Client", "EqualTo", $this );
		return Voetbal_Team_Membership_Player_Factory::createObjectsFromDatabase( $oOptions );
	}

	/**
	* @see Voetbal_Person_Interface::getStaffMemberMemberships()
	*/
	public function getStaffMemberMemberships(): MemberShip_Collection
	{
        return Voetbal_Team_Membership_StaffMember_Factory::createObjects();
	}

	/**
	* @see Voetbal_Person_Interface::getNrOfGoalsTmp()
	*/
	public function getNrOfGoalsTmp()
	{
		return $this->m_nNrOfGoalsTmp;
	}

	/**
	 * {@inheritdoc }
	 */
	public function putNrOfGoalsTmp( $nNrOfGoalsTmp )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Person::NrOfGoalsTmp", $this->m_nNrOfGoalsTmp, $nNrOfGoalsTmp );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nNrOfGoalsTmp = $nNrOfGoalsTmp;
	}
}
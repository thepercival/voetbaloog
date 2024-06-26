<?php

/**
 * @copyright 	2007 Coen Dunnink
 * @license 	http://www.gnu.org/licenses/gpl.txt
 * @version 	$Id: Team.php 955 2014-09-15 16:08:29Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Team implements Voetbal_Team_Interface, MemberShip_Provider_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface, Import_Importable_Interface
{
	// Voetbal_Team_Interface
	protected $m_sName;				// string
	protected $m_sAbbreviation;		// string
	protected $m_sImageName;		// string
	protected $m_oAssociation;		// Voetbal_Association

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait, Import_Importable_Trait;

	CONST MAX_NAME_LENGTH = 16;

	/**
	 * @see Voetbal_Team_Interface::getName()
	 */
	public function getName()
	{
		return $this->m_sName;
	}

	/**
	 * @see Voetbal_Team_Interface::putName()
	 */
	public function putName( $sName )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Team::Name", $this->m_sName, $sName );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sName = $sName;
	}

	/**
	 * @see Voetbal_Team_Interface::getAbbreviation()
	 */
	public function getAbbreviation()
	{
		return $this->m_sAbbreviation;
	}

	/**
	 * @see Voetbal_Team_Interface::putAbbreviation()
	 */
	public function putAbbreviation( $sAbbreviation )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Team::Abbreviation", $this->m_sAbbreviation, $sAbbreviation );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sAbbreviation = $sAbbreviation;
	}

	/**
	 * @see Voetbal_Team_Interface::getImageName()
	 */
	public function getImageName()
	{
		return $this->m_sImageName;
	}

	/**
	 * @see Voetbal_Team_Interface::putImageName()
	 */
	public function putImageName( $sImageName )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Team::ImageName", $this->m_sImageName, $sImageName );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sImageName = $sImageName;
	}

	/**
	 * @see Voetbal_Team_Interface::getAssociation()
	 */
	public function getAssociation()
	{
		if ( is_int( $this->m_oAssociation ) )
			$this->m_oAssociation = Voetbal_Association_Factory::createObjectFromDatabase( $this->m_oAssociation );

		return $this->m_oAssociation;
	}

	/**
	 * @see Voetbal_Team_Interface::putAssociation()
	 */
	public function putAssociation( $oAssociation )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Team::Association", $this->m_oAssociation, $oAssociation );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_oAssociation = $oAssociation;
	}

	/**
	* @see MemberShip_Provider_Interface::getMemberShips()
	*/
	public function getMemberShips( $oDateTimeSlot )
	{
		$oMemberships = MemberShip_Factory::createObjects();

		$oStaffMemberMemberships = $this->getStaffMemberships( $oDateTimeSlot );
		$oStaffMemberMemberships->putId( "staff" );
		$oMemberships->add( $oStaffMemberMemberships );

		$oPlayerMemberships = $this->getPlayerMemberships( $oDateTimeSlot );
		$oPlayerMemberships->putId( "players" );
		$oMemberships->add( $oPlayerMemberships );

		return $oMemberships;
	}

	/**
	 * @see Voetbal_Team_Interface::getPlayerMemberships()
	 */
	public function getPlayerMemberships( $oDateTimeSlot, Construction_Option_Collection $p_oOptions = null )
	{
		$oMemberships = Voetbal_Team_Membership_Player_Factory::createObjects();

		$oOptions = MemberShip_Factory::getMembershipFilters( "Voetbal_Team_Membership_Player", $this, null, $oDateTimeSlot );
		if ( $p_oOptions !== null and $p_oOptions instanceof Construction_Option_Collection )
			$oOptions->addCollection( $p_oOptions );
		$oOptions->addOrder("Voetbal_Team_Membership_Player::BackNumber", false );
		return Voetbal_Team_Membership_Player_Factory::createObjectsFromDatabase( $oOptions );
	}

	/**
	 * @see Voetbal_Team_Interface::getPlayerPersons()
	 */
	public function getPlayerPersons( $oDateTimeSlot, $p_oOptions = null )
	{
		$oOptions = Construction_Factory::createFiltersForTimeSlots( "Voetbal_Team_Membership_Player", $oDateTimeSlot, Agenda_TimeSlot::EXCLUDE_NONE, true );

		if ( $p_oOptions !== null and $p_oOptions instanceof Construction_Option_Collection )
			$oOptions->addCollection( $p_oOptions );
		$oOptions->addOrder("Voetbal_Team_Membership_Player::BackNumber", false );
		return Voetbal_Person_Factory::createObjectsFromDatabaseExt( $this, $oOptions );
	}

	/**
	* @see Voetbal_Team_Interface::getStaffMemberships()
	*/
	public function getStaffMemberships( $oDateTimeSlot )
	{
		$oMemberships = Voetbal_Team_Membership_StaffMember_Factory::createObjects();

		$oOptions = MemberShip_Factory::getMembershipFilters( "Voetbal_Team_Membership_StaffMember", $this, null, $oDateTimeSlot );
		$oOptions->addOrder("Voetbal_Team_Membership_StaffMember::Importance", true );
		return Voetbal_Team_Membership_StaffMember_Factory::createObjectsFromDatabase( $oOptions );
	}
}
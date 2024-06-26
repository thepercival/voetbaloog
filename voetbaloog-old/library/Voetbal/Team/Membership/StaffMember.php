<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: StaffMember.php 776 2014-03-05 08:37:12Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Team_Membership_StaffMember extends Voetbal_Team_Membership implements Voetbal_Team_Membership_StaffMember_Interface
{
	// Voetbal_Team_Membership_StaffMember_Interface
	protected $m_nImportance;	// int
	protected $m_sFunctionX;	// string

	CONST FUNCTION_TRAINER = "trainer";

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @see Voetbal_Team_Membership_StaffMember_Interface::getFunctionX()
	 */
	public function getFunctionX()
	{
		return $this->m_sFunctionX;
	}

	/**
	 * @see Voetbal_Team_Membership_StaffMember_Interface::putFunctionX()
	 */
	public function putFunctionX( $sFunctionX )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Team_Membership_StaffMember::FunctionX", $this->m_sFunctionX, $sFunctionX );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_sFunctionX = $sFunctionX;
	}

	/**
	 * @see Voetbal_Team_Membership_StaffMember_Interface::getImportance()
	 */
	public function getImportance()
	{
		return $this->m_nImportance;
	}

	/**
	 * @see Voetbal_Team_Membership_StaffMember_Interface::putImportance()
	 */
	public function putImportance( $nImportance )
	{
		if ( $this->m_bObserved === true )
		{
			$oObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), "Voetbal_Team_Membership_StaffMember::Importance", $this->m_nImportance, $nImportance );
			$this->notifyObservers( $oObjectChange );
		}
		$this->m_nImportance = $nImportance;
	}
}
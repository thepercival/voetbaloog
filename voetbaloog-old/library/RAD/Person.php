<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Person.php 4563 2019-09-01 12:32:37Z thepercival $
 * @package    RAD
 */


/**
 * @package RAD
 */
class RAD_Person implements RAD_Person_Interface, Patterns_ObservableObject_Interface, Patterns_Idable_Interface
{
	// RAD_Person_Interface
	protected $m_szFirstName;				// string
	protected $m_szFirstNamePartner;		// string
	protected $m_szLastName;				// string
	protected $m_szLastNamePartner;			// string
	protected $m_szNameInsertions;			// string
	protected $m_szNameInsertionsPartner;	// string
	protected $m_nCallType;					// int
	protected $m_oDateOfBirth;				// DateTime

	use Patterns_ObservableObject_Trait, Patterns_Idable_Trait;

	CONST CALLTYPE_LASTNAME = 1;
	CONST CALLTYPE_LASTNAME_LASTNAMEPARTNER = 2;
	CONST CALLTYPE_LASTNAMEPARTNER_LASTNAME = 3;
	CONST CALLTYPE_LASTNAMEPARTNER = 4;

	CONST CALLTYPE_FULLNAME_ORDER = 1;
	CONST CALLTYPE_FULLNAME_FIRSTNAMELETTER = 2;

	public function __construct(){}

  	/**
	 * @see RAD_Person_Interface::getFirstName()
	 */
  	public function getFirstName()
	{
		return $this->m_szFirstName;
	}

	/**
	 * @see RAD_Person_Interface::putFirstName()
	 */
	public function putFirstName( $szFirstName )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::FirstName", $this->m_szFirstName, $szFirstName );
  			$this->notifyObservers( $objObjectChange );
		}
		$this->m_szFirstName = $szFirstName;
	}

	/**
	 * @see RAD_Person_Interface::getNameInsertions()
	 */
	public function getNameInsertions()
	{
		return $this->m_szNameInsertions;
	}

	/**
	 * @see RAD_Person_Interface::putNameInsertions()
	 */
	public function putNameInsertions( $szNameInsertions )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::NameInsertions", $this->m_szNameInsertions, $szNameInsertions );
  			$this->notifyObservers( $objObjectChange );
		}
		$this->m_szNameInsertions = $szNameInsertions;
	}

	/**
	* @see RAD_Person_Interface::getNameInsertionsPartner()
	*/
	public function getNameInsertionsPartner()
	{
		return $this->m_szNameInsertionsPartner;
	}

	/**
	 * @see RAD_Person_Interface::putNameInsertionsPartner()
	 */
	public function putNameInsertionsPartner( $szNameInsertionsPartner )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::NameInsertionsPartner", $this->m_szNameInsertionsPartner, $szNameInsertionsPartner );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_szNameInsertionsPartner = $szNameInsertionsPartner;
	}

	/**
	* @see RAD_Person_Interface::getLastName()
	*/
	public function getLastName()
	{
		return $this->m_szLastName;
	}

	/**
	 * @see RAD_Person_Interface::putLastName()
	 */
	public function putLastName( $szLastName )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::LastName", $this->m_szLastName, $szLastName );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_szLastName = $szLastName;
	}

	/**
	 * @see RAD_Person_Interface::getLastNamePartner()
	 */
	public function getLastNamePartner()
	{
		return $this->m_szLastNamePartner;
	}

	/**
	 * @see RAD_Person_Interface::putLastNamePartner()
	 */
	public function putLastNamePartner( $szLastNamePartner )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::LastNamePartner", $this->m_szLastNamePartner, $szLastNamePartner );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_szLastNamePartner = $szLastNamePartner;
	}

	/**
	* @see RAD_Person_Interface::getCallType()
	*/
	public function getCallType()
	{
		return $this->m_nCallType;
	}

	/**
	 * @see RAD_Person_Interface::putCallType()
	 */
	public function putCallType( $nCallType )
	{
		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::CallType", $this->m_nCallType, $nCallType );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_nCallType = $nCallType;
	}

	/**
	 * @see RAD_Person_Interface::getLastNameCalled()
	 */
	public function getLastNameCalled( $bForOrdering = false )
	{
		return RAD_Person_Factory::getLastNameCalled(
			$this->getNameInsertions(), $this->getLastName(),
			$this->getNameInsertionsPartner(), $this->getLastNamePartner(),
			$this->getCallType(), $bForOrdering
		);
	}

    /**
     * @see RAD_Person_Interface::getFullName()
     */
    public function getFullName( $nCallType = 0, $nMaxLength = null )
    {
        $sFullName = $this->getFullNameHelper( $nCallType );
        if( $nMaxLength === null ) {
            return $sFullName;
        }
        if( mb_strlen( $sFullName ) <= $nMaxLength ) {
            return $sFullName;
        }
        return mb_substr( $sFullName, 0, $nMaxLength - 2 ) . "..";
    }

	protected function getFullNameHelper( $nCallType = 0 )
	{
		if ( $nCallType === RAD_Person::CALLTYPE_FULLNAME_ORDER )
			return $this->getLastNameCalled( true ) . ", " . $this->getFirstName();
		else if ( $nCallType === RAD_Person::CALLTYPE_FULLNAME_FIRSTNAMELETTER )
			return strtoupper( mb_substr( $this->getFirstName(), 0, 1 ) ) . ". " . $this->getLastNameCalled( false );
		return $this->getFirstName() . " " . $this->getLastNameCalled( false );
	}

	/**
	* @see RAD_Person_Interface::getDateOfBirth()
	*/
	public function getDateOfBirth()
	{
		return $this->m_oDateOfBirth;
	}

	/**
	 * @see RAD_Person_Interface::putDateOfBirth()
	 */
	public function putDateOfBirth( $oDateOfBirth )
	{
		if ( $oDateOfBirth !== null and is_string( $oDateOfBirth ) )
			$oDateOfBirth = Agenda_Factory::createDateTime( $oDateOfBirth );

		if ( $this->m_bObserved === true )
		{
			$objObjectChange = MetaData_ObjectChange_Factory::createObjectChange( $this->getId(), get_called_class()."::DateOfBirth", $this->m_oDateOfBirth, $oDateOfBirth );
			$this->notifyObservers( $objObjectChange );
		}
		$this->m_oDateOfBirth = $oDateOfBirth;
	}
}
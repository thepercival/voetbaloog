<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Team_Membership_StaffMember_Db_Reader extends Source_Db_Reader implements Voetbal_Team_Membership_Db_Reader_Interface
{
	public function __construct( $objFactory )
	{
		parent::__construct( $objFactory );
	}

	/**
	 * @see Reader_Interface::getObjectPropertiesToRead()
	 */
	public function getObjectPropertiesToRead(): Patterns_Collection
	{
		if ( $this->m_objObjectProperties === null )
		{
			$this->m_objObjectProperties = Patterns_Factory::createCollection();
			$objObjectPropertiesToAdd = MetaData_Factory::createObjectPropertiesExt( "Voetbal_Team_Membership_StaffMember", "Id", "Provider", "Client", "FunctionX", "Importance", "StartDateTime", "EndDateTime" );
			$this->m_objObjectProperties->addCollection( $objObjectPropertiesToAdd );
		}
		return $this->m_objObjectProperties;
	}

	/**
	 * @see Voetbal_Team_Membership_Db_Reader_Interface::getPicture()
	 */
	public function getPicture( $nId )
	{
		$sQuery = "SELECT Picture ".$this->getTableName()." WHERE Id = ".Source_Db::getParamName( $this->getDbType(), 0 );

		$stmt = $this->m_objDatabase->prepare( $sQuery );
		$stmt->execute( array( $nId ) );

		if ( $row = $stmt->fetch() )
			return $row["Picture"];
		return null;
	}
}
<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 921 2014-08-28 18:49:24Z thepercival $
 * @package		Voetbal
 */


/**
 * @package Voetbal
 */
class Voetbal_Team_Membership_Player_Db_Reader extends Source_Db_Reader implements Voetbal_Team_Membership_Db_Reader_Interface
{
	public function __construct( $objFactory )
	{
		parent::__construct( $objFactory );

		$this->addPersistance( Voetbal_Person_Factory::createDbPersistance() );
        $this->addPersistance( Voetbal_Team_Factory::createDbPersistance() );
	}

	/**
	 * @see Source_Db_Reader::getSelectFrom()
	 */
    protected function getSelectFrom( $bCount = false )
    {
        $sTablePersons = Voetbal_Person_Db_Persistance::getTable()->getName();
        $sTableTeams = Voetbal_Team_Db_Persistance::getTable()->getName();

        $oSelect = parent::getSelectFrom( $bCount );
        $oSelect->join(array( $sTablePersons ), $this->getTableName().".PersonId = ".$sTablePersons.".Id", array() );
        $oSelect->join(array( $sTableTeams ), $this->getTableName().".TeamId = ".$sTableTeams.".Id", array() );
        return $oSelect;
    }

	/**
	 * @see Reader_Interface::getObjectPropertiesToRead()
	 */
	public function getObjectPropertiesToRead(): Patterns_Collection
	{
		if ( $this->m_objObjectProperties === null )
		{
			$this->m_objObjectProperties = Patterns_Factory::createCollection();
			$objObjectPropertiesToAdd = MetaData_Factory::createObjectPropertiesExt( "Voetbal_Team_Membership_Player", "Id", "Provider", "Client", "Line", "BackNumber", "StartDateTime", "EndDateTime", "ExternId" );
			$this->m_objObjectProperties->addCollection( $objObjectPropertiesToAdd );
		}
		return $this->m_objObjectProperties;
	}

	// add join to persion

	/**
	 * @see Voetbal_Team_Membership_Db_Reader_Interface::getPicture()
	 */
	public function getPicture( $nId )
	{
		$sQuery = "SELECT Picture FROM ".$this->getTableName()." WHERE Id = ".Source_Db::getParamName( $this->getDbType(), 0 );

		$stmt = $this->m_objDatabase->prepare( $sQuery );
		$stmt->execute( array( $nId ) );

		if ( $row = $stmt->fetch() )
			return $row["Picture"];
		return null;
	}
}
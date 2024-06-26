<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Reader.php 4557 2019-08-12 18:50:59Z thepercival $
 * @since	  File available since Release 4.0
 * @package	Auth
 */

/**
 * @package Auth
 */
class RAD_Auth_Action_Db_Reader extends Source_Db_Reader
{
	public function __construct( $objFactory )
	{
		parent::__construct( $objFactory );

		$this->addPersistance( RAD_Auth_Role_Factory::createDbPersistance() );
	}

	/**
	 * @see Source_Db_Reader_Interface::getQuery()
	 */
    public function getQuery( Construction_Option_Collection $oOptions = null ): Zend_Db_Select
	{
		$objSelect = parent::getQuery( $oOptions );
		$objSelect->joinLeft(array("ActionsPerRole" => "ActionsPerRole"), $this->getTableName().".Name = ActionsPerRole.ActionName", array() )
			->joinLeft(array("Roles" => "Roles"), "ActionsPerRole.RoleName = Roles.Name", array() );
		return $objSelect;
	}

	/**
	 * @see Source_Db_Reader_Interface::createObjects()
	 */
	public function createObjects( Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		$objActions = $this->m_objFactory->createObjects();

		$oSelect = $this->getQuery( $oOptions );

		try
		{
			$stmt = $this->m_objDatabase->prepare( $oSelect );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			while ( $row = $stmt->fetch() )
			{
				$objAction = $this->m_objFactory->createObject();
				$objAction->putId( $row["ModuleName"]."-".$row["Name"] );
				$objAction->putName( $row["Name"] );
				$objAction->putModule( $row["ModuleName"] );

				$objActions->add( $objAction );
			}
		}
		catch ( Exception $e)
		{
			throw new Exception( $e->getMessage().", For Query: ".(string) $oSelect, E_ERROR );
		}
		return $objActions;
	}
}
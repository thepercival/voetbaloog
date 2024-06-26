<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: ActionWriter.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package Auth
 */
class RAD_Auth_Role_Db_ActionWriter extends Source_Db_Writer
{
	protected $m_objRole;

	public function __construct( $objRole, $objFactory )
	{
		parent::__construct( $objFactory );

		$this->m_objRole = $objRole;
	}

	/**
	 * @see Source_Db_Writer::add()
	 */
	protected function add( $oObjectChange, $oStmt = null )
	{
		$objAction = $oObjectChange->getObject();

		$szInsertQuery =
			"INSERT INTO ActionsPerRole".
			"( RoleName, ActionModuleName, ActionName ) ".
			"VALUES( ".
			$this->toSqlString( $this->m_objRole ).", ".
			$this->toSqlString( $objAction->getModule() ).", ".
			$this->toSqlString( $objAction->getName() )." )";

		try
		{
			$this->m_objDatabase->query( $szInsertQuery );
		}
		catch ( Exception $e)
		{
			throw new Exception( $e->getMessage().", For Query: ".$szInsertQuery, E_ERROR );
		}

		return true;
	}

	/**
	 * @see Source_Db_Writer::getPersistance()
	 */
	protected function getPersistance()
	{
		return null;
	}

	/**
	 * @see Source_Db_Writer::getTableName()
	 */
	protected function getTableName()
	{
		return "ActionsPerRole";
	}

	protected function delete( $objObjectChange )
	{
		throw new Exception( "deleteActionsPerRole needs implementing", E_ERROR );
	}

	protected function update( $objObjectChange )
	{
		throw new Exception( "updateActionsPerRole needs implementing", E_ERROR );
	}
}
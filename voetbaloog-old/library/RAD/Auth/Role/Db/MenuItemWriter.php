<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: MenuItemWriter.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 *
 * @package    Auth
 */


/**
 *
 *
 * @package Auth
 */
class RAD_Auth_Role_Db_MenuItemWriter extends Source_Db_Writer
{
	protected $m_objRole;

	public function __construct( $objRole, $objFactory )
	{
		parent::__construct( $objFactory );

		$this->m_objRole = $objRole;
	}

	/**
	 *
	 * @see Source_Db_Writer::add()
	 */
	public function writeExt( $szModule, $objMenuItem )
	{
		$this->writeExtHelper( $szModule, $objMenuItem->getChildren() );
	}

	/**
	 *
	 * @see Source_Db_Writer::add()
	 */
	public function writeExtHelper( $sModule, $oMenuItems )
	{
		foreach( $oMenuItems as $oMenuItem )
		{
			$sInsertQuery =
				"INSERT INTO MenuItemsPerRole".
				"( RoleName, MenuItemModuleName, MenuItemName ) ".
				"VALUES( ".
					$this->toSqlString( $this->m_objRole->getId() ).", ".
					$this->toSqlString( $sModule ).", ".
					$this->toSqlString( $oMenuItem->getId() ).
				" )";

            try
            {
                $this->m_objDatabase->query( $sInsertQuery );
            }
            catch ( Exception $e)
            {
                throw new Exception( "for query " . $sInsertQuery . " error is : " . $e->getMessage(), E_ERROR );
            }

			$this->writeExtHelper( $sModule, $oMenuItem->getChildren() );
		}
	}

	/**
	 * @see Source_Db_Writer::add()
	 */
	protected function add( $oObjectChange, $oStmt = null )
	{
		throw new Exception( "addMenuItemsPerRole needs implementing", E_ERROR );
	}

	/**
	 * Defined by Source_Db_Writer; gets the persistance
	 *
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
		return "MenuItemsPerRole";
	}

	protected function delete( $objObjectChange )
	{
		throw new Exception( "deleteMenuItemsPerRole needs implementing", E_ERROR );
	}

	protected function update( $objObjectChange )
	{
		throw new Exception( "updateMenuItemsPerRole needs implementing", E_ERROR );
	}
}
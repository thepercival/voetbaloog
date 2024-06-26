<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Writer.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package Auth
 */
class RAD_Auth_MenuItem_Db_Writer extends Source_Db_Writer
{
	public function __construct( $objFactory )
	{
		parent::__construct( $objFactory );
	}

	/**
	 *
	 * @see Source_Db_Writer::add()
	 */
	public function writeExt( $szModule, $objMenuItem )
	{
		$objMenuItems = $objMenuItem->getChildren();

        $szInsertQuery =
            "INSERT INTO ".$this->getTableName().
            "( ModuleName, Name, Description ) ".
            "VALUES( ".
            $this->toSqlString( $szModule ).", ".
            $this->toSqlString( "root" ).", ".
            $this->toSqlString( $szModule ).
            " )";

		try {
			$this->m_objDatabase->query( $szInsertQuery );
		}
		catch ( Exception $e) {
			throw new Exception( $e->getMessage().", For Query: ".$szInsertQuery, E_ERROR );
		}

		try
		{
			$this->writeExtHelper( $szModule, $objMenuItems );
			return true;
		}
		catch ( Exception $e) {
			throw new Exception( $e->getMessage(), E_ERROR );
		}

		return false;
	}

	/**
	 *
	 * @see Source_Db_Writer::add()
	 */
	protected function writeExtHelper( $szModule, $objMenuItems, $objParentMenuItem = null )
	{
		$nOrder = 0;
		foreach( $objMenuItems as $objMenuItem )
		{
            $szInsertQuery = null;
			try
			{
				{
					$szActionId = $objMenuItem->getAction();
					$szActionModule = null;
					if ( $szActionId !== null )
						$szActionModule = $szModule;

					$szInsertQuery =
						"INSERT INTO ".$this->getTableName().
						"( ModuleName, Name, Description, ActionModuleName, ActionName ) ".
						"VALUES( ".
							$this->toSqlString( $szModule ).", ".
							$this->toSqlString( $objMenuItem->getId() ).", ".
							$this->toSqlString( $objMenuItem->getDescription() ).", ".
							$this->toSqlString( $szActionModule ).", ".
							$this->toSqlString( $szActionId ).
						" )";

					$this->m_objDatabase->query( $szInsertQuery );
				}

				$szParentId = "root";
				if ( $objParentMenuItem !== null )
					$szParentId = $objParentMenuItem->getId();

				{
					$szInsertQuery =
						"INSERT INTO MenuItemHierarchy".
						"( ParentMenuItemModuleName, ParentMenuItemName, ChildMenuItemModuleName, ChildMenuItemName, ".$this->m_objDatabase->quoteIdentifier( "Order" )." ) ".
						"VALUES( ".
							$this->toSqlString( $szModule ).", ".
							$this->toSqlString( $szParentId ).", ".
							$this->toSqlString( $szModule ).", ".
							$this->toSqlString( $objMenuItem->getId() ).", ".
							$this->toSqlString( ++$nOrder ).
						" )";

					$this->m_objDatabase->query( $szInsertQuery );
				}
			}
			catch ( Exception $e)
			{
				throw new Exception( $e->getMessage().", For Query: ".$szInsertQuery, E_ERROR );
			}
			$this->writeExtHelper( $szModule, $objMenuItem->getChildren(), $objMenuItem );
		}
	}

	protected function add( $oObjectChange, $oStmt = null )
	{
		throw new Exception( __FILE__."Not implemented!", E_ERROR );
	}
}
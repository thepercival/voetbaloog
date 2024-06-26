<?php

/**
 * @copyright2007 Coen Dunnink
 * @licensehttp://www.gnu.org/licenses/gpl.txt
 * @version$Id: Reader.php 4559 2019-08-13 09:57:58Z thepercival $
 * @sinceFile available since Release 4.0
 * @packageAuth
 */

/**
 * @packageAuth
 */
class RAD_Auth_MenuItem_Db_Reader extends Source_Db_Reader implements RAD_Auth_MenuItem_Db_Reader_Interface
{
	public function __construct( $objFactory )
	{
		parent::__construct( $objFactory );
	}

	/**
	 * @see RAD_Auth_MenuItem_Db_Reader_Interface::getMenuItems()
	 */
	public function getMenuItems( $oRoles, $szModuleName, $szMenuItemName )
	{
		$objMenuItem = $this->m_objFactory->createObject();
		$objMenuItem->putId( $szMenuItemName );
		$objMenuItem->putDescription( $szMenuItemName );

		$this->getChildren( $oRoles, $szModuleName, $objMenuItem );

		return $objMenuItem;
	}

    /**
     * @param Patterns_Collection|null $oRoles
     * @param string $szModuleName
     * @param RAD_Auth_MenuItem $objMenuItem
     * @throws Zend_Exception
     */
	protected function getChildren( $oRoles, string $szModuleName, RAD_Auth_MenuItem $objMenuItem )
	{
		$objChildMenuItems = $objMenuItem->getChildren();

		$szRoleJoin = null;
		$szRoleFilter = null;
		if ( $oRoles !== null )
		{
			$szRoleJoin = "									JOIN MenuItemsPerRole MPR ON M.ModuleName = MPR.MenuItemModuleName AND M.Name = MPR.MenuItemName ";
			$szRoleFilter = "AND		MPR.RoleName IN ".$this->toSqlString( $oRoles )." ";
		}

		$objDatabase = Zend_Registry::get("db");

		$query = 	"SELECT		DISTINCT M.Name AS Name, M.Description AS Description, M.ActionName AS ActionName, MH.".$objDatabase->quoteIdentifier( "Order" )." ".
					"FROM 		MenuItemHierarchy MH 	JOIN MenuItems M ON MH.ChildMenuItemModuleName = M.ModuleName AND MH.ChildMenuItemName = M.Name ".
					$szRoleJoin.
					"WHERE 		ParentMenuItemName = ".$this->toSqlString( $objMenuItem )." ".
					$szRoleFilter.
					"AND		M.ModuleName = ".$this->toSqlString( $szModuleName )." ".
					"ORDER BY	MH.".$objDatabase->quoteIdentifier( "Order" );

		$stmt = $objDatabase->query( $query );
		while ( $row = $stmt->fetch() )
        {
            $objMenuItem = $this->m_objFactory->createObject();

            $objMenuItem->putId( $row["Name"] );
            $objMenuItem->putDescription( $row["Description"] );
            $objMenuItem->putAction( $row["ActionName"] );

            $this->getChildren( $oRoles, $szModuleName, $objMenuItem );

            $objChildMenuItems->add( $objMenuItem );
        }
	}
}
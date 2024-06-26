<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Reader.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
class RAD_Auth_User_Db_Reader extends Source_Db_Reader implements RAD_Auth_User_Db_Reader_Interface
{
	public function __construct( $objFactory )
	{
		parent::__construct( $objFactory );
	}

	/**
	 * @see RAD_Auth_User_Db_Reader_Interface::createObjectsForRole()
	 */
	public function createObjectsForRole( $objRole, $oOptions = null )
	{
		$this->addPersistance( RAD_Auth_Role_Factory::createDbPersistance() );

		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();
		if ( $objRole !== null )
			$oOptions->addFilter( "RAD_Auth_Role::Id", "EqualTo", $objRole );

		$objSelect = $this->m_objDatabase->select();

		$objSelect->distinct()
			->from(array("UsersPerRole" => "UsersPerRole"), array() )
			->join(array( $this->getTableName() => $this->getTableName() ), "UsersPerRole.UserId = ".$this->getTableName().".Id" )
			->join(array("Roles" => "Roles"), "UsersPerRole.RoleName = Roles.Name", array() )
		;

		$this->addWhereOrderBy( $objSelect, $oOptions );

		return $this->createObjectsHelper( $objSelect, $this->getCustomReadProperties( $oOptions ) );
	}
}
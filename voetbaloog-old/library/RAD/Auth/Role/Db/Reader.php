<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Reader.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
class RAD_Auth_Role_Db_Reader extends Source_Db_Reader implements Source_Reader_Ext_Interface
{
	public function __construct( $objFactory )
	{
		parent::__construct( $objFactory );
	}

	/**
	 * @see Source_Reader_Ext_Interface::createObjectsExt()
	 */
    public function createObjectsExt( $oObject, Construction_Option_Collection $oOptions = null, $sClassName = null ): Patterns_Collection
	{
		if ( $oObject === null )
			throw new Exception( "Object can not be null", E_ERROR );

		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		$oSelect = $this->m_objDatabase->select();

		if ( $oObject instanceof RAD_Auth_User )
		{
			$this->addPersistance( RAD_Auth_User_Factory::createDbPersistance() );
			$oOptions->addFilter( "RAD_Auth_User::Id", "EqualTo", $oObject );

			$oSelect->distinct()
				->from(array( "UsersPerRole" => "UsersPerRole"), array() )
				->join(array( $this->getTableName() => $this->getTableName() ), "UsersPerRole.RoleName = ".$this->getTableName().".Name" )
				->join(array( "UsersExt" => "UsersExt"), "UsersPerRole.UserId = UsersExt.Id", array() )
			;
		}
		else if ( $oObject instanceof RAD_Auth_Action )
		{
			// can be added through options
			$this->addPersistance( RAD_Auth_User_Factory::createDbPersistance() );

			$this->addPersistance( RAD_Auth_Action_Factory::createDbPersistance() );
			$oOptions->addFilter( "RAD_Auth_Action::Name", "EqualTo", $oObject->getName() );
			$oOptions->addFilter( "RAD_Auth_Action::Module", "EqualTo", $oObject->getModule() );

			$oSelect->distinct()
				->from(array( "ActionsPerRole" => "ActionsPerRole"), array() )
				->join(array( $this->getTableName() => $this->getTableName() ), "ActionsPerRole.RoleName = ".$this->getTableName().".Name" )
				->join(array( "Actions" => "Actions"), "ActionsPerRole.ActionName = Actions.Name", array() )
				->join(array( "UsersPerRole" => "UsersPerRole"), "UsersPerRole.RoleName = ".$this->getTableName().".Name", array() )
				->join(array( "UsersExt" => "UsersExt"), "UsersPerRole.UserId = UsersExt.Id", array() )
			;
		}
		else
			throw new Exception( "Object has no correct value", E_ERROR );

		$this->addWhereOrderBy( $oSelect, $oOptions );

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}
}
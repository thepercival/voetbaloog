<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Reader.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package Auth
 */
abstract class RAD_Auth_Db_Reader extends Source_Db_Reader implements RAD_Auth_Db_Reader_Interface
{
	public function __construct( $oFactory )
  	{
  		parent::__construct( $oFactory );
  	}

	/**
	 * @see RAD_Auth_Db_Reader_Interface::createAuthorizedObjects()
	 */
    public function createAuthorizedObjects( RAD_Auth_User $oUser, $oRole, Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		$this->addPersistance( RAD_Auth_User_Factory::createDbPersistance() );
		$this->addPersistance( RAD_Auth_Role_Factory::createDbPersistance() );

		if ( $oOptions === null )
			$oOptions = Construction_Factory::createOptions();

		if ( $oUser !== null )
			$oOptions->addFilter( "RAD_Auth_User::Id", "EqualTo", $oUser );

		$bRoleFilterAdded = false;
		if ( $oRole !== null )
		{
			if ( $oRole instanceof Patterns_Collection and $oRole->count() > 0 )
			{
				$oOrOptions = Construction_Factory::createOptions();
				$oOrOptions->putId( "__OR__ROLE" );
				foreach( $oRole as $oRoleIt )
					$oOrOptions->addFilter( "RAD_Auth_Role::Id", "EqualTo", $oRoleIt );

				$oOptions->add( $oOrOptions );
				$bRoleFilterAdded = true;
			}
			else if ( ( $oRole instanceof RAD_Auth_Role ) or is_string( $oRole ) )
			{
				$oOptions->addFilter( "RAD_Auth_Role::Id", "EqualTo", $oRole );
				$bRoleFilterAdded = true;
			}
		}

		if ( $bRoleFilterAdded !== true )
			$oOptions->addFilter( "RAD_Auth_Role::Id", "EqualTo", null );

		$oSelect = $this->getSelectForAuthorizedObjects();

		$this->addWhereOrderBy( $oSelect, $oOptions );

		return $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
	}

	protected abstract function getSelectForAuthorizedObjects();
}



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
class RAD_Auth_Role_XML_Reader extends Source_XML_Reader
{
	public function __construct( $objFactory )
	{
		parent::__construct();

		$this->m_objFactory = $objFactory;

		$objObjectProperties = parent::getObjectPropertiesToRead();
		$objObjectPropertiesToAdd = MetaData_Factory::createObjectPropertiesExt( "RAD_Auth_Role", "Id" );
		$objObjectProperties->addCollection( $objObjectPropertiesToAdd );
	}

	/**
	 * @see Reader_Interface::createObjects()
	 */
	public function createObjects( Construction_Option_Collection $oOptions = null): Patterns_Collection
	{
		$objXML = $this->getSource();

		$objRoles = $this->m_objFactory->createObjects();

		$oRoleGuest = RAD_Auth_Role_Factory::getGuest();

		$szModule = $this->getModuleName( $oOptions );

		foreach ( $objXML->children() as $xmlRole )
		{
			$objRole = $this->m_objFactory->createObject();

			$sRoleName = (string) $xmlRole->Name;
			if ( $sRoleName !== "superadmin" and $sRoleName !== "soap" and $sRoleName !== $oRoleGuest->getId() )
				$sRoleName = $szModule . "_" . $sRoleName;
			$objRole->putId( $sRoleName );
			$objRole->putSystem( ( (string) $xmlRole->System ) === "Y" );

			foreach ( $xmlRole->children() as $xmlRoleProperty )
			{
				$objActions = $objRole->getActions( $szModule );

				if ( $xmlRoleProperty->getName() === "Actions" )
				{
					if ( (string) $xmlRoleProperty->All === "Y" )
					{
						$objActions->addCollection( RAD_Auth_Action_Factory::createObjectsFromDatabase() );
					}
					else
					{
						$objOptions = Construction_Factory::createOptions();
						$objOptions->addFilter( "RAD_Auth_Action::Module", "EqualTo", $szModule );
						$objActions->addCollection( RAD_Auth_Action_Factory::createObjectsFromXML( $xmlRoleProperty, $objOptions ) );
					}
				}
				else if ( $xmlRoleProperty->getName() === "MenuItems" )
				{
					if ( (string) $xmlRoleProperty->All === "Y" )
					{
						$objRole->putRootMenuItem( RAD_Auth_MenuItem_Factory::getRootMenuItem( null, $szModule ) );
					}
					else
					{
						$objRole->putRootMenuItem( RAD_Auth_MenuItem_Factory::createObjectFromXML( $xmlRoleProperty ) );
					}
				}
			}

			$objRoles->add( $objRole );
		}

		return $objRoles;
	}

	protected function getModuleName( $oOptions )
	{
		if ( $oOptions !== null and $oOptions->count() === 1
			and $oOptions->first()->getObjectProperty() === "RAD_Auth_Role::Module"
		)
			return $oOptions->first()->getValue();
		return null;
	}
}
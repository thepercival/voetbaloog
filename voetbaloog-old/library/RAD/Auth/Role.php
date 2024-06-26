<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Role.php 4261 2015-12-23 21:07:47Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
class RAD_Auth_Role implements RAD_Auth_Role_Interface, Patterns_Idable_Interface
{
	protected $m_bSystem;
	protected $m_objActions;		// Patterns_Collection_Interface
	protected $m_objRootMenuItem; 	// RAD_Auth_MenuItem_Interface

	use Patterns_Idable_Trait;

	/**
	 * Defined by RAD_Auth_Role_Interface; gets the system property, tells if the role can be changed by anyone
	 *
	 * @see RAD_Auth_Role_Interface::getSystem()
	 */
  	public function getSystem()
  	{
  		return $this->m_bSystem;
  	}

  	/**
	 * Defined by RAD_Auth_Role_Interface; puts the system property, tells if the role can be changed by anyone
	 *
	 * @see RAD_Auth_Role_Interface::putSystem()
	 */
	public function putSystem( $bSystem )
	{
		$this->m_bSystem = $bSystem;
	}

	/**
	 * Defined by RAD_Auth_Role_Interface; gets the actions for this user
	 *
	 * @see RAD_Auth_Role_Interface::getActions()
	 */
  	public function getActions( $szModuleName )
  	{
  		if ( $this->m_objActions === null )
  		{
  			$objFilters = Construction_Factory::createOptions();
	    	$objFilters->addFilter( "RAD_Auth_Action::Module", "EqualTo", $szModuleName );
	    	$objFilters->addFilter( "RAD_Auth_Role::Id", "EqualTo", $this );
	    	$this->m_objActions = RAD_Auth_Action_Factory::createObjectsFromDatabase( $objFilters );
  		}
  		return $this->m_objActions;
  	}

	/**
	 * Defined by RAD_Auth_Role_Interface; gets the actions for this user
	 *
	 * @see RAD_Auth_Role_Interface::getRootMenuItem()
	 */
  	public function getRootMenuItem( $szModuleName )
  	{
  		if ( $this->m_objRootMenuItem === null )
  		{
  			$objRoles = RAD_Auth_Role_Factory::createObjects();
  			$objRoles->add( $this );
	    	$this->m_objRootMenuItem = RAD_Auth_MenuItem_Factory::getRootMenuItem( $objRoles, $szModuleName );
  		}
  		return $this->m_objRootMenuItem;
  	}

	/**
	 * @see RAD_Auth_Role_Interface::putRootMenuItem()
	 */
  	public function putRootMenuItem( $objRootMenuItem )
  	{
  		$this->m_objRootMenuItem = $objRootMenuItem;
  	}
}
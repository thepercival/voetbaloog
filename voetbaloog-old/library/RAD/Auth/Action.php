<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Action.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package Auth
 */
class RAD_Auth_Action implements RAD_Auth_Action_Interface, Patterns_Idable_Interface
{
	// RAD_Auth_Action_Interface
	protected $m_sName;  	// string
	protected $m_sModule;  	// string

	use Patterns_Idable_Trait;

  	/**
	 * @see RAD_Auth_Action_Interface::getName()
	 */
	public function getName()
	{
		return $this->m_sName;
	}

	/**
	 * @see RAD_Auth_Action_Interface::putName()
	 */
	public function putName( $sName )
	{
		$this->m_sName = $sName;
	}

	/**
	 * @see RAD_Auth_Action_Interface::getModule()
	 */
	public function getModule()
	{
		return $this->m_sModule;
	}

	/**
	 * @see RAD_Auth_Action_Interface::putModule()
	 */
	public function putModule( $sModule )
	{
		$this->m_sModule = $sModule;
	}
}
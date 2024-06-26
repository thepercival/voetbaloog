<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: ObjectChange.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    MetaData
 */

/**
 * @package    MetaData
 */
class MetaData_ObjectChange implements MetaData_ObjectChange_Interface, Patterns_Idable_Interface
{
	// MetaData_ObjectChange_Interface
	protected $m_szEntityName;  	// string
	protected $m_nAction;  			// int
	protected $m_szSystemId;  		// string
	// add/Remove
	protected $m_objObject;  		// Patterns_Idable_Interface
	// Update
	protected $m_objObjectProperty; // Patterns_Idable_Interface
	protected $m_vtOldValue;  		// variant
	protected $m_vtNewValue;  		// variant

	use Patterns_Idable_Trait;

	/**
	 * @see MetaData_ObjectChange_Interface::getEntityName()
	 */
	public function getEntityName()
	{
		return $this->m_szEntityName;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::putEntityName()
	 */
	public function putEntityName( $szEntityName )
	{
		$this->m_szEntityName = $szEntityName;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::getActionName()
	 */
	public function getActionName()
	{
		return $this->m_nAction;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::putActionName()
	 */
	public function putActionName( $nAction )
	{
		$this->m_nAction = $nAction;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::getSystemId()
	 */
	public function getSystemId()
	{
		return $this->m_szSystemId;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::putSystemId()
	 */
	public function putSystemId( $szSystemId )
	{
		$this->m_szSystemId = $szSystemId;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::getObject()
	 */
	public function getObject()
	{
		return $this->m_objObject;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::putObject()
	 */
	public function putObject( $objObject )
	{
		$this->m_objObject = $objObject;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::getObjectProperty()
	 */
	public function getObjectProperty()
	{
		return $this->m_objObjectProperty;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::putObjectProperty()
	 */
	public function putObjectProperty( $objObjectProperty )
	{
		$this->m_objObjectProperty = $objObjectProperty;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::getOldValue()
	 */
	public function getOldValue()
	{
		return $this->m_vtOldValue;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::putOldValue()
	 */
	public function putOldValue( $vtOldValue )
	{
		$this->m_vtOldValue = $vtOldValue;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::getNewValue()
	 */
	public function getNewValue()
	{
		return $this->m_vtNewValue;
	}

	/**
	 * @see MetaData_ObjectChange_Interface::putNewValue()
	 */
	public function putNewValue( $vtNewValue )
	{
		$this->m_vtNewValue = $vtNewValue;
	}
}
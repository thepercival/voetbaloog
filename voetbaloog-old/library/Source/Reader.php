<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Reader.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 * @package	   Source
 */

/**
 * @package Source
 */
class Source_Reader implements Source_Reader_Interface
{
	protected $m_objFactory;	
	// Source_Reader_Interface
	protected $m_objObjectProperties;
	
	public function __construct() 	{}

  	/**
	 * @see Source_Reader_Interface::getObjectPropertiesToRead()
	 */
	public function getObjectPropertiesToRead(): Patterns_Collection
	{
		if ( $this->m_objObjectProperties === null )
			$this->m_objObjectProperties = Patterns_Factory::createCollection();
		return $this->m_objObjectProperties;
	}

	/**
	 * @see Source_Reader_Interface::createObjects()
	 */
	public function createObjects( Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		throw new Exception( "This function should be implemented(".__FILE__.",".__LINE__.")", E_ERROR );
	}
	
	/**
	 * @see Source_Reader_Interface::createObject()
	 */
	public function createObject( Construction_Option_Collection $oOptions = null )
	{
		throw new Exception( "This function should be implemented(".__FILE__.",".__LINE__.")", E_ERROR );
	}
}
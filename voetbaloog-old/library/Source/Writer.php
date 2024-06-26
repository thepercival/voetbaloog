<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Writer.php 4261 2015-12-23 21:07:47Z thepercival $
 *
 * @package	   Source
 */

/**
 * @package Source
 */
class Source_Writer implements Source_Writer_Interface, Patterns_Idable_Interface
{
	protected $m_objObjectPropertiesToWrite;

	use Patterns_Idable_Trait;

	/**
	 * Constructs the class
	 */
	public function __construct()
	{
		$this->m_objObjectPropertiesToWrite = Patterns_Factory::createCollection();
	}

	/**
	 * @see Writer_Interface::getObjectPropertiesToWrite()
	 */
	public function getObjectPropertiesToWrite()
	{
		return $this->m_objObjectPropertiesToWrite;
	}

	/**
	 * @see Writer_Interface::write()
	 */
	public function write()
	{
		// throw new Exception("write should be inherited", E_ERROR);
	}

	/**
	 * @see Writer_Interface::removeObjects()
	 */
	public function removeObjects( $oOptions = null )
	{
		throw new Exception("removeObjects should be inherited", E_ERROR);
	}
}
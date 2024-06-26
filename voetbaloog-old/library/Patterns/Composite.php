<?php

/**
 *
 *
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Composite.php 4264 2015-12-23 22:41:38Z thepercival $
 *
 *
 * @package    Patterns
 */

/**
 * @package    Patterns
 */
class Patterns_Composite implements Patterns_Composite_Interface, Patterns_Idable_Interface
{
	protected $m_objChildren; 	// Patterns_Collection_Interface
	protected $m_objParent; 	// Patterns_Composite_Interface

	use Patterns_Idable_Trait;

	public function __construct(){}

	/**
	 * Defined by Patterns_Composite_Interface; gets the children of the composite object
	 *
	 * @see Patterns_Composite_Interface::getChildren()
	 */
	public function getChildren()
	{
		if ( $this->m_objChildren === null )
			$this->m_objChildren = Patterns_Factory::createCollection();
		
		return $this->m_objChildren;
	}

	/**
	 * Defined by Patterns_Composite_Interface; gets the parent of the composite object
	 *
	 * @see Patterns_Composite_Interface::getParent()
	 */
	public function getParent()
	{
		return $this->m_objParent;
	}

	/**
	 * Defined by Patterns_Composite_Interface; gets the parent of the composite object
	 *
	 * @see Patterns_Composite_Interface::putParent()
	 */
	public function putParent( $objParent )
	{
		$this->m_objParent = $objParent;
	}
}
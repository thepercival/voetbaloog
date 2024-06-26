<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: ObserverObject.php 4261 2015-12-23 21:07:47Z thepercival $
 *
 * @package    Patterns
 */

/**
 * @package Patterns
 */
class Patterns_ObserverObject implements Patterns_ObserverObject_Interface, Patterns_Idable_Interface
{
	protected $m_objObjectChanges;

	use Patterns_Idable_Trait;

	/**
	 *
	 */
	public function __construct()
	{
		$this->m_objObjectChanges = Patterns_Factory::createCollection();
	}

	/**
	 * @see Patterns_ObserverObject_Interface::addObjectChange()
	 */
	public function addObjectChange( $objObjectChange )
	{
		$bRetVal = $this->m_objObjectChanges->remove( $objObjectChange );
		$bRetVal = $this->m_objObjectChanges->add( $objObjectChange );
		return $bRetVal;
	}

	/**
	 * @see Patterns_ObserverObject_Interface::getObjectChanges()
	 */
	public function getObjectChanges()
	{
		return $this->m_objObjectChanges;
	}
}
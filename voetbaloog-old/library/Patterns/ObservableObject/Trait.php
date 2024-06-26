<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: ObservableObject.php 3853 2013-03-14 15:35:55Z cdunnink $
 *
 * @package    Patterns
 */

/**
 * @package Patterns
 */
trait Patterns_ObservableObject_Trait
{
	protected $m_oObservers;
	protected $m_bObserved = false;

	/**
	 * @see Patterns_ObservableObject_Interface::getObservers()
	 */
	public function getObservers()
	{
		if ( $this->m_oObservers === null )
			$this->m_oObservers = Patterns_Factory::createCollection();
		return $this->m_oObservers;
	}

	/**
	 * @see Patterns_ObservableObject_Interface::flushObservers()
	 */
	public function flushObservers()
	{
		$this->m_bObserved = false;
		$this->m_oObservers = Patterns_Factory::createCollection();
	}

	/**
	 * @see Patterns_ObservableObject_Interface::addObserver()
	 */
	public function addObserver( $oObserver )
	{
		$this->m_bObserved = true;
		return $this->getObservers()->add( $oObserver );
	}

	/**
	 * @see Patterns_ObservableObject_Interface::notifyObservers()
	 */
	public function notifyObservers( $oObjectChange )
	{
		$oObservers = $this->getObservers();
		foreach ( $oObservers as $oObserver )
		{
			if ( $oObserver instanceof Patterns_ObserverObject_Interface )
				$oObserver->addObjectChange( $oObjectChange );
		}
		return true;
	}
}
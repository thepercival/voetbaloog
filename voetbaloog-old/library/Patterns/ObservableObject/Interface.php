<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 *
 * @package    Patterns
 */

/**
 * @package    Patterns
 */
interface Patterns_ObservableObject_Interface
{
	/**
	 * gets the Observers
	 *
	 * @return Patterns_Collection_Interface	The Observers
	 */
	public function getObservers();
	/**
	 * sets the Observers to null
	 *
	 * @return null
	 */
	public function flushObservers();	
	/**
	 * adds an Observer
	 *
	 * @param Patterns_ObserverObject	$objObserver	the observer
	 * @return true or false
	 */
	public function addObserver( $objObserver );
	/**
	 * notify the observers when something happens
	 *
	 * @param MetaData_ObjectChange	$oObjectChange	the objectchange
	 * @return null
	 */
	public function notifyObservers( $oObjectChange );
}
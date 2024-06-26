<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 *
 * @package    Patterns
 */

/**
 *
 *
 * @package    Patterns
 */
interface Patterns_ObserverObject_Interface
{
	/**
	 * gets the objectchanges
	 *
	 * @return Patterns_Collection_Interface	The objectchanges
	 */
	public function getObjectChanges();
	/**
	 * This function is called by the observable. Adds an objectchange to the observer
	 *
	 * @param MetaData_ObjectChange_Interface	$objObjectChange	the objectchange
	 * @return null
	 */
	public function addObjectChange( $objObjectChange );
}
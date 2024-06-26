<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 * @package	   Source
 */

/**
 * @package Source
 */
interface Source_Reader_Interface
{
	/**
	 * gets the objectproperties which should be read
	 *
	 * @return Patterns_Collection the objectproperties which should be read
	 */
	public function getObjectPropertiesToRead(): Patterns_Collection;
	/**
	 * Gets the objects
	 *
	 * @param Construction_Option_Collection	$oOptions	The construction options
     * @throws Exception
	 * @return Patterns_Collection									A collection of instances
	 */
	public function createObjects( Construction_Option_Collection $oOptions = null ): Patterns_Collection;
	/**
	 * Creates an object depending on the construction options
	 *
	 * @param Construction_Option_Collection	$oOptions	The construction options
	 * @throws Exception
	 */
	public function createObject(Construction_Option_Collection $oOptions = null );
}
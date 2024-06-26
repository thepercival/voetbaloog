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
 * @package MetaData
 */

/**
 *
 *
 * @package MetaData
 */
interface MetaData_ObjectChange_Interface
{
	/**
	 * gets the EntityName
	 *
	 * @return string The EntityName
	 */
	public function getEntityName();
	/**
	 * puts the EntityName
	 *
	 * @param  string	$szEntityName	The EntityName of the class
	 * @return null
	 */
	public function putEntityName( $szEntityName );
	/**
	 * gets the ActionName
	 *
	 * @return string The ActionName
	 */
	public function getActionName();
	/**
	 * puts the ActionName
	 *
	 * @param  string	$szActionName	The ActionName of the class
	 * @return null
	 */
	public function putActionName( $szActionName );
	/**
	 * gets the SystemId
	 *
	 * @return string The SystemId
	 */
	public function getSystemId();
	/**
	 * puts the SystemId
	 *
	 * @param  string	$szSystemId	The SystemId of the class
	 * @return null
	 */
	public function putSystemId( $szSystemId );
	/**
	 * Returns the Object
	 *
	 * @return Patterns_Idable_Interface	The object that has changed
	 */
	public function getObject();
	/**
	 * puts the Object
	 *
	 * @param  Patterns_Idable_Interface	$objObject	The object that will be set
	 * @return null
	 */
	public function putObject( $objObject );
	/**
	 * Returns the Property
	 *
	 * @return Patterns_Idable_Interface	The objectproperty that has changed
	 */
	public function getObjectProperty();
	/**
	 * puts the ObjectProperty
	 *
	 * @param  Patterns_Idable_Interface	$objObjectProperty	The objectproperty that will be set
	 * @return null
	 */
	public function putObjectProperty( $objObjectProperty );
	/**
	 * Returns the old value
	 *
	 * @return mixed	The old value
	 */
	public function getOldValue();
	/**
	 * puts the old value
	 *
	 * @param  mixed	$vtOldValue		The old value that will be set
	 * @return null
	 */
	public function putOldValue( $vtOldValue );
	/**
	 * Returns the new value
	 *
	 * @return mixed	The new value
	 */
	public function getNewValue();
	/**
	 * puts the new value
	 *
	 * @param  mixed	$vtNewValue		The new value that will be set
	 * @return null
	 */
	public function putNewValue( $vtNewValue );
}
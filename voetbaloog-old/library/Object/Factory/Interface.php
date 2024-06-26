<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package	   Object
 */

/**
 * @package Object
 */
interface Object_Factory_Interface
{
	/**
	 * Creates a new instance of an object
	 *
	 * @return mixed A new instance of an object
	 */
	public static function createObject();
	/**
	 * Creates a collection of objects
	 *
	 * @return Patterns_Collection A collection of objects
	 */
	public static function createObjects();
	/**
	 * gets the pool
	 *
	 * @return Patterns_Collection	A collection
	 */
	public static function getPool();
	/**
	 * Creates a new instance of an object, if the id is in the pool and the pool is enabled than
	 * the object is returned from the pool
	 *
	 * @param mixed $vtId variant	the id by which the object is searched for
	 * @return mixed A new instance of an object
	 */
	public static function createObjectFromPool( $vtId );
}

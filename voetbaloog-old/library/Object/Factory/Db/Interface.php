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
interface Object_Factory_Db_Interface
{
	/**
	 * Creates an array from the database
	 *
	 * @param Construction_Option_Collection	$vtOptions	The constructionOptions
	 * @param bool								$bLowerCase	lowercase def=false
	 * @return array		An array of json objects
	 */
	public static function createArrayFromDatabase( $vtOptions = null, $bLowerCase = false );
	/**
	 * Creates an object from the database
	 *
	 * @param Construction_Option_Collection		$vtOptions	The constructionOptions
	 * @return Patterns_Idable_Interface		An instance of an object of class $szClassName
	 */
	public static function createObjectFromDatabase( $vtOptions = null );
	/**
	 * Creates a collection of objects from the database
	 *
	 * @param Construction_Option_Collection		$vtOptions	The Options
	 * @return Patterns_ObservableObject_Collection
	 */
	public static function createObjectsFromDatabase( $vtOptions = null );
	/**
	 * Removes objects from the database
	 *
	 * @param Patterns_Collection_Interface		$vtOptions	The Options
	 * @throws Exception
	 * @return bool 		true | false
	 */
	public static function removeObjectsFromDatabase( $vtOptions = null );
	/**
	 * Gets the number of objects
	 *
	 * @param Construction_Option_Collection		$oOptions	The Options
	 * @return int	The number of objects
	 */
	public static function getNrOfObjectsFromDatabase( $oOptions = null );
	/**
	 * Creates an instance of a dbreader_interface
	 *
	 * @return Source_Db_Reader_Interface	An instance of a dbreader_interface
	 */
	public static function createDbReader();
	/**
	 * Resets the dbreader instance
	 *
	 * @return null
	 */
	public function resetDbReader();
	/**
	 * Creates an instance of a Source_Writer_Interface
	 *
	 * @return Source_Writer_Interface	An instance of a Source_Writer_Interface
	 */
	public static function createDbWriter();
	/**
	 * gets the DbPersistance
	 *
	 * @return Source_Db_Persistance_Interface	A DbPersistance Instance
	 */
	public static function createDbPersistance();
	/**
	 * gets the id-property
	 *
	 * @return Source_Db_Persistance_Interface	A DbPersistance Instance
	 */
	public function getIdProperty();
}
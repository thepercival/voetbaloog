<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4584 2020-04-29 09:31:18Z thepercival $
 *
 * @package    Patterns
 */

/**
 * @package    Patterns
 */
interface Patterns_Collection_Interface
{
	/**
	 * inserts an item at the beginning of the collection
	 *
	 * @param  Patterns_Idable_Interface	$oItem	The item that is added to the Collection
	 * @return bool		true if the insertion went ok, else false
	 */
	public function insert( $oItem );
	/**
	 * adds an item to the collection
	 *
	 * @param  Patterns_Idable_Interface	$oItem	The item that is added to the Collection
	 * @return bool		true if the addition went ok, else false
	 */
	public function add( $oItem );
	/**
	 * adds a collection to the collection
	 *
	 * @param  Patterns_Collection_Interface	$objCollection	The items that are added to the Collection
	 * @return bool		true if the addition went ok, else false
	 */
	public function addCollection( $objCollection );
	/**
	 * removes an item from the collection
	 *
	 * @param  Patterns_Idable_Interface	$oItem	The item that is removed from the Collection
	 * @return bool		true if the addition went ok, else false
	 */
	public function remove( $oItem );
	/**
	 * removes a collection from the collection
	 *
	 * @param  Patterns_Collection	$oCollection	The items that are removed from the Collection
	 * @return bool		true if the removal went ok, else false
	 */
	public function removeCollection( Patterns_Collection $oCollection );
	/**
	 * flushes the collection
	 *
	 * @return bool		true if the flushing went ok
	 */
	public function flush();
	/**
	 * gets the first element of the collection
	 *
	 * @return Patterns_Idable_Interface		The first object in the collection
	 */
	public function first();
	/**
	 * gets the iterator of the reversed
	 *
	 * @return ArrayIterator		the iterator of the reversed
	 */
    public function getIteratorReversed(): ArrayIterator;
	/**
	 * reindexes the item
	 *
	 * @param	mixed                       $vtOldIndex					The old index
	 * @param	Patterns_Idable_Interface   $oItem	The item
	 * @return null
	 */
	public function reindexItem( $vtOldIndex, $oItem );
	/**
	* compare the objects in the collection by type
	*
	* @param 	Patterns_Collection $oCollection    the collection
	* @return 	bool true if all elements in the collection are identical, else false
	*/
	public function equals( $oCollection );
	/**
	 * convert the objects in the collection to string, a seperator can be set as parameter
	 *
	 * @param 	string		$sSeperator	The string which will be added after every conversion of an object
	 * @param 	bool		$bRemoveLastSeperator	if true removes the last seperator
	 * @return string		the converted string
	 */
	public function toString( $sSeperator = ",", $bRemoveLastSeperator = true );
}
<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Patterns
 */

/**
 * @package    Patterns
 */
interface Patterns_Factory_Interface
{
	/**
	 * creates a collection
	 *
	 * @return Patterns_Collection_Interface	The created collection
	 */
	public static function createCollection();
	/**
	 * creates a collection
	 *
	 * @return Patterns_ObservableObject_Collection	The created collection
	 */
	public static function createObservableCollection();
	/**
	 * creates an Idable
	 *
	 * @param 	string $szId	The id of the idable
	 * @return Patterns_Idable_Interface	The created Idable
	 */
	public static function createIdable( $szId );
	/**
	 * creates an Idable
	 *
	 * @param 	Patterns_Idable_Interface $oIdable	The idable
	 * @return Patterns_Collection_Idable	The created Idable Collection
	 */
	public static function createIdableCollection( Patterns_Idable_Interface $oIdable );
    /**
     * Creates a collection of idables
     *
     * @return 	Patterns_Collection     A collection of strings
     */
	public static function createCollectionExt( /* variable param list */ );
	/**
	 * creates a Collection
	 *
	 * @param 	int $nStart		The startnumber of the collection
	 * @param 	int $nEnd		The endnumber of the collection
	 * @param 	int $nModulus	The number for an interval. Default is 1
	 * @return Patterns_Collection_Interface	The created collection
	 */
	public static function createNumbers( $nStart, $nEnd, $nModulus = 1 );
	/**
	 * creates a Collection
	 *
	 * @param 	string $szStart		The startLetter of the collection
	 * @param 	string $szEnd		The endLetter of the collection
	 * @return Patterns_Collection_Interface	The created collection
	 */
	public static function createLetters( $szStart, $szEnd );
}

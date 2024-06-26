<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4609 2021-08-14 14:24:10Z thepercival $
 *
 * @package    Construction
 */

/**
 * @package    Construction
 */
interface Construction_Factory_Interface
{
	/**
	 * create a collection of Options
	 *
	 * @return  Construction_Option_Collection_Interface	Options
	 */
	public static function createOptions();
	/**
	 * creates a collection of filters
	 * 1, 2, 3, 4 are datetimes in chronologic order
	 * A, B and C are timeslots
	 *
	 * 1	2		3		4
	 * ______________________
	 * |	|		|		|
	 * |   _|___	|		|
	 * |  |	| A |	|		|
	 * |   -|---	|		|
	 * |	|	 ___|_		|
	 * |	|	| B	| |		|
	 * |	|	 ---|-		|
	 * |	|		|		|
	 *
	 * The timeslot is from 2 to 3
	 * When the second paramter is "ExcludeBeforeStart"	only B will be found.
	 * When the second paramter is "ExcludeAfterEnd" only A will be found.
	 * When the second paramter is "ExcludeBoth" nothing will be found.
	 *
	 * @param 	string  								$szClassName 		The class which is needed to create the filter
	 * @param 	Patterns_Collection | Agenda_TimeSlot | Agenda_DateTime	$oTimeSlots			The timeslot(s) where the filter is based on
	 * @param 	int  									$nRange 			Can be "ExcludeBeforeStart", "ExcludeAfterEnd" or "ExcludeBoth", default = ExcludeNone
	 * @param	bool									$bEndDateCanBeNull	default is false
	 * @return 	Construction_Option_Collection_Interface
	 */
	public static function createFiltersForTimeSlots( $szClassName, $oTimeSlots, $nRange = 0 /*Agenda_TimeSlot::EXCLUDE_NONE*/, $bEndDateCanBeNull = false );
	/**
	 * creates a readproperty
	 *
	 * @param  string					$sObjectProperty	The objectproperty to add
	 * @return  Construction_Option		A ConstructionOption
	 */
	public static function createReadProperty( $sObjectProperty );
	/**
	 * creates a filter
	 *
	 * @param  string					$szId			The id of the filter
	 * @return  Construction_Option_Filter_Interface	A ConstructionOptionFilter
	 */
	public static function createFilter( $szId );
	/**
	 * creates a filter
	 *
	 * @param  string					$szObjectProperty	The objectproperty
	 * @param  string					$szSearchOperator	The searchoperator for example "EqualTo"
	 * @param  mixed					$vtValue			The value of the filter
	 * @return  Construction_Option							A ConstructionOptionFilter
	 */
	public static function createFilterExt( $szObjectProperty, $szSearchOperator, $vtValue );
	/**
	 * creates a ordering
	 *
	 * @param  string			$szObjectProperty	The objectproperty
	 * @param  bool				$bDescending		descending or ascending
	 * @return  Construction_Option_Order_Interface	A ConstructionOptionOrder
	 */
	public static function createOrder( $szObjectProperty, $bDescending );
	/**
	 * creates a limit
	 *
	 * @param  int			$nCount		The count
	 * @param  int			$nOffSet	The offset
	 * @return  Construction_Option_Limit_Interface	A ConstructionOptionOrder
	 */
	public static function createLimit( $nCount, $nOffSet = null );
	/**
	 * create a collection of Orders
	 *
	 * @return  Construction_Option_Collection_Interface	A ConstructionOptionCollection
	 */
	public static function createOrders();
	/**
	 * creates a SearchOperator
	 *
	 * @return  Construction_SearchOperator_Interface	A SearchOperator
	 */
	public static function createSearchOperator();
	/**
	 * gets a SearchOperator
	 *
	 * @param 	string 	$szId		The id to look for in the searchoperators
	 * @return  Construction_SearchOperator_Interface	A SearchOperators
	 */
	public static function getSearchOperator( $szId );
	/**
	 * gets the SearchOperators
	 *
	 * @return  Patterns_Collection_Interface	The SearchOperators
	 */
	public static function getSearchOperators();
}
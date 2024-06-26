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
 * @package    Construction
 */

/**
 *
 *
 * @package    Construction
 */
interface Construction_Option_Collection_Interface
{
	/**
	 * adds a Construction_Option_Filter
	 *
	 * @param  string			$szObjectProperty	The objectproperty
	 * @param  string			$szSearchOperator	The searchoperator for example "EqualTo"
	 * @param  mixed			$vtValue			The value of the filter
	 * @return  Construction_Option_Filter_Interface	A ConstructionOptionFilter
	 */
	public function addFilter( $szObjectProperty, $szSearchOperator, $vtValue );
	/**
	 * adds a ConstructionOptionOrder
	 *
	 * @param  string			$szObjectProperty	The objectproperty
	 * @param  bool				$bDescending		descending or ascending
	 * @return Construction_Option_Order_Interface	A ConstructionOptionOrder
	 */
	public function addOrder( $szObjectProperty, $bDescending );
	/**
	 * adds a limit
	 *
	 * @param  int			$nCount		The maximum number of records
	 * @param  int			$nOffSet	The offset
	 * @return Construction_Option_Limit_Interface	A ConstructionOptionOrder
	 */
	public function addLimit( $nCount, $nOffSet = null );
}
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
interface Construction_Option_Filter_Interface
{
	/**
	 * gets the SearchOperator
	 *
	 * @return  Construction_SearchOperator_Interface	the SearchOperator
	 */
	public function getSearchOperator();
	/**
	 * puts the SearchOperator
	 *
	 * @param  Construction_SearchOperator_Interface		$objSearchOperator	The SearchOperator
	 * @return  null
	 */
  	public function putSearchOperator( $objSearchOperator );
  	/**
	 * gets the Value
	 *
	 * @return mixed	the Value
	 */
	public function getValue();
	/**
	 * puts the Value
	 *
	 * @param  mixed		$vtValue	The Value
	 * @return  null
	 */
	public function putValue( $vtValue );
}
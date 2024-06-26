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
 * @package    Construction
 */

/**
 *
 *
 * @package    Construction
 */
interface Construction_Option_Limit_Interface
{
	/**
	 * gets the count
	 *
	 * @return  int		Maximum number of records
	 */
	public function getCount();
	/**
	 * puts the count
	 *
	 * @param  int		$nCount	 Maximum number of records
	 * @return  null
	 */
	public function putCount( $nCount );
	/**
	 * gets the OffSet
	 *
	 * @return  int		Maximum number of records
	 */
	public function getOffSet();
	/**
	 * puts the OffSet
	 *
	 * @param  int		$nOffSet	 Maximum number of records
	 * @return  null
	 */
	public function putOffSet( $nOffSet );
}
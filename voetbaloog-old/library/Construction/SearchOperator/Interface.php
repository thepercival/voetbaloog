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
interface Construction_SearchOperator_Interface
{
	/**
	 * gets the description
	 *
	 * @return  string	the description
	 */
	public function getDescription();
	/**
	 * puts the description
	 *
	 * @param  string		$szDescription	The description
	 * @return  null
	 */
	public function putDescription( $szDescription );
	/**
	 * gets the number of parameters
	 *
	 * @return  int	the number of parameters
	 */
	public function getNumberOfParameters();
	/**
	 * puts the number of parameters
	 *
	 * @param	 int	$nNumberOfParameters	the number of parameters
	 * @return  null
	 */
	public function putNumberOfParameters( $nNumberOfParameters );
}
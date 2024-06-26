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
 * @package    Controls
 */

/**
 *
 *
 * @package    Controls
 */
interface Controls_CheckBoxes_Interface
{
	/**
	 * gets the objects which will be selected
	 *
	 * @return  Patterns_Collection the objects will be selected
	 */
	public function getObjectsToSelect();

	/**
	 * puts the objects which will be selected
	 *
	 * @param  Patterns_Collection	$objObjectsToSelect	the objects which will be selected
	 * @return null
	 */
	public function putObjectsToSelect( $objObjectsToSelect );
	/**
	 * gets the MaxNrOfColumns
	 *
	 * @return  int	 the maximal number of columns created
	 */
	public function getMaxNrOfColumns();
	/**
	 * puts the the maximal number of columns created
	 *
	 * @param  int		$nMaxNrOfColumns		the maximal number of columns
	 * @return null
	 */
	public function putMaxNrOfColumns( $nMaxNrOfColumns );
	/**
	 * gets the MaxNrOfItemsPerColumn
	 *
	 * @return  int	 the maximal number of items per column created
	 */
	public function getMaxNrOfItemsPerColumn();
	/**
	 * puts the the maximal number of items per column
	 *
	 * @param  int		$nMaxNrOfItemsPerColumns		the maximal number of items per column
	 * @return null
	 */
	public function putMaxNrOfItemsPerColumn( $nMaxNrOfItemsPerColumns );
	/**
	 * puts the collection which not will be shown
	 *
	 * @param  Patterns_Collection_Interface		$objFilterCollection	the collection which not will be shown
	 * @return null
	 */
	public function putFilterCollection( $objFilterCollection );

	/**
	 * puts the objectproperty which will be shown
	 *
	 * @param  string	$szObjectProperty	the objectproperty which will be shown
	 * @return null
	 */
	public function putObjectPropertyToShow( $szObjectProperty );
	/**
	 * adds an event
	 * for example addEvent( "onchange=\"document.myform.submit();\"" );
	 *
	 * @param string $szEvent	The event that wll be added
	 * @return  true if succeeded
	 */
	public function addEvent( $szEvent );
}
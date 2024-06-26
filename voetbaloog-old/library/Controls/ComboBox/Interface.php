<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Controls
 */

/**
 * @package    Controls
 */
interface Controls_ComboBox_Interface
{
	/**
	 * disabled the combobox
	 *
	 * @return null
	 */
	public function disable();
	/**
	 * for exampe data-role="test"
	 *
	 * @param string $sAttribute
	 */
	public function addAttribute( $sAttribute );
	/**
	 * for exampe data-content="<div>test</div>"
	 *
	 * @param string $sOptionAttribute
	 */
	public function addOptionAttribute( $sOptionAttribute );
	/**
	 * empties the objects which will be selected
	 *
	 * @return null
	 */
	public function emptyObjectToSelect();
	/**
	 * puts the objects which will be selected
	 *
	 * @param  Patterns_Collection_Interface | Patterns_Idable_Interface $vtObjectToSelect	the object(s) which will be selected
	 * @return null
	 */
	public function putObjectToSelect( $vtObjectToSelect );

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
	 * removes an empty row from the combobox
	 *
	 * @return null
	 */
	public function removeEmptyRow();
	/**
	 * adds an event
	 * for example addEvent( "onchange='document.myform.submit();'" );
	 *
	 * @param string $szEvent	The event that wll be added
	 * @return  true if succeeded
	 */
	public function addEvent( $szEvent );
	/**
	 * sets the maximum number of characters
	 *
	 * @param int	$nMaximumNumberOfCharacters
	 * @return  true if succeeded
	 */
	public function putMaximumNumberOfCharacters( $nMaximumNumberOfCharacters );
	/**
	 * This function puts the cssclass
	 *
	 * @param  	string	$szClassId			The CSSClass
	 * @return null
	 */
	public function putCSSClass( $szClassId );
	/**
	 * This function puts the width
	 *
	 * @param  	int	$nWidth			The width
	 * @return null
	 */
	public function putWidth( $nWidth );
	/**
	 * This function puts the style to multiple select
	 *
	 * @param int $nSize the height of the select
	 * @return null
	 */
	public function putMultiple( $nSize );
	/**
	 * gets the value as a html-hidden-control
	 *
	 * @return null
	 */
	public function toHidden();
}
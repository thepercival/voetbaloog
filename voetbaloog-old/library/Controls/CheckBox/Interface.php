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
 * @package    Controls
 */

/**
 *
 *
 * @package    Controls
 */
interface Controls_CheckBox_Interface
{
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
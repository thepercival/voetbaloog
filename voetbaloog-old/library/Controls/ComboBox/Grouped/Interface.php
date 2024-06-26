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
interface Controls_ComboBox_Grouped_Interface
{
	/**
	 * puts the method to get the parent
	 *
	 * @return null
	 */
	public function putPropertyGetParent( $szPropertyGetParent );
	/**
	 * puts the method to show the parent property
	 *
	 * @return null
	 */
	public function putPropertyShowParent( $szPropertyShowParent );
}
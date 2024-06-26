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
 * @package    Patterns
 */

/**
 *
 *
 * @package    Patterns
 */
interface Patterns_Composite_Interface
{
	/**
	 * gets the children of the composite object
	 *
	 * @return Patterns_Collection_Interface	The children of the composite object
	 */
	public function getChildren();
	/**
	 * gets the parent of the composite object
	 *
	 * @return Patterns_Composite_Interface		the parent of the composite object
	 */
	public function getParent();
	/**
	 * gets the parent of the composite object
	 *
	 * @param Patterns_Composite_Interface		$oParent    the parent of the composite object
	 * @return null
	 */
	public function putParent( $oParent );
}
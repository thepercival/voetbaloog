<?php

/**
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 *
 * @package    Source
 */

/**
 * @package Source
 */
interface Source_Db_Object_Column_Collection_Interface
{
	/**
	 * gets the columns
	 *
	 * @return Patterns_Collection	collection of columns
	 */
	public function getColumns();
}
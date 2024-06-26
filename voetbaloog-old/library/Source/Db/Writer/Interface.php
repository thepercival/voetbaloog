<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
interface Source_Db_Writer_Interface
{
	/**
	 * Removes objects from source
	 *
	 * @param string	$szWhereClause	The whereclause
	 * $param	array	$arrBindVars	The bindvars
	 * @return bool true | false
	 */
	public function removeObjects( $szWhereClause, $arrBindVars );
}
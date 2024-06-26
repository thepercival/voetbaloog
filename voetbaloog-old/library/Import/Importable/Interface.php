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
 * @package    Import
 */

/**
 *
 *
 * @package    Import
 */
interface Import_Importable_Interface
{
	/**
	 * gets the ExternId
	 *
	 * @return  string	the ExternId
	 */
	public function getExternId();
	/**
	 * puts the ExternId
	 *
	 * @param  string		$szExternId	The ExternId
	 * @return  null
	 */
	public function putExternId( $szExternId );
}
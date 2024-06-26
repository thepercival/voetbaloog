<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Writer.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package Auth
 */
class RAD_Auth_Role_Db_Writer extends Source_Db_Writer
{
	public function __construct( $objFactory )
	{
		parent::__construct( $objFactory );
	}

	protected function getAutoKey()
	{
		return false;
	}
}
<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Persistance.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
final class RAD_Auth_MenuItem_Db_Persistance extends Source_Db_Persistance
{
	protected static $m_oTable = "MenuItems";

	public function __construct()
	{
		parent::__construct();
	}

	protected function setObjectProperties()
	{
		$oTable = static::getTable();

		$this["RAD_Auth_MenuItem::Id"] = $oTable->createColumn( "Id" );
		$this["RAD_Auth_MenuItem::Description"] = $oTable->createColumn( "Description" );
		$this["RAD_Auth_MenuItem::Action"] = $oTable->createColumn( "ActionName" );
	}
}
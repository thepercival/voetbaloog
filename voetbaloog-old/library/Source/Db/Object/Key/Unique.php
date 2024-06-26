<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Unique.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
class Source_Db_Object_Key_Unique extends Source_Db_Object_Key implements Source_Db_Object_Column_Collection_Interface
{
    /**
     * @var Patterns_Collection
     */
	protected $m_objColumns;

	public function __construct()
	{
		parent::__construct();
		$this->m_objColumns = Patterns_Factory::createCollection();
	}	
	
	public function getNullsUnique()
	{
		return false;
		// return $this->m_bNullsUnique;
	}

    /**
     * @see Source_Db_Object_Column_Collection_Interface::getColumns()
     */
	public function getColumns()
	{
		return $this->m_objColumns;
	}
}

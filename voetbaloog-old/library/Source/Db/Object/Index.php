<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Index.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 *
 * @package    Source
 */

/**
 *
 *
 * @package Source
 */
class Source_Db_Object_Index extends Source_Db_Object implements Source_Db_Object_Column_Collection_Interface
{
	// Source_Db_Object_Column_Collection_Interface
	protected $m_sTypeX;		// string
    /**
     * @var Patterns_Collection
     */
	protected $m_objColumns;

	public function __construct()
	{
		parent::__construct();
		$this->m_objColumns = Patterns_Factory::createCollection();
	}

	public function getTypeX()
	{
		return $this->m_sTypeX;
	}
	
	public function putTypeX( $sTypeX )
	{
		$this->m_sTypeX = $sTypeX;
	}

    /**
     * @see Source_Db_Object_Column_Collection_Interface::getColumns()
     */
	public function getColumns()
	{
		return $this->m_objColumns;
	}
}
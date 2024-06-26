<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Foreign.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
class Source_Db_Object_Key_Foreign extends Source_Db_Object_Key
{
	protected $m_szDeleteRule; 				// string
	protected $m_szUpdateRule;				// string
	protected $m_objReferencedByColumns; 	// Patterns_Collection_Interface
	protected $m_objReferencingTable;		// Source_Db_Object_Table
	
	public function __construct()
	{
		parent::__construct();
		
		$this->m_objReferencedByColumns = Patterns_Factory::createCollection();		
	}
	
	public function getDeleteRule()
	{
		return $this->m_szDeleteRule;
	}
	
	public function putDeleteRule( $szDeleteRule )
	{
		$this->m_szDeleteRule = $szDeleteRule;
	}
	
	public function getUpdateRule()
	{
		return $this->m_szUpdateRule;
	}
	
	public function putUpdateRule( $szUpdateRule)
	{
		$this->m_szUpdateRule = $szUpdateRule;
	}
	
	public function getReferencedByColumns()
	{			
		return $this->m_objReferencedByColumns;
	}

	public function putReferencingTable( $objTable )
	{
		$this->m_objReferencingTable = $objTable;
	}

	public function getReferencingTable()
	{
		return $this->m_objReferencingTable;
	}
}


<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Table.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
class Source_Db_Object_Table extends Source_Db_Object
{
	protected $m_oColumns;				// Patterns_Collection
	protected $m_objKeys;				// Patterns_Collection
	protected $m_objIndices;			// Patterns_Collection
	protected $m_objPrimaryKeyColumns;	// Patterns_Collection

	public function __construct()
	{
		parent::__construct();
		$this->m_oColumns = Patterns_Factory::createCollection();
		$this->m_objKeys = Patterns_Factory::createCollection();
		$this->m_objIndices = Patterns_Factory::createCollection();
	}

	public function getColumns()
	{
		return $this->m_oColumns;
	}

	public function createColumn( $sName )
	{
		$oColumn = Source_Db_Object_Factory::createColumn( $sName );
		$oColumn->putId( $sName );
		$oColumn->putName( $sName );
		$oColumn->putTable( $this );

		$this->m_oColumns->add( $oColumn );

		return $oColumn;
	}

  	public function getKeys()
	{
		return $this->m_objKeys;
	}

  	public function getIndices()
	{
		return $this->m_objIndices;
	}

  	public function getPrimaryKeyColumns()
	{
		if ( $this->m_objPrimaryKeyColumns === null )
		{
			$this->m_objPrimaryKeyColumns = Patterns_Factory::createCollection();
			$objColumns = $this->getColumns();
			foreach( $objColumns as $objColumn )
			{
				if ( $objColumn->getPrimary() === true )
					$this->m_objPrimaryKeyColumns->add( $objColumn );
			}
		}
		return $this->m_objPrimaryKeyColumns;
	}
}
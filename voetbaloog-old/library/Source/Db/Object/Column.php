<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Column.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
class Source_Db_Object_Column extends Source_Db_Object
{
	protected $m_sDatatype;			// string
	protected $m_nLength;			// int
	protected $m_bNullable;			// bool
	protected $m_bPrimary;			// bool
	protected $m_bAutoIncrement;	// bool
	protected $m_szDefaultValue;	// string
	protected $m_szDefaultValueName;// string
	protected $m_oTable;			// string

	public function __construct()
	{
		parent::__construct();
	}

	public function getName( $bPlusTableName = false )
	{
		if ( $bPlusTableName === true )
			return $this->getTable()->getName() . "." . $this->m_szName;
		return $this->m_szName;
	}

	public function getDatatype()
	{
		return $this->m_sDatatype;
	}

	public function putDatatype( $sDatatype )
	{
		$this->m_sDatatype = $sDatatype;
	}

  	public function getLength()
	{
		return $this->m_nLength;
	}

	public function putLength( $nLength )
	{
		$this->m_nLength = $nLength;
	}

  	public function getNullable()
	{
		return $this->m_bNullable;
	}

	public function putNullable( $bNullable )
	{
		$this->m_bNullable = $bNullable;
	}

  	public function getPrimary()
	{
		return $this->m_bPrimary;
	}

	public function putPrimary( $bPrimary )
	{
		$this->m_bPrimary = $bPrimary;
	}

  	public function getAutoIncrement()
	{
		return $this->m_bAutoIncrement;
	}

	public function putAutoIncrement( $bAutoIncrement )
	{
		$this->m_bAutoIncrement = $bAutoIncrement;
	}

  	public function getDefaultValue()
	{
		return $this->m_szDefaultValue;
	}

	public function putDefaultValue( $szDefaultValue )
	{
		$this->m_szDefaultValue = $szDefaultValue;
	}

	public function getDefaultValueName()
	{
		return $this->m_szDefaultValueName;
	}

	public function putDefaultValueName( $szDefaultValueName )
	{
		$this->m_szDefaultValueName = $szDefaultValueName;
	}

	public function getTable()
	{
		return $this->m_oTable;
	}

	public function putTable( $vtTable )
	{
		if ( is_string( $vtTable ) )
			$vtTable = Source_Db_Object_Factory::createTable( $vtTable );
		$this->m_oTable = $vtTable;
	}
}
<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Factory.php 4558 2019-08-13 08:54:29Z thepercival $
 * @since	  File available since Release 4.0
 * @package	Patterns
 */

/**
 * @package	Patterns
 */
class Source_Db_Object_Factory implements Patterns_Singleton_Interface
{
	private static $m_objSingleton;
	private static $m_oTables;
	private static $m_bPoolEnabled = true;

	 /**
	 * A protected constructor; prevents direct creation of object
	 */
	protected function __construct() { }

	/**
	 * @see Patterns_Singleton_Interface::__clone()
	 */
	public function __clone()
	{
		trigger_error("Cloning is not allowed.", E_USER_ERROR);
	}

	/**
	 * Defined by Patterns_Singleton_Interface; Prevent users to clone the instance
	 *
	 * @see Patterns_Singleton_Interface::getInstance()
	 */
	public static function getInstance()
	{
		if ( static::$m_objSingleton === null )
		{
			$MySelf = __CLASS__;
			static::$m_objSingleton = new $MySelf();
		}
		return static::$m_objSingleton;
	}

	public static function toggleTablePool( $bEnablePool )
	{
		static::$m_bPoolEnabled = $bEnablePool;
	}

	public static function getTables()
	{
		if ( static::$m_oTables === null or static::$m_bPoolEnabled !== true )
			static::$m_oTables = Patterns_Factory::createCollection();
		return static::$m_oTables;
	}

	public static function createColumn( $szName )
	{
		$objColumn = new Source_Db_Object_Column();
		$objColumn->putId( $szName );
		$objColumn->putName( $szName );
		return $objColumn;
	}

	public static function createTable( $sName )
	{
		$oTables = static::getTables();

		$oTable = $oTables[ $sName ];
		if ( $oTable === null )
		{
			$oTable = new Source_Db_Object_Table();
			$oTable->putId( $sName );
			$oTable->putName( $sName );

			$oTables->add( $oTable );
		}
		return $oTable;
	}

	public static function createIndex( $szName )
	{
		$objIndex = new Source_Db_Object_Index();
		$objIndex->putName( $szName );
		$objIndex->putId( $szName );
		return $objIndex;
	}

	public static function createForeignKey( $szName )
	{
		$objKey = new Source_Db_Object_Key_Foreign();
		$objKey->putName( $szName );
		$objKey->putId( $szName );
		return $objKey;
	}

	public static function createUniqueKey( $szName )
	{
		$objKey = new Source_Db_Object_Key_Unique();
		$objKey->putName( $szName );
		$objKey->putId( $szName );
		return $objKey;
	}

	public static function createDefaultKey( $szName )
	{
		$objKey = new Source_Db_Object_Key_Default();
		$objKey->putName( $szName );
		$objKey->putId( $szName );
		return $objKey;
	}

	public static function createTablesFromXML( $objXMLDb )
	{
		$objTables = Patterns_Factory::createCollection();

		foreach ( $objXMLDb->children() as $xmlCollection )
		{
			if ( $xmlCollection->getName() === "tables" )
			{
				foreach ( $xmlCollection->children() as $xmlTable )
				{
					$objTables->add( static::createTableFromXML( $xmlTable ) );
				}
			}
		}

		return $objTables;
	}

	public static function createQueryFromXML( $xmlQueries )
	{
		$arrSql = array();
		foreach ( $xmlQueries->children() as $xmlSql )
		{
			$arrSql[] = (string) $xmlSql;
		}
		return $arrSql;
	}

	public static function createTableFromXML( $xmlTable )
	{
		if ( (string) $xmlTable->Log === "true" )
			return static::createLogTableFromXML( (string) $xmlTable->Name );

		$objTable = static::createTable( (string) $xmlTable->Name );

		$objColumns = $objTable->getColumns();
		$objIndices = $objTable->getIndices();
		$objKeys = $objTable->getKeys();

		foreach ( $xmlTable->children() as $xmlTableProperty )
		{
			if ( $xmlTableProperty->getName() === "Columns" )
				$objColumns->addCollection( static::createColumnsFromXML( $xmlTableProperty ) );
			if ( $xmlTableProperty->getName() === "Indices" )
				$objIndices->addCollection( static::createIndicesFromXML( $xmlTableProperty ) );
			if ( $xmlTableProperty->getName() === "Keys" )
				$objKeys->addCollection( static::createKeysFromXML( $xmlTableProperty ) );
		}

		return $objTable;
	}

	public static function createLogTableFromXML( $sTableName )
	{
		$sXmlPath = realpath( dirname(__FILE__) ) . DIRECTORY_SEPARATOR . "Table" . DIRECTORY_SEPARATOR . "Log.xml";

		$xmlTable = Source_XML_Reader::fileToSimpleXML( $sXmlPath );

		$oTable = static::createTableFromXML( $xmlTable );
		$oTable->putName( $xmlTable->Prefix . $sTableName );

		return $oTable;
	}

	public static function createColumnsFromXML( $objXMLColumns )
	{
		if ( $objXMLColumns === null )
			return null;

		$objColumns = Patterns_Factory::createCollection();

		foreach ( $objXMLColumns->children() as $xmlColumn )
		{
			$objColumn = static::createColumn( (string) $xmlColumn->Name );
			$objColumn->putDatatype( (string) $xmlColumn->Datatype );
			$nLength = (int) $xmlColumn->Length;
			if ( $nLength > 0 )
				$objColumn->putLength( $nLength );
			$objColumn->putNullable( ( (string) $xmlColumn->Nullable ) === "Y" );
			$objColumn->putPrimary( ( (string) $xmlColumn->Primary ) === "Y" );
			$objColumn->putAutoIncrement( ( (string) $xmlColumn->AutoIncrement ) === "Y" );
			$szDefaultValue = (string) $xmlColumn->DefaultValue;
			if ( strlen( $szDefaultValue ) > 0 )
			{
				$objColumn->putDefaultValue( $szDefaultValue );

				$szDefaultValueName = (string) $xmlColumn->DefaultValueName;
				if ( strlen( $szDefaultValueName ) > 0 )
					$objColumn->putDefaultValueName( $szDefaultValueName );
			}
			$objColumns->add( $objColumn );
		}
		return $objColumns;
	}

	public static function createKeysFromXML( $objXMLKeys )
	{
		if ( $objXMLKeys === null )
			return null;

		$objKeys = Patterns_Factory::createCollection();

		foreach ( $objXMLKeys->children() as $xmlKey )
		{
			$objKey = null;

			$szType = (string) $xmlKey->TypeE;
			if ( $szType === "FOREIGN" )
			{
				$objKey = static::createForeignKey( (string) $xmlKey->Name );

				$objKey->putDeleteRule( (string) $xmlKey->DeleteRule );

				$objKey->putUpdateRule( (string) $xmlKey->UpdateRule );

				if ( strlen( $xmlKey->ReferencedByColumns->getName() ) > 0 )
				{
					$objReferencedByColumns = $objKey->getReferencedByColumns();

					foreach($xmlKey->ReferencedByColumns->children() as $xmlColumn )
					{
						$objColumn = static::createColumn( (string) $xmlColumn->Name );
						$objColumn->putTable( (string) $xmlColumn->TableName );
						$objReferencedByColumns->add( $objColumn );
					}
				}

				if ( strlen( $xmlKey->ReferencingTable->getName() ) > 0 )
				{
					$objTable = static::createTable ( (string) $xmlKey->ReferencingTable->Name );
					$objColumns = $objTable->getColumns();
					foreach( $xmlKey->ReferencingTable->Columns->children() as $xmlColumn )
					{
						$objColumn = static::createColumn( (string) $xmlColumn->Name );
						$objColumns->add( $objColumn );
					}
					$objKey->putReferencingTable( $objTable );
				}
			}
			else if ( $szType === "UNIQUE" )
			{
				$objKey= static::createUniqueKey( (string) $xmlKey->Name );

				$objColumns = $objKey->getColumns();

				if ( count( $xmlKey->Columns ) > 0 )
				{
					foreach( $xmlKey->Columns->children() as $xmlColumn )
					{
						$objColumns->add( static::createColumn( (string) $xmlColumn->Name ) );
					}
				}
			}
			else if ( $szType === "DEFAULT" )
			{
				$objKey= static::createDefaultKey( (string) $xmlKey->Name );
			}

			$objKeys->add( $objKey );

		}
		return $objKeys;
	}

	public static function createIndicesFromXML( $objXMLIndices )
	{
		if ( $objXMLIndices === null )
			return null;

		$objIndices = Patterns_Factory::createCollection();

		foreach ( $objXMLIndices->children() as $xmlIndex )
		{
			$objIndex = null;

			$objIndex = static::createIndex( (string) $xmlIndex->Name );

			$szType = (string) $xmlIndex->TypeE;
			if ( strlen( $szType ) > 0 )
				$objIndex->putTypeX( (string) $szType );

			$objColumns = $objIndex->getColumns();

			if ( count( $xmlIndex->Columns ) > 0 )
			{
				foreach( $xmlIndex->Columns->children() as $xmlColumn )
				{
					$objColumns->add( static::createColumn( (string) $xmlColumn->Name ) );
				}
			}
			$objIndices->add( $objIndex );

		}
		return $objIndices;
	}
}
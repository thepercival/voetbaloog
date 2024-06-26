<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: SqlSyntaxFactory.php 4557 2019-08-12 18:50:59Z thepercival $
 * @since	  File available since Release 4.0
 * @package	Patterns
 */

/**
 * @package	Patterns
 */
class Source_Db_SqlSyntaxFactory implements Patterns_Singleton_Interface
{
	private static $m_objSingleton;
	protected static $m_sTablePrefix;
	CONST MYSQL = 1;
	CONST MSSQL = 2;
	CONST ORACLE = 4;

	protected function __construct()
	{

	}

	/**
	 * Defined by Patterns_Singleton_Interface; Prevent users to clone the instance
	 *
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
		if ( self::$m_objSingleton === null )
		{
			$MySelf = __CLASS__;
			self::$m_objSingleton = new $MySelf();
		}
		return self::$m_objSingleton;
	}

	public static function getDbType( $vtDb )
	{
		if ( is_string( $vtDb ) )
		{
			if ( $vtDb === "SQLSRV" or $vtDb === "PDO_MSSQL" )
				return Source_Db_SqlSyntaxFactory::MSSQL;
			elseif ( $vtDb === "PDO_MYSQL" )
				return Source_Db_SqlSyntaxFactory::MYSQL;
			elseif ( $vtDb === "ORACLE" )
				return Source_Db_SqlSyntaxFactory::ORACLE;
		}
		elseif ( is_int( $vtDb ) )
			return $vtDb;
		elseif ( $vtDb instanceof Zend_Db_Adapter_Pdo_Mysql )
			return Source_Db_SqlSyntaxFactory::MYSQL;
		elseif ( $vtDb instanceof Zend_Db_Adapter_Oracle )
			return Source_Db_SqlSyntaxFactory::ORACLE;
		elseif ( $vtDb instanceof Zend_Db_Adapter_Sqlsrv )
			return Source_Db_SqlSyntaxFactory::MSSQL;
		elseif ( $vtDb instanceof Zend_Db_Adapter_Pdo_Mssql )
			return Source_Db_SqlSyntaxFactory::MSSQL;

		throw new Exception( "Database of unknown type (".__FILE__.")!", E_ERROR );
	}

	public static function getTablePrefix()
	{
		return static::$m_sTablePrefix;
	}

	public static function putTablePrefix( $sTablePrefix )
	{
		static::$m_sTablePrefix = $sTablePrefix;
	}

	public static function getQuotedName( $vtDBType, $oObject )
	{
		if ( ! ( $oObject instanceof Source_Db_Object ) )
			throw new Exception( "This is not a database object!", E_ERROR );

		$vtDBType = static::getDbType( $vtDBType );

		$sName = $oObject->getName();
		if ( $oObject instanceof Source_Db_Object_Table )
			$sName = static::getTablePrefix() . $sName;

		if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
			return "`".$sName."`";
		else
			return "\"".$sName."\"";
	}

	/*public static function getIfExistsStatement( $vtDBType )
	{
		$vtDBType = static::getDbType( $vtDBType );

		if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
			return "IGNORE";
		else
			return "\"".$oObject->getName()."\"";
	}*/

	public static function getDateCurrent( $vtDBType )
	{
		$vtDBType = static::getDbType( $vtDBType );

		if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
		{
			return "GETDATE()";
		}
		else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
		{
			return "NOW()";
		}
		else if ( $vtDBType === Source_Db_SqlSyntaxFactory::ORACLE )
		{
			return "SYSDATE";
		}
		else
			throw new Exception( "Unknown dbtype (".__FILE__.")!", E_ERROR );

		return null;
	}

	/*
	 * DATE_ADD
	 * Oracle DateADD("datepart" , number, date )
	 * MySql DATE_ADD( date, interval(INTERVAL 8 DAY) )
	 * MsSql DATEADD (datepart , number , date )
	 */
	public static function getDateAdd( $vtDBType, $nAmount, $nDatePart, $oDateTime )
	{
		$vtDBType = static::getDbType( $vtDBType );
		$szDateTime = static::dateToString( $oDateTime, $vtDBType );

		$szRetVal = null;
		if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
		{
			$szRetVal = "DATE_ADD( ".$szDateTime.", INTERVAL ".$nAmount." ".static::getDatePart( $vtDBType, $nDatePart )." )";
		}
		else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
		{
			$szRetVal = "DATEADD( ".static::getDatePart( $vtDBType, $nDatePart ).", ".$nAmount.", ".$szDateTime." )";
		}
		else
			throw new Exception( "Unknown dbtype (".__FILE__.")!", E_ERROR );

		return $szRetVal;
	}

	/*
	 * VALUE
	 * Oracle ?
	 * MySql EXTRACT( datepart FROM date )
	 * MsSql DATEPART( datepart , date )
	 */
	public static function getDateValue( $vtDBType, $nDatePart, $vtDateTime )
	{
		$vtDBType = static::getDbType( $vtDBType );

		if ( $vtDateTime instanceof DateTime )
			$vtDateTime = static::dateToString( $vtDateTime, $vtDBType );

		$szRetVal = null;
		if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
		{
			if ( $nDatePart === Zend_Date::WEEKDAY_8601	)
				$szRetVal = "WEEKDAY( " . $vtDateTime . ")";
			else if ( $nDatePart === Zend_Date::WEEK )
				  $szRetVal = "WEEK( " . $vtDateTime . ", 3 )";
			else
				$szRetVal = "EXTRACT( ".static::getDatePart( $vtDBType, $nDatePart )." FROM ".$vtDateTime." )";
		}
		else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
		{
			$szRetVal = "DATEPART( ".static::getDatePart( $vtDBType, $nDatePart ).", ".$vtDateTime." )";
		}
		else
			throw new Exception( "Unknown dbtype (".__FILE__.")!", E_ERROR );

		return $szRetVal;
	}

	/*
	 * VALUE
	 * Oracle ?
	 * MySql TIMESTAMPDIFF( datepart, startdate, enddate )
	 * MsSql DATEDIFF ( datepart ,startdate ,enddate )
	 */
	public static function getDateDiff( $vtDBType, $nDatePart, $oStartDateTime, $oEndDateTime )
	{
		$vtDBType = static::getDbType( $vtDBType );
		$szStartDateTime = static::dateToString( $oStartDateTime, $vtDBType );
		$szEndDateTime = static::dateToString( $oEndDateTime, $vtDBType );

		$szRetVal = null;
		if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
		{
			$szRetVal = "TIMESTAMPDIFF( ".static::getDatePart( $vtDBType, $nDatePart ).", ".$szStartDateTime.", ".$szEndDateTime." )";
		}
		else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
		{
			$szRetVal = "DATEDIFF( ".static::getDatePart( $vtDBType, $nDatePart ).", ".$szStartDateTime.", ".$szEndDateTime." )";
		}
		else
			throw new Exception( "Unknown dbtype (".__FILE__.")!", E_ERROR );

		return $szRetVal;
	}

	/**
	 * ODBC STANDARD : 1 = Sunday, 2 = Monday, ..., 7 = Saturday)
	 */
	protected static function getDatePart( $vtDBType, $nDatePart )
	{
		$vtDBType = static::getDbType( $vtDBType );

		switch( $nDatePart )
		{
			case Zend_Date::YEAR:
				if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
					return "";
				else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
					return "yyyy";
				break;
			case Zend_Date::YEAR_SHORT:
				if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
					return "YEAR";
				else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
					return "yy";
				break;
			case Zend_Date::MONTH_SHORT:
				if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
					return "MONTH";
				else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
					return "mm";
				break;
			case Zend_Date::DAY_SHORT:
				if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
					return "DAY";
				else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
					return "dd";
				break;
			case Zend_Date::WEEK:
				if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
					return "WEEK";
				else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
					return "isoww";
				break;
			case Zend_Date::HOUR_SHORT:
				if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
					return "HOUR";
				else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
					return "hh";
				break;
			case Zend_Date::MINUTE_SHORT:
				if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
					return "MINUTE";
				else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
					return "mi";
				break;
			case Zend_Date::SECOND_SHORT:
				if ( $vtDBType === Source_Db_SqlSyntaxFactory::MYSQL )
					return "SECOND";
				else if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
					return "ss";
				break;
			default:
				throw new Exception( "Unknown datepart ".$nDatePart." (".__FILE__.")!", E_ERROR );
		}

		throw new Exception( "Unknown dbtype (".__FILE__.")!", E_ERROR );
	}

	protected static function dateToString( $vtDateTime, $nDbType )
	{
		if ( $vtDateTime instanceof Source_Db_Object_Column )
			return $vtDateTime->getName();
		else
			return Source_Db::toSqlString( $vtDateTime, false, false, $nDbType );
		return null;
	}

	public static function convertReferenceRule( $vtDBType, $szReferenceRule )
	{
		$vtDBType = self::getDbType( $vtDBType );

		if( $szReferenceRule === "R" )
		{
			if ( $vtDBType === Source_Db_SqlSyntaxFactory::MSSQL )
				return "NO ACTION";
			return "RESTRICT";
		}
		elseif( $szReferenceRule === "C" )
		{
			return "CASCADE";
		}
		elseif( $szReferenceRule === "N" )
		{
			return "SET NULL";
		}
		throw new Exception( "An unknown foreign key rule is used!!", E_ERROR );
	}

	/**
	 * SQL Server does not have a create table statement with the INDEX keyword.
	 */
	public static function getCreateTableStatement( $nDBType, $oTable )
	{
		$arrSQL = array();

		$szSQL = "CREATE TABLE ".static::getQuotedName( $nDBType, $oTable )." ( ".PHP_EOL;
		{
			$oColumns = $oTable->getColumns();
			foreach( $oColumns as $oColumn )
			{
				$szSQL .= static::getCreateTableColumnStatement( $nDBType, $oColumn );
				$szSQL .= ",".PHP_EOL;
			}

			// als geen primary key gedefinieerd dan verwijder laatste comma
			$sPrimaryKeyStatement = static::getCreatePrimaryKeyStatement( $nDBType, $oTable );
			if ( $sPrimaryKeyStatement !== null )
				$szSQL .= $sPrimaryKeyStatement.PHP_EOL;
			else
				$szSQL = substr( $szSQL , 0, strlen( $szSQL ) - ( 1 + strlen( PHP_EOL ) ) );

			$szSQL .= " ) ";

			if ( $nDBType === Source_Db_SqlSyntaxFactory::MYSQL )
				$szSQL .= "ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci";
		}
		$arrSQL[] = $szSQL;

		$arrSQLIndices = null;
		$oIndices = $oTable->getIndices();
		if ( $oIndices->count() > 0 )
			$arrSQLIndices = static::getCreateIndicesStatement( $nDBType, $oTable );
		if ( $arrSQLIndices !== null )
		{
			foreach ( $arrSQLIndices as $sSQLIndex )
				$arrSQL[] = $sSQLIndex;
		}

		$arrSQLKeys = null;
		$oKeys = $oTable->getKeys();
		if ( $oKeys->count() > 0 )
			$arrSQLKeys = static::getCreateKeysStatement( $nDBType, $oTable );
		if ( $arrSQLKeys !== null )
		{
			foreach ( $arrSQLKeys as $sSQLKey )
				$arrSQL[] = $sSQLKey;
		}

		return $arrSQL;
	}

	public static function getExistsTableStatement( $nDBType, $oTable )
	{
		return "SELECT * FROM ".static::getQuotedName( $nDBType, $oTable );
	}

	public static function getDeleteTableStatement( $nDBType, $oTable )
	{
		$szSQL = "DROP TABLE ".static::getQuotedName( $nDBType, $oTable )." ";

		return $szSQL;
	}

	public static function getCreateColumnStatement( $nDBType, $oTable )
	{
		$szSQL = "ALTER TABLE ".static::getQuotedName( $nDBType, $oTable )." ";

		$oColumns = $oTable->getColumns();

		$bFirstIt = true;
		foreach( $oColumns as $oColumn )
		{
			if ( !( $nDBType === Source_Db_SqlSyntaxFactory::MSSQL and $bFirstIt === false ) )
				$szSQL .= "ADD ";
			$szSQL .= static::getCreateTableColumnStatement( $nDBType, $oColumn );
			$szSQL .= ",";
			$bFirstIt = false;
		}

		$szSQL = substr( $szSQL , 0, strlen( $szSQL ) - 1 );
		return $szSQL;
	}

	protected static function getCreateTableColumnStatement( $nDBType, $oColumn )
	{
		$szSQL = static::getQuotedName( $nDBType, $oColumn )." ";

		$szDatatype = $oColumn->getDatatype();
		if ( $nDBType === Source_Db_SqlSyntaxFactory::MSSQL )
		{
			if ( $szDatatype === "varchar" )
				$szDatatype = "n".$szDatatype;
			else if ( $szDatatype === "mediumint" )
				$szDatatype = "int";
			else if ( $szDatatype === "blob" )
				$szDatatype = "varBinary(MAX)";
			else if ( $szDatatype === "time" )
				$szDatatype = "datetime2";
			else if ( $szDatatype === "datetime" )
				$szDatatype = "datetime2";
		}
		elseif ( $nDBType === Source_Db_SqlSyntaxFactory::ORACLE )
		{
			if ( $szDatatype === "varchar" )
				$szDatatype = $szDatatype."2";
		}
		$szSQL .= $szDatatype;

		if ( $oColumn->getLength() !== null )
			$szSQL .= "(".$oColumn->getLength().")";
		$szSQL .= " ";

		if ( $oColumn->getDefaultValue() !== null and $oColumn->getDefaultValueName() === null )
			throw new Exception( "defaultvalue specified without defaultvaluename", E_ERROR );

		if ( $nDBType === Source_Db_SqlSyntaxFactory::MSSQL )
		{
			if ( $oColumn->getDefaultValue() !== null )
			{
				$szSQL .= "CONSTRAINT " . $oColumn->getDefaultValueName() . " ";

				$szSQL .= "DEFAULT ".$oColumn->getDefaultValue()." ";
			}

			if ( $oColumn->getNullable() === true )
				$szSQL .= "NULL";
			else
				$szSQL .= "NOT NULL";
		}
		else
		{
			if ( $oColumn->getDefaultValue() !== null )
				$szSQL .= "DEFAULT ".$oColumn->getDefaultValue();
			else if ( $oColumn->getNullable() === true )
				$szSQL .= "DEFAULT NULL";
			else
				$szSQL .= "NOT NULL";
		}

		if ( $oColumn->getAutoIncrement() === true )
		{
			if ( $nDBType === Source_Db_SqlSyntaxFactory::MSSQL )
				$szSQL .= " IDENTITY";
			else
				$szSQL .= " AUTO_INCREMENT";
		}
		return $szSQL;
	}

	public static function getUpdateColumnStatement( $nDBType, $oTable )
	{
		$szSQL = "ALTER TABLE ".static::getQuotedName( $nDBType, $oTable )." ";

		$oColumns = $oTable->getColumns();

		foreach( $oColumns as $oColumn )
		{
			if ( $nDBType === Source_Db_SqlSyntaxFactory::MSSQL )
				$szSQL .= "ALTER COLUMN ";
			else
				$szSQL .= "MODIFY ";

			$szSQL .= static::getCreateTableColumnStatement( $nDBType, $oColumn );
			$szSQL .= ",";
		}

		$szSQL = substr( $szSQL , 0, strlen( $szSQL ) - 1 );
		return $szSQL;
	}

	public static function getDeleteColumnStatement( $nDBType, $oTable )
	{
		$szSQL = "ALTER TABLE ".static::getQuotedName( $nDBType, $oTable )." ";

		$oColumns = $oTable->getColumns();

		$bFirst = true;
		foreach( $oColumns as $oColumn )
		{
			if ( $bFirst === true or $nDBType === Source_Db_SqlSyntaxFactory::MYSQL )
				$szSQL .= "DROP COLUMN ";

			$szSQL .= static::getQuotedName( $nDBType, $oColumn );
			$szSQL .= ",";

			$bFirst = false;
		}

		$szSQL = substr( $szSQL , 0, strlen( $szSQL ) - 1 );
		return $szSQL;
	}

	public static function getCreateKeysStatement( $nDBType, $oTable )
	{
		$arrSQL = array();

		$oKeys = $oTable->getKeys();
		foreach( $oKeys as $oKey )
		{
			$szSQL = null;
			if( $oKey instanceof Source_Db_Object_Key_Foreign )
				$szSQL = static::getCreateForeignKeyStatement( $nDBType, $oKey, $oTable );
			else if( $oKey instanceof Source_Db_Object_Key_Unique  )
				$szSQL = static::getCreateUniqueKeyStatement( $nDBType, $oKey, $oTable );

			if ( $szSQL !== null )
				$arrSQL[] = $szSQL;
		}

		return $arrSQL;
	}

	protected static function getCreateUniqueKeyStatement( $nDBType, $oKey, $oTable )
	{
		$szSQL = null;
		// Filtered index is needed for MSSQL
		if ( $nDBType === Source_Db_SqlSyntaxFactory::MSSQL )
		{
			$szSQL = static::getCreateIndexStatement( $nDBType, $oKey, $oTable );
		}
		else
		{
			$szSQL = "ALTER TABLE ".static::getQuotedName( $nDBType, $oTable )." ADD ";

			$szSQL .= "CONSTRAINT ";
			$szSQL .= static::getQuotedName( $nDBType, $oKey );

			$szSQL .= " UNIQUE ( ";

			$bFirst = true;
			foreach( $oKey->getColumns() as $oColumn )
			{
				if( $bFirst === true )
				$bFirst = false;
				else
				$szSQL .= ", ";

				$szSQL .= static::getQuotedName( $nDBType, $oColumn );
			}
			$szSQL .= " ) ";
		}

		return $szSQL;
	}

	public static function getCreateForeignKeyStatement( $nDBType, $oKey, $oTable )
	{
		if( !( $oKey instanceof Source_Db_Object_Key_Foreign ) )
			throw new Exception( "This is not a foreign key!", E_ERROR );

		$szSQL = "ALTER TABLE ".static::getQuotedName( $nDBType, $oTable )." ADD ";

		$szSQL .= "CONSTRAINT ";
		$szSQL .= static::getQuotedName( $nDBType, $oKey );

		$szSQL .= " FOREIGN KEY ( ";

		$bFirst = true;
		foreach( $oKey->getReferencedByColumns() as $oColumn )
		{
			if( $bFirst === true )
				$bFirst = false;
			else
				$szSQL .= ", ";

			$szSQL .= static::getQuotedName( $nDBType, $oColumn );

		}

		$szSQL .= " ) ";

		$szSQL .= "REFERENCES ";
		$szSQL .= static::getQuotedName( $nDBType, $oKey->getReferencingTable() );

		$szSQL .= "( ";

		$bFirst = true;
		foreach( $oKey->getReferencingTable()->getColumns() as $oColumn )
		{
			if( $bFirst === true )
				$bFirst = false;
			else
				$szSQL .= ", ";

			$szSQL .= static::getQuotedName( $nDBType, $oColumn );
		}


		$szSQL .= " ) ";

		$szSQL .= "ON DELETE ";
		$szSQL .= static::convertReferenceRule( $nDBType, $oKey->getDeleteRule() );
		$szSQL .= " ON UPDATE ";
		$szSQL .= static::convertReferenceRule( $nDBType, $oKey->getUpdateRule() );

		return $szSQL;
	}

	public static function getDeleteKeysStatement( $nDBType, $oTable )
	{
		$arrSQL = array();

		$oKeys = $oTable->getKeys();
		foreach( $oKeys as $oKey )
		{
			if( $oKey instanceof Source_Db_Object_Key_Unique and $nDBType === Source_Db_SqlSyntaxFactory::MSSQL )
				$szSQL = static::getDeleteIndexStatement( $nDBType, $oKey, $oTable );
			else
				$szSQL = static::getDeleteKeyStatement( $nDBType, $oKey, $oTable );

			if ( $szSQL !== null )
				$arrSQL[] = $szSQL;
		}

		return $arrSQL;
	}

	public static function getDeleteKeyStatement( $nDBType, $oKey, $oTable )
	{
		if( $oKey instanceof Source_Db_Object_Key_Default and $nDBType !== Source_Db_SqlSyntaxFactory::MSSQL )
			return "";

		$szSQL = "ALTER TABLE ".static::getQuotedName( $nDBType, $oTable )." ";
		$szSQL .= "DROP ";

		if ( $nDBType === Source_Db_SqlSyntaxFactory::MYSQL )
		{
			if( $oKey instanceof Source_Db_Object_Key_Foreign )
			{
				$szSQL .= "FOREIGN KEY ";
			}
			else if( $oKey instanceof Source_Db_Object_Key_Unique )
			{
				$szSQL .= "KEY ";
			}
		}
		else
		{
			$szSQL .= "CONSTRAINT ";
		}

		$szSQL .= static::getQuotedName( $nDBType, $oKey );

		return $szSQL;
	}

	protected static function getCreatePrimaryKeyStatement( $nDBType, $oTable )
	{
		$oPrimaryKeyColumns = $oTable->getPrimaryKeyColumns();
		if ( $oPrimaryKeyColumns->count() === 0 )
			return null;

		$szSQL = "PRIMARY KEY ( ";

		$bFirst = true;
		foreach( $oPrimaryKeyColumns as $oPrimaryKeyColumn )
		{
			if ( $bFirst === true )
				$bFirst = false;
			else
				$szSQL .= ", ";

			$szSQL .= static::getQuotedName( $nDBType, $oPrimaryKeyColumn )." ";
		}

		$szSQL .= " )";

		return $szSQL;
	}

	public static function getCreateIndicesStatement( $nDBType, $oTable )
	{
		$arrSQL = array();

		$oIndices = $oTable->getIndices();
		foreach( $oIndices as $oIndex )
			$arrSQL[] = static::getCreateIndexStatement( $nDBType, $oIndex, $oTable );

		return $arrSQL;
	}

	public static function getCreateIndexStatement( $nDBType, $oIndex, $oTable )
	{
		$sTypeX = null;
		$bNullsUnique = false;
		if( $oIndex instanceof Source_Db_Object_Key_Unique  )
		{
			$sTypeX = "UNIQUE";
			$bNullsUnique = $oIndex->getNullsUnique();
		}
		else
		{
			$sTypeX = $oIndex->getTypeX();
		}

		$szSQL = "CREATE ".$sTypeX." INDEX ".static::getQuotedName( $nDBType, $oIndex )." ON ";
		$szSQL .= static::getQuotedName( $nDBType, $oTable )." (";

		$bFirst = true;
		foreach( $oIndex->getColumns() as $oColumn )
		{
			if( $bFirst === true )
				$bFirst = false;
			else
				$szSQL .= ", ";

			$szSQL .= static::getQuotedName( $nDBType, $oColumn );
		}

		$szSQL .= " ) ";

		// default is false
		if ( $nDBType === Source_Db_SqlSyntaxFactory::MSSQL and $bNullsUnique !== true )
		{
			$szSQL .= "WHERE ( ";
			$bFirst = true;
			foreach( $oIndex->getColumns() as $oColumn )
			{
				if( $bFirst === true )
				{
					$bFirst = false;
				}
				else
				{
					$szSQL .= "AND ";
				}

				$szSQL .= static::getQuotedName( $nDBType, $oColumn ). " IS NOT NULL ";
			}
			$szSQL .= ")";
		}

		return $szSQL;
	}

	public static function getDeleteIndicesStatement( $nDBType, $oTable )
	{
		$arrSQL = array();

		$oIndices = $oTable->getIndices();
		foreach( $oIndices as $oIndex )
			$arrSQL[] = static::getDeleteIndexStatement( $nDBType, $oIndex, $oTable );

		return $arrSQL;
	}

	public static function getDeleteIndexStatement( $nDBType, $oIndex, $oTable )
	{
		$szSQL = "DROP INDEX ";

		if ( $nDBType === Source_Db_SqlSyntaxFactory::MYSQL )
		{
			$szSQL .= static::getQuotedName( $nDBType, $oIndex );
			$szSQL .= " ON ";
			$szSQL .= static::getQuotedName( $nDBType, $oTable );
		}
		else
		{
			$szSQL .= static::getQuotedName( $nDBType, $oTable );
			$szSQL .= ".";
			$szSQL .= static::getQuotedName( $nDBType, $oIndex );
		}

		return $szSQL;
	}
}
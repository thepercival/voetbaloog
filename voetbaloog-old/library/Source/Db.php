<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license	http://www.gnu.org/licenses/gpl.txt
 * @version	$Id: Db.php 4559 2019-08-13 09:57:58Z thepercival $
 * @since	  File available since Release 4.0
 * @package	Source
 */

/**
 * @package Source
 */
class Source_Db implements Source_Db_Interface, Patterns_Singleton_Interface
{
	private static $m_objSingleton;
	CONST ACTION_NONE = 0;
	CONST ACTION_INSERT = 1;
	CONST ACTION_UPDATE = 2;
	CONST ACTION_DELETE = 4;

	CONST DT_VARCHAR = 1;
	CONST DT_INT = 2;
	CONST DT_DATETIME = 3;
	CONST DT_FLOAT = 4;
	CONST DT_BINARY = 5;

	private static $m_arrTablesWithMetaData = array();

	protected function __construct()
	{
	}

	/**
	 * @see Patterns_Singleton_Interface::__clone()
	 */
	public function __clone()
	{
		trigger_error("Cloning is not allowed.", E_USER_ERROR);
	}

	/**
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

	public static function getParamName( $nDbType, $nCounter )
	{
		if ( $nDbType === Source_Db_SqlSyntaxFactory::ORACLE )
			return ":" . $nCounter;
		return "?";
	}

	public static function toSqlString( $vtVariant, bool $bForWriting = true, bool $bPrepared = false, int $nDbType = null )
	{
		$vtRetVal = "";
		if ( $vtVariant === null )
		{
			if ( $bPrepared === false )
				$vtRetVal = "NULL";
			else
				$vtRetVal = null;
		}
		elseif ( is_string( $vtVariant ) )
		{
			if ( $bForWriting and $nDbType === Source_Db_SqlSyntaxFactory::MYSQL )
				$vtVariant = stripslashes ( $vtVariant );

			if ( $bPrepared === false )
			{
				if ( $bForWriting )
				{
					$vtVariant = static::convertQuotes( $vtVariant, $nDbType );
				}
				$vtRetVal = "'".$vtVariant."'";
			}
			else
				$vtRetVal = $vtVariant;
		}
		elseif ( is_float( $vtVariant ) or is_int( $vtVariant ) )
		{
			$vtRetVal = $vtVariant;
		}
		elseif ( is_bool( $vtVariant ) )
		{
			$vtRetVal = ( $vtVariant === true ) ? 1 : 0;
		}
		elseif ( is_object( $vtVariant ) )
		{
			if ( $vtVariant instanceof Patterns_Collection )
			{
				foreach ( $vtVariant as $Item )
					$vtRetVal .= self::toSqlString( $Item, $bForWriting, $bPrepared ).", ";
				// Should only be done here : put single-quotes around items ???
				$vtRetVal = substr( $vtRetVal, 0, strlen( $vtRetVal ) - strlen( ", " ) );

				//if ( $objSearchOperator !== null and $objSearchOperator->getId() === "EqualTo" )
				$vtRetVal = "( ".$vtRetVal." )";
			}
			elseif ( $vtVariant instanceof Agenda_DateTime )
			{
				$vtRetVal = self::toSqlString( (string)$vtVariant, $bForWriting, $bPrepared/*, $nDbType*/ );
			}
			else
			{
				$vtRetVal = self::toSqlString( $vtVariant->getId(), $bForWriting, $bPrepared/*, $nDbType*/ );
			}
		}
		else
		{
			throw new Exception ("Error in ToSqlString : Could not correct type " . gettype($vtVariant));
		}

		return $vtRetVal;
	}

    /**
     * @param string $sString
     * @param int $nDbType
     * @return mixed|string
     */
	public static function convertQuotes( string $sString, int $nDbType = null )
	{
		if ( $nDbType === Source_Db_SqlSyntaxFactory::MYSQL )
			return addslashes( $sString );
		return str_replace( "'", "''", $sString );
	}

	/**
	 * @see Source_Db_Interface::searchOperatorToSqlString()
	**/
	public static function searchOperatorToSqlString( $objSearchOperator, $nType )
	{
		if ( $objSearchOperator->getId() === "EqualTo" )
		{
			if ( $nType === 0 )
				return "IS";
			elseif( $nType === 2 )
				return "IN";
			return "=";
		}
		elseif ( $objSearchOperator->getId() === "NotEqualTo" )
		{
			if ( $nType === 0 )
				return "IS NOT";
			elseif( $nType === 2 )
				return "NOT IN";
			return "<>";
		}
		elseif ( $objSearchOperator->getId() === "StartsWith" )
		{
			return "LIKE";
		}
		elseif ( $objSearchOperator->getId() === "EndsWith" )
		{
			return "LIKE";
		}
		elseif ( $objSearchOperator->getId() === "Like" )
		{
			return "LIKE";
		}
		elseif ( $objSearchOperator->getId() === "NotLike" )
		{
			return "NOT LIKE";
		}
		elseif ( $objSearchOperator->getId() === "GreaterThan" )
		{
			return ">";
		}
		elseif ( $objSearchOperator->getId() === "GreaterThanOrEqualTo" )
		{
			return ">=";
		}
		elseif ( $objSearchOperator->getId() === "SmallerThan" )
		{
			return "<";
		}
		elseif ( $objSearchOperator->getId() === "SmallerThanOrEqualTo" )
		{
			return "<=";
		}
		elseif ( $objSearchOperator->getId() === "BinaryIn" )
		{
			return "&";
		}
	}

    /**
     * @param Construction_Option_Filter $objConstructionOptionFilter
     * @param string $sColumnName
     * @param array $arrBindVars
     * @param int $nDbType
     * @return string
     * @throws Exception
     */
	public static function toSingleWhereClause( Construction_Option_Filter $objConstructionOptionFilter, string $sColumnName, array &$arrBindVars, int $nDbType )
	{
		$szObjectProperty = $objConstructionOptionFilter->getObjectProperty();
		if ( $szObjectProperty === null )
			return "";

		$szRet = $sColumnName;

		$szRet .= " ";

		$nType = 0;
		$vtValue = $objConstructionOptionFilter->getValue();
		if ( $vtValue !== null )
		{
			$nType = 1;
			if ( $vtValue instanceof Patterns_Collection_Interface )
			{
				$nType = 2;
			}
		}
		$objSearchOperator = $objConstructionOptionFilter->getSearchOperator();
		$szRet .= static::searchOperatorToSqlString( $objSearchOperator, $nType );
		$szRet .= " ";

		$vtPost = null;
		// $vtValue = $objConstructionOptionFilter->getValue();
		if ( $objSearchOperator !== null )
		{
			if ( $objSearchOperator->getId() === "Like" )
				$vtValue = "%".$vtValue."%";
			elseif ( $objSearchOperator->getId() === "StartsWith" )
				$vtValue = $vtValue."%";
			elseif ( $objSearchOperator->getId() === "EndsWith" )
				$vtValue = "%".$vtValue;
			elseif ( $objSearchOperator->getId() === "BinaryIn" )
				$vtPost = " = ".$sColumnName;
		}

		if ( $nType === 0 )
		{
			$vtValue = static::toSqlString( $vtValue, false, true, $nDbType );
			$szRet .= "NULL";
		}
		else if ( $nType === 1 )
		{
			$vtValue = static::toSqlString( $vtValue, false, true, $nDbType );
			$nNrOfBindVars = count( $arrBindVars );
			$arrBindVars[] = $vtValue;
			$szRet .= static::getParamName( $nDbType, $nNrOfBindVars );
		}
		else // if ( $nType === 2
		{
			// $szRet .= $vtValue;
			if ( $vtValue instanceof Patterns_Collection )
			{
				$sTmp = "";
				foreach ( $vtValue as $vtValueIt ) {
					$sValue = static::toSqlString( $vtValueIt, false, true, $nDbType );
					$nNrOfBindVars = count( $arrBindVars );
					$arrBindVars[] = $sValue;
					$sTmp .= static::getParamName( $nDbType, $nNrOfBindVars ) . ", ";
				}
				$sTmp = substr( $sTmp, 0, strlen( $sTmp ) - strlen( ", " ) );
				//if ( $objSearchOperator !== null and $objSearchOperator->getId() === "EqualTo" )
				$szRet .= "( ".$sTmp." )";
			}
		}

		if ( $vtPost !== null )
			$szRet .= $vtPost;
		$szRet .= " ";

		return $szRet;
	}

	public static function addTableMetaData( $oDb, $oTable )
	{
		$sTableName = $oTable->getName();

		if ( array_key_exists( $sTableName, static::$m_arrTablesWithMetaData ) === true )
			return;

		$arrTableMetaData = $oDb->describetable( $sTableName );

		$oColumns = $oTable->getColumns();

		foreach( $arrTableMetaData as $sColumnName => $arrColumn )
		{
			$oColumn = $oColumns[ $sColumnName ];
			if ( $oColumn === null )
				$oColumn = $oTable->createColumn( $sColumnName );

			switch ( $arrColumn['DATA_TYPE'] ) {
				case 'varchar':
				case 'nvarchar':
					$oColumn->putDatatype( Source_Db::DT_VARCHAR );
					break;
				case 'mediumint':
				case 'int':
				case 'tinyint':
				case 'smallint':
					$oColumn->putDatatype( Source_Db::DT_INT );
					break;
				case 'datetime':
					$oColumn->putDatatype( Source_Db::DT_DATETIME );
					break;
				case 'decimal':
					$oColumn->putDatatype( Source_Db::DT_FLOAT );
					break;
				case 'blob':
				case 'image':
					$oColumn->putDatatype( Source_Db::DT_BINARY );
					break;
				default:
					break;
			}
		}
	}

	public static function initDb( $cfgDB )
	{
		$db = null;
		if ( $cfgDB !== null )
		{
			$nDbType = Source_Db_SqlSyntaxFactory::getDbType( $cfgDB->adapter );

			$dbparams = $cfgDB->config->toArray();
			if ( $nDbType === Source_Db_SqlSyntaxFactory::MYSQL )
				$dbparams['driver_options'] = array(
						PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
						PDO::ATTR_EMULATE_PREPARES => false,
						PDO::ATTR_STRINGIFY_FETCHES => false
				);

				// Load Database
				$db = Zend_Db::factory( $cfgDB->adapter, $dbparams );

//                if ( $nDbType === Source_Db_SqlSyntaxFactory::MYSQL )
//                {
//                    if ( APPLICATION_ENV === "development" ) {
//                        $db->query("RESET QUERY CACHE");
//                    }
//                }
				if ( $nDbType === Source_Db_SqlSyntaxFactory::MSSQL )
				{
					$db->query("SET ANSI_NULLS ON");
					$db->query("SET QUOTED_IDENTIFIER ON");
					$db->query("SET CONCAT_NULL_YIELDS_NULL ON");
					$db->query("SET ANSI_WARNINGS ON");
					$db->query("SET ANSI_PADDING ON");
					$db->query("SET DATEFIRST 1"); // voor dagvandeweek
				}
				else if ( $nDbType === Source_Db_SqlSyntaxFactory::ORACLE )
				{
					$db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
				}
		}
		return $db;
	}
}
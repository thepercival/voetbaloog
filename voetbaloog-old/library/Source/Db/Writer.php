<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Writer.php 4560 2019-08-21 06:57:06Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
class Source_Db_Writer extends Patterns_ObserverObject implements Source_Db_Writer_Interface, Source_Writer_Interface
{
	protected $m_objDatabase;					// Zend_Db
	protected $m_szSchema;						// string

	protected $m_objFactory;					// Object_Factory_Db
	protected $m_objPersistance;				// Source_db_Persistance
	protected $m_szTableName;					// string
	protected $m_objObjectPropertiesToWrite;	// Patterns_Collection
	protected $m_nDbType;						// int

	public function __construct( $objFactory )
	{
		parent::__construct();

		$this->putId( get_called_class() );

		$this->m_objFactory = $objFactory;

		if ( $this->m_objDatabase === null )
			$this->m_objDatabase = Zend_Registry::get("db");

		$arrOptions = $this->m_objDatabase->getConfig();
		if ( array_key_exists( "schema", $arrOptions) and strlen ( $arrOptions["schema"] ) > 0 )
			$this->m_szSchema = $arrOptions["schema"];
	}

	protected function getDbType()
	{
		if ( $this->m_nDbType === null )
			$this->m_nDbType = Source_Db_SqlSyntaxFactory::getDbType( $this->m_objDatabase );
		return $this->m_nDbType;
	}

	/**
	 * @see Source_Writer_Interface::getObjectPropertiesToWrite()
	 */
	public function getObjectPropertiesToWrite()
	{
		if ( $this->m_objObjectPropertiesToWrite === null )
		{
			$this->m_objObjectPropertiesToWrite = Patterns_Factory::createCollection();

			$oDbPersistance = $this->getPersistance();

			foreach( $oDbPersistance as $sObjectProperty => $oColumn )
			{
				$oObjectProperty = Patterns_Factory::createIdable( $sObjectProperty );
				$this->m_objObjectPropertiesToWrite->add( $oObjectProperty );
			}
		}

		return $this->m_objObjectPropertiesToWrite;
	}

	/**
	 * @see Source_Writer_Interface::write()
	 */
	public function write( $nBatchAction = null )
	{
		$oObjectChanges = $this->getObjectChanges();

		$oStmt = null;
		if ( $nBatchAction !== null )
		{
			$sQuery = $this->getPreparedQuery( $nBatchAction );
			$oStmt = $this->m_objDatabase->prepare( $sQuery );
		}

		try
		{
			$oDbPersistance = $this->getPersistance();
			if ( $oDbPersistance !== null )
				Source_Db::addTableMetaData( $this->m_objDatabase, $oDbPersistance->getTable() );

			// $this->m_objDatabase->beginTransaction();
			foreach ( $oObjectChanges as $oObjectChange )
			{
				$vtRet = null;

				switch ( $oObjectChange->getActionName() )
				{
					case Source_Db::ACTION_INSERT:
						$vtRet = $this->add( $oObjectChange, $oStmt );
						break;
					case Source_Db::ACTION_DELETE:
						$vtRet = $this->delete( $oObjectChange );
						break;
					case Source_Db::ACTION_UPDATE:
						$vtRet = $this->update( $oObjectChange );
						break;
				}

				if ( $vtRet === false )
				{
					// $this->m_objDatabase->rollback();
					$oObjectChanges->flush();
					return false;
				}
			}

			// $this->m_objDatabase->commit();
			$oObjectChanges->flush();
			return true;
		}
		catch ( Exception $e )
		{
			// $this->m_objDatabase->rollback();
			$sMessage = $e->getMessage();

			if ( $this->getDbType() === Source_Db_SqlSyntaxFactory::MSSQL AND APPLICATION_ENV == 'development' )
			{
				if ( file_exists ( sys_get_temp_dir() . DIRECTORY_SEPARATOR . "freetds.log" ) === true ) {
					$sMessage .= " :: " . Source_Db_Log::tail( sys_get_temp_dir() . DIRECTORY_SEPARATOR . "freetds.log", "msgno" );
				}
			}
			$oObjectChanges->flush();
			throw new Exception( "dbwriting gave error(rollback) : ".$sMessage, E_ERROR );
		}

		return false;
	}

	/**
	 * @see Source_Db_Writer_Interface::removeObjects()
	 */
	public function removeObjects( $szWhereClause, $arrBindVars )
	{
		$sDeleteQuery =
			"DELETE ".
			"FROM	".$this->getTableName()." ".
			"WHERE	".$szWhereClause;

		return $this->deletehelper( $sDeleteQuery, $arrBindVars );
	}

	protected function execute( $sQuery, $arrBindVars, $oStmt = null )
	{
		if ( $oStmt === null )
			$oStmt = $this->m_objDatabase->prepare( $sQuery );

		if ( $oStmt === false )
			return false;

		$bTmp = $oStmt->execute( $arrBindVars );
		if ( $oStmt instanceof Zend_Db_Statement_Sqlsrv )
			return true;
		return $bTmp;
	}

	protected function add( $oObjectChange, $oStmt = null )
	{
		$oObject = $oObjectChange->getObject();

		$arrBindVars = null;
		try
		{
			if ( $oStmt === null )
				$oStmt = $this->m_objDatabase->prepare( $this->getPreparedQuery( Source_Db::ACTION_INSERT ) );

			$arrBindVars = $this->getBindVars( $oObject );
			$this->execute( null, $arrBindVars, $oStmt );
			if ( $this->getAutoKey() === true )
			{
				$nLastId = (int) $this->m_objDatabase->lastInsertId( $this->getTableName() );
				$oObjectChange->putSystemId( $nLastId );
				$oObject->putId( $oObjectChange->getSystemId() );
			}
		}
		catch ( Exception $e)
		{
		    $sBindVars = $arrBindVars !== null ? ( is_array( $arrBindVars ) ? implode( ",", $arrBindVars ): '' ) : '';
			throw new Exception( $e->getMessage().", For Query: ". $this->getPreparedQuery( Source_Db::ACTION_INSERT ) . " with binds : " . $sBindVars, E_ERROR );
		}

		return true;
	}

	protected function getBindVars( $oObject )
	{
		$arrBindVars = array();

		$sIdProperty = $this->m_objFactory->getIdProperty();

		$szColumns = null;
		$szValues = null;
		$nPostFix = 0;

		$objObjectProperties = $this->getObjectPropertiesToWrite();
		foreach ( $objObjectProperties as $sObjectPropertyId => $objObjectProperty )
		{
			if ( $sIdProperty === $sObjectPropertyId )
			{
				if ( $this->getAutoKey() === true )
					continue;
			}
			$arrBindVars[] = $this->toSqlString( MetaData_Factory::getValue( $oObject, $sObjectPropertyId ), true );
		}
		return $arrBindVars;
	}

	protected function getPreparedQuery( $nAction )
	{
		if ( $nAction == Source_Db::ACTION_INSERT )
		{
			$szIdProperty = $this->m_objFactory->getIdProperty();

			$szColumns = null;
			$szValues = null;
			$nDbType = $this->getDbType(); $nI = 0; // for paramname

			$objObjectProperties = $this->getObjectPropertiesToWrite();
			foreach ( $objObjectProperties as $szObjectPropertyId => $objObjectProperty )
			{
				if ( $szIdProperty === $szObjectPropertyId )
				{
					if ( $this->getAutoKey() === true )
						continue;
				}

				if ( $szColumns !== null )
				{
					$szColumns .= ", ";
					$szValues .= ", ";
				}

				$oColumn = $this->getColumn( $szObjectPropertyId );

				$sParamName = Source_Db::getParamName( $nDbType, $nI++ );
				if ( $oColumn->getDatatype() === Source_Db::DT_BINARY and $nDbType === Source_Db_SqlSyntaxFactory::MSSQL )
				{
					$szValues .= "CONVERT(VARBINARY(MAX), ".$sParamName.")";
				}
				else
				{
					$szValues .= $sParamName;
				}
				$szColumns .= $oColumn->getName();
			}

			$szTableName = $this->getTableName();
			if ( $this->m_szSchema !== null )
				$szTableName = $this->m_szSchema.".".$szTableName;

			return
			$this->getDefaultInsertQuery() . $szTableName."( ".$szColumns." ) ".
			"VALUES( ".$szValues." )";
		}

		throw new Exception( "Not implemented yet .....", E_ERROR );
	}

	protected function getDefaultInsertQuery()
	{
		return "INSERT INTO ";
	}

	protected function getAutoKey()
	{
		return true;
	}

  	protected function getPersistance()
	{
		if ( $this->m_objPersistance === null )
			$this->m_objPersistance = $this->m_objFactory->createDbPersistance();
		return $this->m_objPersistance;
	}

  	protected function getTableName()
	{
		if ( $this->m_szTableName === null )
		{
			$oPersistance = $this->getPersistance();
			if ( $oPersistance === null )
				throw new Exception( "No persistance returned( ". get_class() ." )!", E_ERROR );
			$this->m_szTableName = $oPersistance->getTableName();
		}
		return $this->m_szTableName;
	}

	protected function update( $objObjectChange )
	{
		$szObjectPropertyId = $this->m_objFactory->getIdProperty();

		$szColumnName = $this->getColumnName( $szObjectPropertyId );

		$szWhere = $szColumnName." = ".$this->toSqlString( $objObjectChange->getSystemId() );

		return $this->updatehelper( $objObjectChange, $szWhere );
	}

	protected function updatehelper( $objObjectChange, $szWhere )
	{
		$szObjectPropertyId = $objObjectChange->getObjectProperty();

		$szTableName = $this->getTableName();

		if ( $this->m_szSchema !== null )
			$szTableName = $this->m_szSchema.".".$szTableName;

		$vtNewValue = $objObjectChange->getNewValue();

		$oColumn = $this->getColumn( $szObjectPropertyId );

		$szColumnName = $oColumn->getName();

		$nDbType = $this->getDbType();
		$szParamName = Source_Db::getParamName( $nDbType, 0 );

		if ( $oColumn->getDatatype() !== Source_Db::DT_BINARY )
		{
			$vtNewValue = $this->toSqlString( $vtNewValue, true );
		}

		$sUpdateQuery =
			"UPDATE ".$szTableName." ".
			"SET	".$szColumnName." = ".$szParamName." ".
			"WHERE ".$szWhere
		;


//		if ( $oColumn->getDatatype() === Source_Db::DT_BINARY and $nDbType === Source_Db_SqlSyntaxFactory::MSSQL )
//		{
//			$vtNewValue = array( $vtNewValue, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY), SQLSRV_SQLTYPE_VARBINARY('max') );
//		}

		try
		{
			return $this->execute( $sUpdateQuery, array( $vtNewValue ) );
		}
		catch ( Exception $e )
		{
			throw new Exception( $e->getMessage().", For Update: Set ".$szColumnName."=".$vtNewValue." WHERE ".$szWhere , E_ERROR );
		}

		return false;
	}

	protected function delete( $objObjectChange )
	{
		$szObjectPropertyId = $this->m_objFactory->getIdProperty();

		$szColumnName = $this->getColumnName( $szObjectPropertyId, true );
		if ( $szColumnName === false )
			throw new Exception( "Voor eigenschap ".$szObjectPropertyId." kon geen kolom worden gevonden(".get_called_class().")" , E_ERROR );

		$szWhere = $szColumnName . " = " . Source_Db::getParamName( $this->getDbType(), 0 );

		$arrBindVars = array();
		$arrBindVars[] = $this->toSqlString( $objObjectChange->getSystemId(), true );

		$szDeleteQuery =
				"DELETE ".
				"FROM	".$this->getTableName()." ".
				"WHERE	".$szWhere;

		return $this->deletehelper( $szDeleteQuery, $arrBindVars );
	}

	protected function toSqlString( $vtVariant, $bPrepared = false )
	{
		return Source_Db::toSqlString( $vtVariant, true, $bPrepared, $this->getDbType() );
	}

	protected function deletehelper( $sDeleteQuery, $arrBindVars )
	{
		try
		{
			return $this->execute( $sDeleteQuery, $arrBindVars );
		}
		catch ( Exception $e )
		{
			throw new Exception( $e->getMessage().", For DeleteQuery: ".$sDeleteQuery, E_ERROR );
		}

		return false;
	}

	/**
	 * helper function
	 * @param string	$sObjectProperty	The ObjectProperty
	 * return Source_Db_Object_Column The Column
	 */
	protected function getColumn( $sObjectProperty )
	{
		$oDbPersistance = $this->getPersistance();

		$oColumn = $oDbPersistance[ $sObjectProperty ];

		if ( $oColumn === null )
		{
			throw new Exception( "Voor eigenschap ".$sObjectProperty." kon geen kolom worden gevonden(".get_called_class().")" , E_ERROR );
		}

		return $oColumn;
	}

    /**
     * @param string $sObjectProperty
     * @param bool $bPlusTableName
     * @return mixed
     * @throws Exception
     */
	protected function getColumnName( string $sObjectProperty, bool $bPlusTableName = false )
	{
		$oColumn = $this->getColumn( $sObjectProperty );
		return $oColumn->getName( $bPlusTableName );
	}
}

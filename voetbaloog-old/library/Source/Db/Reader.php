<?php

/**
 * @copyright2007 Coen Dunnink
 * @licensehttp://www.gnu.org/licenses/gpl.txt
 * @version$Id: Reader.php 4603 2021-05-17 13:48:14Z thepercival $
 * @sinceFile available since Release 4.0
 * @packageSource
 */

/**
 * @package Source
 */
class Source_Db_Reader extends Source_Reader implements Source_Db_Reader_Interface
{
	protected $m_objDatabase;			// Zend_Db
	protected $m_szSchema;				// string
	protected $m_arrBindVars;			// array
	protected $m_nDbType;				// int

	protected $m_arrAddedPersistances;	// array
	protected $m_oDbPersistances; 		// Patterns_Collection
	protected $m_oPersistance;			// Source_db_Persistance

    CONST COUNT_COLUMN = "ccount";

	public function __construct( $objFactory )
	{
		parent::__construct();

		$this->m_objFactory = $objFactory;

		if ( Zend_Registry::isRegistered( "db" ) )
			$this->m_objDatabase = Zend_Registry::get("db");

		$this->m_arrBindVars = array();
		$this->m_arrAddedPersistances = array();
	}

	// tmp until performance issues are resolved
	public function putDatabase( $oDatabase )
	{
		$this->m_objDatabase = $oDatabase;
	}

	protected function getDbType()
	{
		if ( $this->m_nDbType === null )
			$this->m_nDbType = Source_Db_SqlSyntaxFactory::getDbType( $this->m_objDatabase );
		return $this->m_nDbType;
	}

	protected function toSqlString( $vtVariant, $bPrepared = false )
	{
		$nDbType = Source_Db_SqlSyntaxFactory::getDbType( $this->m_objDatabase );

		return Source_Db::toSqlString( $vtVariant, false, $bPrepared, $nDbType );
	}

	/**
	 * @see Source_Reader_Interface::getObjectPropertiesToRead()
	 */
	public function getObjectPropertiesToRead(): Patterns_Collection
	{
		if ( $this->m_objObjectProperties === null )
		{
			$this->m_objObjectProperties = Patterns_Factory::createCollection();

			$oObjectProperties = $this->getPersistance();

			foreach( $oObjectProperties as $sObjectProperty => $oColumn )
			{
				$objObjectProperty = Patterns_Factory::createIdable( $sObjectProperty );
				$this->m_objObjectProperties->add( $objObjectProperty );
			}
		}

		return $this->m_objObjectProperties;
	}

	/**
	 * @see Source_Reader_Interface::createObjects()
	 */
	public function createObjects( Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		$oSelect = $this->getQuery( $oOptions );
		$oObjects = $this->createObjectsHelper( $oSelect, $this->getCustomReadProperties( $oOptions ) );
		return $oObjects;
	}

	/**
	 * @see Source_Reader_Interface::createObject()
	 */
	public function createObject( Construction_Option_Collection $oOptions = null ): Patterns_Collection
	{
		throw new Exception( "This functionality is handled by the factory", E_ERROR );
	}


	/**
	 * @see Source_Db_Reader_Interface::getQuery()
	 */
	public function getQuery( Construction_Option_Collection $oOptions = null ): Zend_Db_Select
	{
		$oSelect = $this->getSelectFrom();
		$this->addWhereOrderBy( $oSelect, $oOptions );
		return $oSelect;
	}

	/**
	 * @see Source_Db_Reader_Interface::getCountQuery()
	 */
	public function getCountQuery( Construction_Option_Collection $oOptions = null ): Zend_Db_Select
	{
		$oSelect = $this->getSelectFrom( true );
		$this->addWhereOrderBy( $oSelect, $oOptions );
		return $oSelect;
	}

	/**
	 * @see Source_Db_Reader_Interface::createObjectFromRow()
	 */
	protected function createObjectFromRow( $row, $oObjectProperties )
	{
		$oObject = $this->m_objFactory->createObject();

		foreach ( $oObjectProperties as $oObjectProperty )
		{
			$vtValue = $this->getValue( $row, $oObjectProperty->getId() );
			MetaData_Factory::putValue( $oObject, $oObjectProperty->getId(), $vtValue );
		}
		return $oObject;
	}

    /**
     * @param Zend_Db_Select $oSelect
     * @param Patterns_Collection $oReadProperties
     * @return Patterns_Collection
     * @throws Exception
     */
	protected function createObjectsHelper( Zend_Db_Select $oSelect, Patterns_Collection $oReadProperties = null ): Patterns_Collection
	{
		$oObjects = $this->m_objFactory->createObjects();

		$oObjectProperties = $oReadProperties;
		if ( $oObjectProperties === null )
			$oObjectProperties = $this->getObjectPropertiesToRead();

		$bPoolEnabled = $this->m_objFactory->isPoolEnabled();
		$oPool = $this->m_objFactory->getPool();

		try
		{
			$stmt = $this->m_objDatabase->prepare( $oSelect );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			$sObjectPropertyId = $this->m_objFactory->getIdProperty();

			while ( $row = $stmt->fetch() )
			{
				$szVtId = $this->getValue( $row, $sObjectPropertyId );

				$oObject = null;
				if ( $bPoolEnabled === true and $szVtId !== null )
					$oObject = $oPool[ $szVtId ];

				if ( $oObject === null )
				{
					$oObject = $this->createObjectFromRow( $row, $oObjectProperties );
					if ( $bPoolEnabled === true )
						$oPool->add( $oObject );
				}
		 		$oObjects->add( $oObject );
			}
		}
		catch ( Exception $e )
		{
			$sMessage = $e->getMessage().", For Query: ".(string) $oSelect;
			if ( constant("APPLICATION_ENV") !== "production" )
				$sMessage .= " with binds : " . implode( ",", $this->m_arrBindVars );
			throw new Exception( $sMessage, E_ERROR );
		}

		return $oObjects;
	}

	/**
	 * @see Source_Reader_Interface::createArray()
	 */
	public function createArray( $oOptions, $bLowerCase )
	{
		$oSelect = $this->getQuery( $oOptions );
		return $this->createArrayHelper( $oSelect, $bLowerCase, $this->getCustomReadProperties( $oOptions ) );
	}

	/**
	 * @see Source_Reader_Interface::createSingleArray()
	 */
	public function createSingleArray( $oOptions, $bLowerCase )
	{
		$oOptions->addLimit( 1 );
		$oSelect = $this->getQuery( $oOptions );
		$arrObjects = $this->createArrayHelper( $oSelect, $bLowerCase, $this->getCustomReadProperties( $oOptions ) );
		$arrObjects = reset( $arrObjects );
		if ( $arrObjects === false )
			return array();
		return $arrObjects;
	}

    /**
     * @param Zend_Db_Select $oSelect
     * @param bool $bLowerCase
     * @param null $oReadProperties
     * @return array
     * @throws Zend_Db_Select_Exception
     */
	protected function createArrayHelper( Zend_Db_Select $oSelect, bool $bLowerCase, $oReadProperties = null ): array
	{
		$oObjectProperties = $oReadProperties;
		if ( $oObjectProperties === null )
			$oObjectProperties = $this->getObjectPropertiesToRead();

		$arrColumns = array();
		{
			foreach( $oObjectProperties as $oObjectProperty ) {
				$sPropertyName = MetaData_Factory::getPropertyName( $oObjectProperty->getId() );
				if ( $bLowerCase === true )
					$sPropertyName = strtolower( $sPropertyName );
				$arrColumns[] = $this->getColumnName( $oObjectProperty->getId(), true )." AS " . $sPropertyName;
			}
		}
		if ( count( $arrColumns ) > 0 ) {
			$oSelect->reset( Zend_Db_Select::COLUMNS );
			$oSelect->columns( $arrColumns );
		}
		return $this->createArrayHelperExecute( $oSelect );
	}

    /**
     * @param Zend_Db_Select $oSelect
     * @return array
     * @throws Exception
     */
	protected function createArrayHelperExecute(Zend_Db_Select $oSelect ): array
	{
		try
		{
			$stmt = $this->m_objDatabase->prepare( $oSelect );
			$stmt->execute( $this->m_arrBindVars );
			$this->m_arrBindVars = array();

			return $stmt->fetchAll();
		}
		catch ( Exception $e )
		{
			$sMessage = $e->getMessage().", For Query: ".(string) $oSelect;
			if ( constant("APPLICATION_ENV") !== "production" )
				$sMessage .= " with binds : " . implode( ",", $this->m_arrBindVars );
			throw new Exception( $sMessage, E_ERROR );
		}
		return array();
	}

    /**
     * @see Source_Db_Reader_Interface::getNrOfObjects()
     */
    public function getNrOfObjects( Construction_Option_Collection $oOptions = null )
    {
        $oSelect = $this->getCountQuery( $oOptions );
        return $this->getNrOfObjectsHelper( $oSelect );
    }

    /**
     * @see Source_Db_Reader_Interface::getNrOfObjects()
     */
    protected function getNrOfObjectsHelper( $oSelect )
    {
        try
        {
            $stmt = $this->m_objDatabase->prepare( $oSelect );
            $stmt->execute( $this->m_arrBindVars );
            $this->m_arrBindVars = array();

            if( $row = $stmt->fetch() ) {
                return (int) $row[ static::COUNT_COLUMN ];
            }
        }
        catch ( Exception $e )
        {
            throw new Exception( $e->getMessage().", For Query: ".(string) $oSelect, E_ERROR );
        }
        return -1;
    }

	/**
	 * @see Source_Db_Reader_Interface::toWhereClause()
	 */
    public function toWhereClause( Construction_Option_Collection $oOptions, &$arrBindVars = null, $szRet = "", $bOr = false )
	{
		if ( $arrBindVars === null ) {
			$arrBindVars = &$this->m_arrBindVars;
        }
		$nI = 0;

		foreach ( $oOptions as $objConstructionOptionFilter )
		{
			if ( $objConstructionOptionFilter instanceof Construction_Option_Filter )
			{
				$sObjectProperty = $objConstructionOptionFilter->getObjectProperty();
				if ( $sObjectProperty === null )
					continue;

				$sColumnName = $this->getColumnName( $sObjectProperty );
				$szSingleWhereClause = Source_Db::toSingleWhereClause( $objConstructionOptionFilter, $sColumnName, $arrBindVars, $this->getDbType() );
                if ( strlen( $szSingleWhereClause ) > 0 )
                {
                    if ( $nI > 0 )
                    {
                        if ( $bOr === true )
                            $szRet .= "OR ";
                        else
                            $szRet .= "AND ";
                    }
                    $szRet .= $szSingleWhereClause;
                }
			}
			elseif ( $objConstructionOptionFilter instanceof Construction_Option_Collection )
			{
				if ( $nI > 0 )
				{
					if ( $bOr === true )
						$szRet .= "OR ( ";
					else
						$szRet .= "AND ( ";
				}
				else
				{
					$szRet .= " ( ";
				}
				$szRet = $this->toWhereClause( $objConstructionOptionFilter, $arrBindVars, $szRet, !$bOr );
				$szRet .= " ) ";
			}
			else
			{
				continue;
			}
			$nI++;
		}

		return $szRet;
	}

	/**
	 * @see Source_Db_Reader_Interface::addOrderBy()
	 */
    public function addOrderBy( Zend_Db_Select $oSelect, Construction_Option_Collection $objConstructionOptionOrders )
	{
		foreach ( $objConstructionOptionOrders as $objConstructionOptionOrder )
		{
			if ( $objConstructionOptionOrder instanceof Construction_Option_Order )
			{
				$szObjectProperty = $objConstructionOptionOrder->getObjectProperty();

				$szColumnName = $this->getColumnName( $szObjectProperty );

				$szOrder = "";
				if ( $objConstructionOptionOrder->getDescending() )
					$szOrder = " DESC";

                $oSelect->order( array( $szColumnName.$szOrder ) );
			}
		}

		return true;
	}

	private function getPersistances()
	{
		if ( $this->m_oDbPersistances === null )
		{
			$this->m_oDbPersistances = Patterns_Factory::createCollection();
			$oPersistance = $this->getPersistance();
			$this->addPersistance( $oPersistance );

			Source_Db::addTableMetaData( $this->m_objDatabase, $oPersistance->getTable() );
		}
		return $this->m_oDbPersistances;
	}

	public function addPersistance( Source_Db_Persistance $oPersistance )
	{
		if ( array_key_exists( $oPersistance->getId(), $this->m_arrAddedPersistances ) )
			return;

		$oP = $this->getPersistances();
		foreach ( $oPersistance as $sObjectProperty => $oColumn )
		{
			if ( $oP[ $sObjectProperty ] !== null )
				throw new Exception( "OBJECTPROPERTY ".$sObjectProperty."(=>".$oColumn->getName( true ).") ALREADY ADDED TO PERSISTANCE(".get_called_class().")", E_ERROR );
			$oP[ $sObjectProperty ] = $oColumn;
		}
		$this->m_arrAddedPersistances[ $oPersistance->getId() ] = true;
	}

	protected function getCustomReadProperties( $oOptions )
	{
		if ( $oOptions === null )
			return null;

		$oCustomReadProperties = null;

		if ( $oOptions instanceof Construction_Option_Collection )
		{
			foreach ( $oOptions as $oOption )
			{
				if ( $oOption instanceof Construction_Option_ReadProperty )
				{
					if ( $oCustomReadProperties === null )
						$oCustomReadProperties = Patterns_Factory::createCollection();

					$oCustomReadProperties->add( Patterns_Factory::createIdable( $oOption->getObjectProperty() ) );
				}
			}
		}
		return $oCustomReadProperties;
	}

	protected function getPersistance()
	{
		if ( $this->m_oPersistance === null )
			$this->m_oPersistance = $this->m_objFactory->createDbPersistance();
		return $this->m_oPersistance;
	}

	protected function getTableName()
	{
		return $this->getPersistance()->getTable()->getName();
	}

	protected function getColumns()
	{
		return $this->getPersistance()->getTable()->getColumns();
	}

	protected function getSelectFrom( $bCount = false )
	{
		$objSelect = $this->m_objDatabase->select();

		if ( $bCount === true )
			$objSelect->from( $this->getTableName(), "count(*) as " . Source_Db_Reader::COUNT_COLUMN, $this->m_szSchema );
		else {
			$oObjectProperties = $this->getObjectPropertiesToRead();

			$vtColumns = "*";
			if ( $oObjectProperties->count() > 0 )
				$vtColumns = $this->getSelectColumnNames( $oObjectProperties );

			$objSelect->from( $this->getTableName(), $vtColumns, $this->m_szSchema );
		}

		return $objSelect;
	}

	protected function addWhereOrderBy( Zend_Db_Select $oSelect, Construction_Option_Collection $oOptions = null )
	{
	    if( $oOptions === null ) {
	        return;
        }
	    $sWhere = $this->toWhereClause( $oOptions );
	    if( strlen($sWhere) > 0 ){
            $oSelect->where( $sWhere );
        }


		if ( $this->addOrderBy( $oSelect, $oOptions ) === false )
			throw new Exception( "Could not add orderby-constructionoptions!", E_ERROR );

		if ( $this->addLimit( $oSelect, $oOptions ) === false )
			throw new Exception( "Could not add limit-constructionoptions!", E_ERROR );
	}

	protected function addWhere( Zend_Db_Select $objSelect, Construction_Option_Collection $oOptions = null )
	{
		if ( $oOptions !== null )
			$objSelect->where( $this->toWhereClause( $oOptions ) );
	}

	protected function getValue( $row, $sObjectPropertyId )
	{
		$sColumnName = $this->getColumnName( $sObjectPropertyId, false );

		return $row[ $sColumnName ];
/*		if ( $vtValue === "" )
			return null;

		$oColumns = $this->getColumns();

		if ( $vtValue !== null )
		{
			$nDatatype = $oColumns[ $sColumnName ]->getDatatype();
			if ( $nDatatype === Source_Db::DT_INT )
				$vtValue = (int) $vtValue;
			else if ( $nDatatype === Source_Db::DT_FLOAT )
				$vtValue = (float) $vtValue;
			// else if ( $nDatatype === Source_Db::DT_DATETIME )
			//		$vtValue = Agenda_Factory::createDateTime( $vtValue );
		}

		return $vtValue;*/
	}

	protected function addLimit( $objSelect, $oOptions )
	{
		if ( $oOptions === null )
			return true;

		foreach ( $oOptions as $objConstructionOption )
		{
			if ( $objConstructionOption instanceof Construction_Option_Limit )
			{
				$objSelect->limit( $objConstructionOption->getCount(), $objConstructionOption->getOffSet() );
			}
		}

		return true;
	}

    /**
     * @param Patterns_Collection $objObjectProperties
     * @return array
     */
	protected function getSelectColumnNames( Patterns_Collection $objObjectProperties ): array
	{
		$arrColumnNames = array();

		foreach ( $objObjectProperties as $szObjectPropertyId => $objObjectProperty )
		{
			$szColumnName = $this->getColumnName( $szObjectPropertyId );
			$arrColumnNames[] = $szColumnName;
		}
		return $arrColumnNames;
	}

    /**
     * @param string $sObjectProperty
     * @return Source_Db_Object_Column
     * @throws Exception
     */
	protected function getColumn( string $sObjectProperty ): Source_Db_Object_Column
	{
		$oDbPersistances = $this->getPersistances();

		$oColumn = $oDbPersistances[ $sObjectProperty ];

		if ( $oColumn === null )
		{
			// var_dump( $oDbPersistances ); die();
			throw new Exception( "Voor eigenschap ".$sObjectProperty." kon geen kolom worden gevonden(".get_called_class().")" , E_ERROR );
		}

		return $oColumn;
	}

    /**
     * @param string $sObjectProperty
     * @param bool $bPlusTableName
     * @return string
     * @throws Exception
     */
	protected function getColumnName( string $sObjectProperty, bool $bPlusTableName = true ): string
	{
		$oColumn = $this->getColumn( $sObjectProperty );
		return $oColumn->getName( $bPlusTableName );
	}
}

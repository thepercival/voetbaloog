<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Writer.php 3809 2012-12-04 21:59:51Z thepercival $
 *
 * @package    Source
 */

/**
 * Logtable described in Log.xml
 *
 * @package Source
 */
class Source_Db_Writer_Log extends Source_Db_Writer
{
	protected $m_sLogQuery;	// string

	public function __construct( $objFactory )
	{
		parent::__construct( $objFactory );
	}

	protected function add( $oObjectChange, $oStmt = null )
	{
		$bRetVal = parent::add( $oObjectChange, $oStmt );
		if ( $bRetVal !== true )
			return $bRetVal;

		if ( $this->isValid( $oObjectChange ) )
			return $this->addToLog( $oObjectChange );
		return $bRetVal;
	}

	protected function update( $oObjectChange )
	{
		$bRetVal = parent::update( $oObjectChange );
		if ( $bRetVal !== true )
			return $bRetVal;

		if ( $this->isValid( $oObjectChange ) )
			return $this->addToLog( $oObjectChange );
		return $bRetVal;
	}

	protected function delete( $oObjectChange )
	{
		$bRetVal = parent::delete( $oObjectChange );
		if ( $bRetVal !== true )
			return $bRetVal;

		if ( $this->isValid( $oObjectChange ) )
			return $this->addToLog( $oObjectChange );
		return $bRetVal;
	}

	protected function addToLog( $oObjectChange )
	{
		$sLogQuery = $this->getLogQuery();
		$arrBindVars = null;

		try
		{
			$stmt = $this->m_objDatabase->prepare( $sLogQuery );
			if ( $stmt === false )
				return false;

			$arrBindVars = $this->getLogBindVars( $oObjectChange );

			$bTmp = $stmt->execute( $arrBindVars );
			if ( $stmt instanceof Zend_Db_Statement_Sqlsrv )
				return true;
			return $bTmp;
		}
		catch ( Exception $e )
		{
			$sMessage = $e->getMessage();

			$nDbType = Source_Db_SqlSyntaxFactory::getDbType( $this->m_objDatabase );
			if ( $nDbType === Source_Db_SqlSyntaxFactory::MSSQL AND APPLICATION_ENV == 'development' )
			{
				if ( file_exists ( sys_get_temp_dir() . DIRECTORY_SEPARATOR . "freetds.log" ) === true ) {
					$sMessage .= " :: " . Source_Db_Log::tail( sys_get_temp_dir() . DIRECTORY_SEPARATOR . "freetds.log", "msgno" );
				}
			}
			throw new Exception( "logquery ".$sLogQuery." with binds : " . implode( ",", $arrBindVars ) . " gave error : ".$sMessage, E_ERROR );
		}

		return false;
	}

	protected function getLogQuery()
	{
		if( $this->m_sLogQuery === null )
		{
			$nDbType = $this->getDbType(); $nI = 0; // for paramname
			$this->m_sLogQuery =
				"INSERT INTO ".$this->getLogTableName().
					"( UserLoginName, ReqUri, MetaData, TableName, MasterId, RelatedId1, RelatedId2, Action, OldValue, NewValue, LogDateTime, ObjectProperty ) ".
				"VALUES( ".Source_Db::getParamName( $nDbType, $nI++ ).", ".Source_Db::getParamName( $nDbType, $nI++ ).
					", ".Source_Db::getParamName( $nDbType, $nI++ ).", ".Source_Db::getParamName( $nDbType, $nI++ ).
					", ".Source_Db::getParamName( $nDbType, $nI++ ).", ".Source_Db::getParamName( $nDbType, $nI++ ).
					", ".Source_Db::getParamName( $nDbType, $nI++ ).", ".Source_Db::getParamName( $nDbType, $nI++ ).
					", ".Source_Db::getParamName( $nDbType, $nI++ ).", ".Source_Db::getParamName( $nDbType, $nI++ ).
					", ".Source_Db::getParamName( $nDbType, $nI++ ).", ".Source_Db::getParamName( $nDbType, $nI++ )." ) "
			;
		}
		return $this->m_sLogQuery;
	}

	protected function getLogTableName()
	{
		$sTableName = "Log_" . $this->getTableName() . "Log";
		if ( $this->m_szSchema !== null )
			$sTableName = $this->m_szSchema . "." . $sTableName;
		return $sTableName;
	}

	protected function getLogBindVars( $oObjectChange )
	{
		// UserLoginName
		$vtUserId = null;
		$oSession = new Zend_Session_Namespace( APPLICATION_NAME );
		if( $oSession !== null and $oSession->__get("userid") > 0 )
			$vtUserId = $oSession->__get("userid");

		// ReqUri
		$vtReqUri = null;
		if ( array_key_exists( "REQUEST_URI", $_SERVER ) === true )
			$vtReqUri = $_SERVER["REQUEST_URI"];

		// MetaData
		$sPost = serialize( $_POST );
		$sGet = serialize( $_GET );
		if ( strlen( $sPost) + strlen( $sGet ) > 3950 )
		{
			$nPostLength = strlen( $sPost );
			if ( $nPostLength < 3950 )
			{
				$nGetLength = 3950 - $nPostLength;
				$sGet = substr( $sGet, 0, $nGetLength );
			}
			else
			{
				$sGet = null;
				$sPost = substr( $sPost, 0, 3950 );
			}
		}
		$vtMetaData = "POSTSTART";
		$vtMetaData .= $sPost;
		$vtMetaData .= "POSTEND";
		$vtMetaData .= "GETSTART";
		$vtMetaData .= $sGet;
		$vtMetaData .= "GETEND";

		// TableName
		$sTableName = $this->getTableName();
		if ( $this->m_szSchema !== null )
			$sTableName = $this->m_szSchema . "." . $sTableName;

		// MasterId
		$vtMasterId = $oObjectChange->getSystemId();
		if ( $oObjectChange->getActionName() === Source_Db::ACTION_INSERT
			or $oObjectChange->getActionName() === Source_Db::ACTION_DELETE
		)
			$vtMasterId = $oObjectChange->getObject()->getId();

		// OldValue
		$vtOldValue = null;
		if ( $oObjectChange->getActionName() === Source_Db::ACTION_UPDATE )
			$vtOldValue = $oObjectChange->getOldValue();
		else if ( $oObjectChange->getActionName() === Source_Db::ACTION_DELETE )
			$vtOldValue = $oObjectChange->getSystemId();

		// NewValue
		$vtNewValue = null;
		if ( $oObjectChange->getActionName() === Source_Db::ACTION_UPDATE )
			$vtNewValue = $oObjectChange->getNewValue();
		else if ( $oObjectChange->getActionName() === Source_Db::ACTION_INSERT )
			$vtNewValue = $oObjectChange->getSystemId();

		// ObjectProperty
		$vtObjectProperty = null;
		if ( $oObjectChange->getActionName() === Source_Db::ACTION_UPDATE )
			$vtObjectProperty = $oObjectChange->getObjectProperty();

		$arrBindVars = array();
		// UserLoginName
		$arrBindVars[] = $this->toSqlString( $vtUserId, true );
		// ReqUri
		$arrBindVars[] = $this->toSqlString( $vtReqUri, true );
		// MetaData
		$arrBindVars[] = $this->toSqlString( $vtMetaData, true );
		// TableName
		$arrBindVars[] = $this->toSqlString( $sTableName, true );
		// MasterId
		$arrBindVars[] = $this->toSqlString( $vtMasterId, true );
		// RelatedId1
		$arrBindVars[] = $this->toSqlString( $this->getRelatedId1( $oObjectChange ), true );
		// RelatedId2
		$arrBindVars[] = $this->toSqlString( $this->getRelatedId2( $oObjectChange ), true );
		// Action
		$arrBindVars[] = $this->toSqlString( $oObjectChange->getActionName(), true );
		// OldValue
		$arrBindVars[] = $this->toSqlString( $vtOldValue, true );
		// NewValue
		$arrBindVars[] = $this->toSqlString( $vtNewValue, true );
		// LogDateTime
		$arrBindVars[] = $this->toSqlString( Agenda_Factory::createDateTime(), true );
		// ObjectProperty
		$arrBindVars[] = $this->toSqlString( $vtObjectProperty, true );

		return $arrBindVars;
	}

	protected function getRelatedId1( $oObjectChange )
	{
		return null;
	}

	protected function getRelatedId2( $oObjectChange )
	{
		return null;
	}

	protected function isValid( $oObjectChange )
	{
		return true;
	}
}
<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Writer.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 *
 * @package    Auth
 */

/**
 * @package Auth
 */
abstract class RAD_Auth_Db_Writer extends Source_Db_Writer
{
	protected $m_oUser;	// RAD_Auth_User
	protected $m_oRole;	// RAD_Auth_Role

	public function __construct( $oFactory, $oUser, $oRole )
  	{
  		$this->m_oUser = $oUser;
  		$this->m_oRole = $oRole;

  		parent::__construct( $oFactory );
  	}

	/**
	 * @see Source_Db_Writer::add()
	 */
	protected function add( $oObjectChange, $oStmt = null )
	{
		$oObject = $oObjectChange->getObject();

		$sInsertQuery = $this->getInsertQuery( $oObject );

		try
		{
			$this->m_objDatabase->query( $sInsertQuery );
		}
		catch ( Exception $e)
		{
			throw new Exception( $e->getMessage().", For Query: ".$sInsertQuery, E_ERROR );
		}

		return true;
	}

	protected function getInsertQuery( $oObject )
	{
		return
			"INSERT INTO ".$this->getTableName().
			"( UserId, RoleName, ".$this->getObjectColumnName()." ) ".
			"VALUES( ".
				$this->toSqlString( $this->m_oUser ).", ".
				$this->toSqlString( $this->m_oRole ).", ".
				$this->toSqlString( $oObject->getId() ).
			" )";
	}

	protected function update( $objObjectChange )
  	{
  		throw new Exception( "update is not allowed for userroleauth!", E_ERROR );
  	}

	protected function delete( $oObjectChange )
	{
		$oObject = $oObjectChange->getObject();

		$sDeleteQuery = $this->getDeleteQuery( $oObject );

		try
		{
			$this->m_objDatabase->query( $sDeleteQuery );
		}
		catch ( Exception $e)
		{
			throw new Exception( $e->getMessage().", For Query: ".$sDeleteQuery, E_ERROR );
		}

		return true;
	}

	protected function getDeleteQuery( $oObject )
	{
		return
			"DELETE ".
			"FROM	".$this->getTableName()." ".
			"WHERE	UserId = ".$this->toSqlString( $this->m_oUser )." ".
			"AND	RoleName = ".$this->toSqlString( $this->m_oRole )." ".
			"AND	".$this->getObjectColumnName()." = ".$this->toSqlString( $oObject );
	}

	protected abstract function getObjectColumnName();
}
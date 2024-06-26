<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: UserWriter.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package Auth
 */
class RAD_Auth_Role_Db_UserWriter extends Source_Db_Writer
{
	protected $m_oUser;

	public function __construct( $oUser, $objFactory )
	{
		parent::__construct( $objFactory );

		$this->m_oUser = $oUser;
	}

	/**
	 * @see Source_Db_Writer::add()
	 */
	protected function add( $oObjectChange, $oStmt = null )
	{
		$oRole = $oObjectChange->getObject();

		$sInsertQuery =
			"INSERT INTO UsersPerRole".
			"( Rolename, UserId ) ".
			"VALUES( ".$this->toSqlString( $oRole ).", ".$this->toSqlString( $this->m_oUser )." )";

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

	/**
	 * Defined by Source_Db_Writer; gets the persistance
	 *
	 * @see Source_Db_Writer::getPersistance()
	 */
	protected function getPersistance()
	{
		return null;
	}

	/**
	 * @see Source_Db_Writer::getTableName()
	 */
	protected function getTableName()
	{
		return "UsersPerRole";
	}

	/**
	 * @see Source_Db_Writer::delete()
	 */
	protected function delete( $objObjectChange )
	{
		$objRole = $objObjectChange->getObject();

		$sDeleteQuery =
			"DELETE ".
			"FROM 	UsersPerRole ".
			"WHERE 	Rolename = ".$this->toSqlString( $objRole )." ".
			"AND	UserId = ".$this->toSqlString( $this->m_oUser );

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

	/**
	 * Defined by Source_Db_Writer; Writes the objectChange
	 *
	 * @see Source_Db_Writer::update()
	 */
	protected function update( $objObjectChange )
	{
		throw new Exception( "updateUserperRole needs implementing", E_ERROR );
	}
}
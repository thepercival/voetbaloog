<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Writer.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Bet_Db_Writer extends Source_Db_Writer
{
	public function __construct( $oFactory )
	{
		parent::__construct( $oFactory );
	}

	/**
	 * @see Source_Db_Writer::add()
	 */
	protected function add( $oObjectChange, $oStmt = null )
	{
		$oBet = $oObjectChange->getObject();

		$sTableName = $this->getTableName();

		$arrBindVars = array();
		$nDbType = $this->getDbType(); $nI = 0;

		$sInsertQuery = null;
		if ( $oBet instanceof VoetbalOog_Bet_Score )
		{
			$sInsertQuery =
				"INSERT INTO ".$sTableName.
				"( GameId, UsersPerPoolId, HomeGoals, AwayGoals, RoundBetConfigId ) ".
				"VALUES( ".Source_Db::getParamName( $nDbType, $nI++ ).", ".Source_Db::getParamName( $nDbType, $nI++ ).",".Source_Db::getParamName( $nDbType, $nI++ ).",".Source_Db::getParamName( $nDbType, $nI++ ).",".Source_Db::getParamName( $nDbType, $nI++ )." )";

			$arrBindVars[] = $this->toSqlString( $oBet->getGame(), true );
			$arrBindVars[] = $this->toSqlString( $oBet->getPoolUser(), true );
			$arrBindVars[] = $this->toSqlString( $oBet->getHomeGoals(), true );
			$arrBindVars[] = $this->toSqlString( $oBet->getAwayGoals(), true );
			$arrBindVars[] = $this->toSqlString( $oBet->getRoundBetConfig(), true );
		}
		elseif ( $oBet instanceof VoetbalOog_Bet_Result )
		{
			$sInsertQuery =
				"INSERT INTO ".$sTableName.
				"( GameId, UsersPerPoolId, Result, RoundBetConfigId ) ".
				"VALUES( ".Source_Db::getParamName( $nDbType, $nI++ ).", ".Source_Db::getParamName( $nDbType, $nI++ ).",".Source_Db::getParamName( $nDbType, $nI++ ).",".Source_Db::getParamName( $nDbType, $nI++ )." )";

			$arrBindVars[] = $this->toSqlString( $oBet->getGame(), true );
			$arrBindVars[] = $this->toSqlString( $oBet->getPoolUser(), true );
			$arrBindVars[] = $this->toSqlString( $oBet->getResult(), true );
			$arrBindVars[] = $this->toSqlString( $oBet->getRoundBetConfig(), true );
		}
		elseif ( $oBet instanceof VoetbalOog_Bet_Qualify )
		{
			$sInsertQuery =
				"INSERT INTO ".$sTableName.
				"( PoulePlaceId, UsersPerPoolId, TeamId, RoundBetConfigId ) ".
				"VALUES( ".Source_Db::getParamName( $nDbType, $nI++ ).", ".Source_Db::getParamName( $nDbType, $nI++ ).",".Source_Db::getParamName( $nDbType, $nI++ ).",".Source_Db::getParamName( $nDbType, $nI++ )." )";

			$arrBindVars[] = $this->toSqlString( $oBet->getPoulePlace(), true );
			$arrBindVars[] = $this->toSqlString( $oBet->getPoolUser(), true );
			$arrBindVars[] = $this->toSqlString( $oBet->getTeam(), true );
			$arrBindVars[] = $this->toSqlString( $oBet->getRoundBetConfig(), true );
		}

		try
		{
			$stmt = $this->m_objDatabase->prepare( $sInsertQuery );
			$stmt->execute( $arrBindVars );

			$oObjectChange->putSystemId( $this->m_objDatabase->lastInsertId( $sTableName ) );
			$oBet->putId( $oObjectChange->getSystemId() );
		}
		catch ( Exception $e)
		{
			throw new Exception( $e->getMessage().", For Query: ".$sInsertQuery, E_ERROR );
		}

		return true;
	}
}
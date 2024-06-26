<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Source
 */

/**
 * @package    Source
 */
interface Source_Db_Interface
{
	/**
	 * Returns the maybe quoted value
	 *
	 * @param mixed     $vtVariant	 	the to-maybe-quoted value
	 * @param bool 		$bForWriting 	default is false
	 * @param bool		$bPrepared		if prepared
	 * @param int		$nDbType		the db type
	 * @return string|null the db-value
	**/
	public static function toSqlString( $vtVariant, bool $bForWriting = true, bool $bPrepared = false, int $nDbType = null );

	/**
	 * Returns the db-value
	 *
	 * @param Construction_SearchOperator 	$objSearchOperator	the db-value
	 * @param int 							$nType 				the collection-type
	 * @return string 											the db-value
	**/
	public static function searchOperatorToSqlString( $objSearchOperator, $nType );
}
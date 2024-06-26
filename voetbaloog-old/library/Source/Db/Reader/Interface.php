<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4557 2019-08-12 18:50:59Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
interface Source_Db_Reader_Interface
{
	/**
	 * gets the Select
	 *
	 * @param  Construction_Option_Collection	$oOptions	The options
	 * @return Zend_Db_Select The Select
	 */
	public function getQuery( Construction_Option_Collection $oOptions = null ): Zend_Db_Select;
	/**
	 * gets the Select
	 *
	 * @param  Construction_Option_Collection	$oOptions	The options
	 * @return Zend_Db_Select The Select
	 */
	public function getCountQuery( Construction_Option_Collection $oOptions = null ): Zend_Db_Select;
	/**
	 * returns the number of objects
	 *
	 * @param  Construction_Option_Collection	$oOptions	The construction options
	 * @return int	the number of objects
	 */
	public function getNrOfObjects( Construction_Option_Collection $oOptions = null );
	/**
	 * adds a where to selectobject
	 *
	 * @param  Construction_Option_Collection	$oOptions			The filteroptions
	 * @param  array				            $arrBindVars					The bind variables
	 * @param  string				            $szRet							The wherepart
	 * @param  bool					            $bOr							true = Or, false = And
	 * @return bool true if succeeded else false
	 */
	public function toWhereClause( Construction_Option_Collection $oOptions, &$arrBindVars, $szRet = "", $bOr = false );
	/**
	 * adds an orderby to selectobject
	 *
	 * @param  Zend_Db_Select		            $oSelect	The selectobject
	 * @param  Construction_Option_Collection	$objConstructionOptionOrders	The orderoptions
	 * @return bool true if succeeded else false
	 */
	public function addOrderBy( Zend_Db_Select $oSelect, Construction_Option_Collection $objConstructionOptionOrders );
	/**
	 * adds a DbPersistance
	 *
	 * @param Source_Db_Persistance $oPersistance
	 */
	public function addPersistance( Source_Db_Persistance $oPersistance );
}
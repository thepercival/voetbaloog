<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Collection.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Patterns
 */

/**
 * @package    Patterns
 */
class Voetbal_PoulePlace_Collection extends Patterns_Collection
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @see Patterns_Collection_Interface::insert()
	 */
	public function insert( $oItem )
	{
		if ( $oItem === null or ( $oItem instanceof Voetbal_PoulePlace ) === false )
			return false;

		$nRanking = $oItem->getRanking();
		if ( $this[ $nRanking ] === null )
		{
			$arrNew = array_merge( array( $nRanking => $oItem ), $this->getArrayCopy() );
			$this->exchangeArray( $arrNew );
			return true;
		}
		return false;
	}

	/**
	 *
	 * @see Patterns_Collection_Interface::add()
	 */
	public function add( $oItem )
	{
		if ( $oItem === null or ( $oItem instanceof Voetbal_PoulePlace ) === false )
			return false;

		$nRanking = $oItem->getRanking();
		if ( $this[ $nRanking ] === null )
		{
			$this[ $nRanking ] = $oItem;
			return true;
		}
		return false;
	}

	/**
	 *
	 * @see Patterns_Collection_Interface::remove()
	 */
	public function remove( $oItem )
	{
		if ( $oItem === null or ( $oItem instanceof Voetbal_PoulePlace ) === false )
			return false;

		$nRanking = $oItem->getRanking();
		if ( $this[ $nRanking ] !== null )
		{
			unset( $this[ $nRanking ] );
			return true;
		}
		return false;
	}

	/**
	 *
	 * @see Patterns_Collection_Interface::reindexItem()
	 */
	public function reindexItem( $vtOldIndex, $oItem )
	{
		$this[ $oItem->getRanking() ] = $oItem;
		unset( $this[ $vtOldIndex ] );
	}

	/**
	 * @see Patterns_Collection_Interface::equals()
	 */
	public function equals( $oCollection )
	{
		if ( $oCollection === null or ( $oCollection instanceof Patterns_Collection ) === false )
			return false;

		// quick compare
		if ( count( $this ) !== count( $oCollection ) )
			return false;

		// loop through both collections. If they contain identical objects return true
		foreach( $this as $oItem )
		{
			if( $oItem !== $oCollection[ $oItem->getRanking() ] )
				return false;
		}
		return true;
	}

	/**
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet ( $nRanking )
	{
		if ( $nRanking !== null and $this->offsetExists( $nRanking ) === true )
			return parent::offsetGet ( $nRanking );
		return null;
	}
}
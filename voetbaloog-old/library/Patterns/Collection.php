<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Collection.php 4584 2020-04-29 09:31:18Z thepercival $
 *
 * @package    Patterns
 */

/**
 * @package    Patterns
 */
class Patterns_Collection extends ArrayObject implements Patterns_Collection_Interface, JsonSerializable
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
		if ( $oItem === null or ( $oItem instanceof Patterns_Idable_Interface ) === false )
			return false;

		$vtId = $oItem->getId();
		if ( $this[ $vtId ] === null )
		{
			$arrNew = array_merge( array( $vtId => $oItem ), $this->getArrayCopy() );

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
		if ( $oItem === null or ( $oItem instanceof Patterns_Idable_Interface ) === false )
			return false;

		$vtId = $oItem->getId();
		if ( $this[ $vtId ] === null )
		{
			$this[ $vtId ] = $oItem;
			return true;
		}
		return false;
	}

	/**
	 *
	 * @see Patterns_Collection_Interface::addCollection()
	 */
	public function addCollection( $objCollection )
	{
		if ( $objCollection === null )
			return false;

		$bRetVal = true;
		foreach ( $objCollection as $oItem )
		{
			if ( $this->add( $oItem ) === false )
				$bRetVal = false;
		}
		return $bRetVal;
	}

	/**
	 *
	 * @see Patterns_Collection_Interface::remove()
	 */
	public function remove( $oItem )
	{
		if ( $oItem === null or ( $oItem instanceof Patterns_Idable_Interface ) === false )
			return false;

		$vtId = $oItem->getId();
		if ( $this[ $vtId ] !== null )
		{
			unset( $this[ $vtId ] );
			return true;
		}
		return false;
	}

	/**
	 *
	 * @see Patterns_Collection_Interface::remove()
	 */
	public function removeCollection( Patterns_Collection $oCollection )
	{
		$bRetVal = true;
		foreach ( $oCollection as $oItem )
		{
			if ( $this->remove( $oItem ) === false )
				$bRetVal = false;
		}
		return $bRetVal;
	}


	/**
	 *
	 * @see Patterns_Collection_Interface::flush()
	 */
	public function flush()
	{
		$oItem = $this->first();

		while ( $oItem !== null )
		{
			if ( $this->remove( $oItem ) === false )
				return false;
			$oItem = $this->first();
		}

		return true;
	}

    /**
     * @see Patterns_Collection_Interface::first()
     */
    public function first()
    {
        $it = $this->getIterator();
        $it->rewind();
        if ( !$it->valid() ) {
            return null;
        }
        return $it->current();
    }

    /**
     *
     * @see Patterns_Collection_Interface::getIteratorReversed()
     */
    public function getIteratorReversed(): ArrayIterator
    {
        return new ArrayIterator( array_reverse( $this->getArrayCopy() ) );
    }

	/**
	 *
	 * @see Patterns_Collection_Interface::reindexItem()
	 */
	public function reindexItem( $vtOldIndex, $oItem )
	{
		$this[ $oItem->getId() ] = $oItem;
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
			if( $oItem !== $oCollection[ $oItem->getId() ] )
				return false;
		}
		return true;
	}

	/**
	 * @see Patterns_Collection_Interface::toString()
	 */
	public function toString( $sSeperator = ",", $bRemoveLastSeperator = true )
	{
		$sItemsAsString = "";

		foreach ( $this as $vtItemId => $oItem )
			$sItemsAsString .= $vtItemId . $sSeperator;

		if ( $bRemoveLastSeperator === true )
			$sItemsAsString = substr( $sItemsAsString, 0, strlen( $sItemsAsString ) - strlen( $sSeperator ) );

		return $sItemsAsString;
	}

	/**
	 * Overload of a "php-magic-method"
	 *
	 * @return  string	the Id
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet ( $vtId )
	{
		if ( $vtId !== null and $this->offsetExists( $vtId ) === true )
			return parent::offsetGet ( ( $vtId instanceof Patterns_Idable_Interface ) ? $vtId->getId() : $vtId );
		return null;
	}

	/**
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetExists( $p_vtIndex )
	{
		$vtIndex = ( $p_vtIndex instanceof Patterns_Idable_Interface ) ? $p_vtIndex->getId() : $p_vtIndex;
		return parent::offsetExists ( $vtIndex );
	}

	/**
	 * @see JsonSerializable::jsonSerialize()
	 */
	public function jsonSerialize()
	{
		return $this->getArrayCopy();
	}
}
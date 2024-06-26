<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Collection.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    Patterns
 */

/**
 * @package Patterns
 */
class Patterns_ObservableObject_Collection extends Patterns_Collection implements Patterns_ObservableObject_Interface
{
	use Patterns_ObservableObject_Trait;

	public function __construct()
	{
		parent::__construct();
		$this->m_oObservers = Patterns_Factory::createCollection();
	}

	/**
	 * @see Patterns_Collection_Interface::add()
	 */
	function insert( $objItem )
	{
		if ( parent::insert( $objItem ) )
		{
			if ( $this->m_bObserved === true )
			{
				$objObjectChange = MetaData_ObjectChange_Factory::createCollectionChange( Source_Db::ACTION_INSERT, $objItem );
				return $this->notifyObservers( $objObjectChange );
			}
			return true;
		}

		return false;
	}

	/**
	 *
	 * @see Patterns_Collection_Interface::add()
	 */
	function add( $objItem )
	{
		if ( parent::add( $objItem ) )
		{
			if ( $this->m_bObserved === true )
			{
				$objObjectChange = MetaData_ObjectChange_Factory::createCollectionChange( Source_Db::ACTION_INSERT, $objItem );
				return $this->notifyObservers( $objObjectChange );
			}
			return true;
		}

		return false;
	}

	/**
	 *
	 * @see Patterns_Collection_Interface::remove()
	 */
	function remove( $objItem )
	{
		if ( parent::remove( $objItem ) )
		{
			if ( $this->m_bObserved === true )
			{
				$objObjectChange = MetaData_ObjectChange_Factory::createCollectionChange( Source_Db::ACTION_DELETE, $objItem );
				return $this->notifyObservers( $objObjectChange );
			}
			return true;
		}
		return false;
	}
}
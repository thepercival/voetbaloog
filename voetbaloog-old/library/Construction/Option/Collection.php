<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Collection.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Construction
 */

/**
 * @package    Construction
 */
class Construction_Option_Collection extends Patterns_Collection implements Construction_Option_Collection_Interface, Patterns_Idable_Interface
{
	// Patterns_Idable_Interface
	protected $m_vtId;							// string

	public function __construct()
  	{
  		parent::__construct();  		
  	}

  	/**
	 * @see Patterns_Idable_Interface::getId()
	 */
	public function getId()
  	{
  		return $this->m_vtId;
	}

	/**
	 * @see Patterns_Idable_Interface::putId()
	 */
	public function putId( $vtId )
	{
		$this->m_vtId = $vtId;
	}

	/**
	 * @see Construction_Option_Collection_Interface::addReadProperties()
	 */
	public function addReadProperties( /* variable number of arguments */ )
	{
		for ( $nI = 0 ; $nI < func_num_args() ; $nI++ ) {
			$oConstructionOptionReadProperty = Construction_Factory::createReadProperty( func_get_arg( $nI ) );
			$this->add( $oConstructionOptionReadProperty );
		}
		
		return true;
	}
	
  	/**
	 * @see Construction_Option_Collection_Interface::addFilter()
	 */
	public function addFilter( $szObjectProperty, $szSearchOperator, $vtValue )
	{
		$objConstructionOptionFilter = Construction_Factory::createFilterExt( $szObjectProperty, $szSearchOperator, $vtValue );
		return $this->add( $objConstructionOptionFilter );
	}

	/**
	 * @see Construction_Option_Collection_Interface::addOrder()
	 */
	public function addOrder( $szObjectProperty, $bDescending )
	{
		$objConstructionOptionOrder = Construction_Factory::createOrder( $szObjectProperty, $bDescending );
		return $this->add( $objConstructionOptionOrder );
	}
	
	/**
	 * @see Construction_Option_Collection_Interface::addOrder()
	 */
	public function addLimit( $nCount, $nOffSet = null )
	{
		$objConstructionOptionLimit = Construction_Factory::createLimit( $nCount, $nOffSet );
		return $this->add( $objConstructionOptionLimit );
	}
}
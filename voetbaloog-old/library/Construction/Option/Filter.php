<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Filter.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Construction
 */

/**
 * @package    Construction
 */
class Construction_Option_Filter extends Construction_Option implements Construction_Option_Filter_Interface
{
	protected $m_objSearchOperator; 	// Construction_SearchOperator
	protected $m_vtValue; 				//Variant

  	public function __construct()
  	{
  		parent::__construct();
  	}

  	/**
	 * Defined by Construction_Option_Filter_Interface; gets the SearchOperator
	 *
	 * @see Construction_Option_Filter_Interface::getSearchOperator()
	 */
  	public function getSearchOperator()
  	{
  		return  $this->m_objSearchOperator;
  	}

  	/**
	 * Defined by Construction_Option_Filter_Interface; puts the SearchOperator
	 *
	 * @see Construction_Option_Filter_Interface::putSearchOperator()
	 */
	public function putSearchOperator( $objSearchOperator )
	{
		$this->m_objSearchOperator = $objSearchOperator;
	}

	/**
	 * Defined by Construction_Option_Filter_Interface; gets the Value
	 *
	 * @see Construction_Option_Filter_Interface::getValue()
	 */
	public function getValue()
	{
		return $this->m_vtValue;
	}

	/**
	 * Defined by Construction_Option_Filter_Interface; puts the Value
	 *
	 * @see Construction_Option_Filter_Interface::putValue()
	 */
	public function putValue( $vtValue )
	{
		$this->m_vtValue = $vtValue;
	}
}
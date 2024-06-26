<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: SearchOperator.php 4261 2015-12-23 21:07:47Z thepercival $
 *
 * @package    Construction
 */

/**
 * @package    Construction
 */
class Construction_SearchOperator implements Construction_SearchOperator_Interface, Patterns_Idable_Interface
{
	protected $m_szDescription;  		// string
	protected $m_nNumberOfParameters;	// int

	use Patterns_Idable_Trait;

	/**
	 * @see Construction_SearchOperator_Interface::getDescription()
	 */
	public function getDescription()
	{
		return $this->m_szDescription;
	}

	/**
	 * @see Construction_SearchOperator_Interface::putDescription()
	 */
	public function putDescription( $szDescription )
	{
		$this->m_szDescription = $szDescription;
	}

	/**
	 * @see Construction_SearchOperator_Interface::getNumberOfParameters()
	 */
	public function getNumberOfParameters()
	{
		return $this->m_nNumberOfParameters;
	}

	/**
	 * @see Construction_SearchOperator_Interface::putNumberOfParameters()
	 */
	public function putNumberOfParameters( $nNumberOfParameters )
	{
		$this->m_nNumberOfParameters = $nNumberOfParameters;
	}
}
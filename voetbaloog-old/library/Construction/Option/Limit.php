<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Limit.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 *
 * @package    Construction
 */

/**
 *
 *
 * @package    Construction
 */
class Construction_Option_Limit extends Construction_Option implements Construction_Option_Limit_Interface
{
	protected $m_nCount; 		// int
	protected $m_nOffSet; 		// int

  	public function __construct()
  	{
  		parent::__construct();
  	}

  	/**
	 * Defined by Construction_Option_Order_Interface; gets the count
	 *
	 * @see Construction_Option_Order_Interface::getCount()
	 */
	public function getCount()
	{
		return $this->m_nCount;
	}

	/**
	 * Defined by Construction_Option_Order_Interface; puts the count
	 *
	 * @see Construction_Option_Order_Interface::putCount()
	 */
	public function putCount( $nCount )
	{
		$this->m_nCount = $nCount;
	}
	
  	/**
	 * Defined by Construction_Option_Order_Interface; gets the OffSet
	 *
	 * @see Construction_Option_Order_Interface::getOffSet()
	 */
	public function getOffSet()
	{
		return $this->m_nOffSet;
	}

	/**
	 * Defined by Construction_Option_Order_Interface; puts the OffSet
	 *
	 * @see Construction_Option_Order_Interface::putOffSet()
	 */
	public function putOffSet( $nOffSet )
	{
		$this->m_nOffSet = $nOffSet;
	}
}
?>
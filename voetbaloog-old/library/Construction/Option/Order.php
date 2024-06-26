<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Order.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Construction
 */

/**
 * @package    Construction
 */
class Construction_Option_Order extends Construction_Option implements Construction_Option_Order_Interface
{
	protected $m_bDescending; 		//boolean

  	public function __construct()
  	{
  		parent::__construct();

  		$this->m_bDescending = false;
  	}

  	/**
	 * Defined by Construction_Option_Order_Interface; gets the order
	 *
	 * @see Construction_Option_Order_Interface::getDescending()
	 */
	public function getDescending()
	{
		return $this->m_bDescending;
	}

	/**
	 * Defined by Construction_Option_Order_Interface; puts the order
	 *
	 * @see Construction_Option_Order_Interface::putDescending()
	 */
	public function putDescending( $bDescending )
	{
		$this->m_bDescending = $bDescending;
	}
}
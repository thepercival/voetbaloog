<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: MenuItem.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    Auth
 */

/**
 * @package    Auth
 */
class RAD_Auth_MenuItem extends Patterns_Composite implements RAD_Auth_MenuItem_Interface
{
	protected $m_szDescription;
	protected $m_bShow;
	protected $m_szAction;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @see MenuItem_Interface::getDescription()
	 */
	public function getDescription()
	{
		return $this->m_szDescription;
	}

	/**
	 * @see MenuItem_Interface::putDescription()
	 */
	public function putDescription( $szDescription )
	{
		$this->m_szDescription = $szDescription;
	}

	/**
	 * @see MenuItem_Interface::getAction()
	 */
	public function getAction()
	{
		return $this->m_szAction;
	}

	/**
	 * @see MenuItem_Interface::putAction()
	 */
	public function putAction( $szAction )
	{
		$this->m_szAction = $szAction;
	}

	/**
	 * @see MenuItem_Interface::getShow()
	 */
	public function getShow()
	{
		return $this->m_bShow;
	}

	/**
	 * @see MenuItem_Interface::putShow()
	 */
	public function putShow( $bShow )
	{
		$this->m_bShow = $bShow;
	}
}
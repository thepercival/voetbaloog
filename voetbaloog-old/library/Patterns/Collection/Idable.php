<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Idable.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Patterns
 */

/**
 * @package    Patterns
 */
class Patterns_Collection_Idable extends Patterns_Collection implements Patterns_Idable_Interface
{
	// Patterns_Idable_Interface
	protected $m_vtId;			// string | int

	public function __construct() { parent::__construct(); }

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
}
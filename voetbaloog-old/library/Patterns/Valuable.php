<?php

/**
 *
 *
 *
 *
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Valuable.php 4476 2016-10-14 09:29:33Z thepercival $
 *
 *
 * @package    Patterns
 */

/**
 * @package    Patterns
 */
class Patterns_Valuable implements Patterns_Valuable_Interface, Patterns_Idable_Interface, JsonSerializable
{
	protected $m_vtValue;

	use Patterns_Idable_Trait;

  	/**
	 * @see Patterns_Valueable_Interface::getValue()
	 */
  	public function getValue()
  	{
  		return $this->m_vtValue;
	}

	/**
	 * @see Patterns_Valueable_Interface::putValue()
	 */
	public function putValue( $vtValue )
	{
		$this->m_vtValue = $vtValue;
	}

    public function jsonSerialize()
    {
        return $this->m_vtValue;
    }
}
<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Option.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Construction
 */

/**
 * @package    Construction
 */
class Construction_Option implements Construction_Option_Interface, Patterns_Idable_Interface
{
	// Construction_Option_Interface
    /**
     * @var string
     */
	protected $m_sObjectProperty;

	use Patterns_Idable_Trait;

	public function __construct(){}

	/**
	 * @see Construction_Option_Interface::getObjectProperty()
	 */
	public function getObjectProperty()
	{
		return  $this->m_sObjectProperty;
	}

	/**
	 * @see Construction_Option_Interface::putObjectProperty()
	 */
	public function putObjectProperty( $sObjectProperty )
	{
		$this->m_sObjectProperty = $sObjectProperty;
	}
}
<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Object.php 4264 2015-12-23 22:41:38Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
abstract class Source_Db_Object implements Patterns_Idable_Interface
{
	protected $m_szName;		// string

	use Patterns_Idable_Trait;

	public function __construct()
	{

	}

	public function getName()
	{
		return $this->m_szName;
	}

	public function putName( $szName )
	{
		$this->m_szName = $szName;
	}
}
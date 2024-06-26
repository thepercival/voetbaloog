<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 *
 * @package Voetbal
 */
class Voetbal_Competition_Factory extends Object_Factory_Db
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }
}
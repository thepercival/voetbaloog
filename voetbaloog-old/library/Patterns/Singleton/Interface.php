<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    Patterns
 */

/**
 *
 *
 * @package Patterns
 */
interface Patterns_Singleton_Interface
{
	/**
	 * Prevent to clone the instance
	 *
	 * @return null
	 */
    public function __clone();
    /**
	 * Creating an instance of the Singleton, there will only be made one instance
	 *
	 * @return Patterns_Singleton_Interface
	 */
    public static function getInstance();
}
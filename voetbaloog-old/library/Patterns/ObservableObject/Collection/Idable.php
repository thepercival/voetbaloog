<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Idable.php 4261 2015-12-23 21:07:47Z thepercival $
 *
 * @package    Patterns
 */

/**
 * @package Patterns
 */
class Patterns_ObservableObject_Collection_Idable 	extends Patterns_ObservableObject_Collection
											implements Patterns_Idable_Interface
{
	use Patterns_Idable_Trait;
}
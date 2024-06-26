<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Factory.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    Message
 */


/**
 *
 * @package Message
 */
class Message_Factory extends Object_Factory_Db
{
	protected static $m_objSingleton;

    /**
	 * Call parent
	 */
    protected function __construct(){ parent::__construct(); }
}
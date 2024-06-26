<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Interface.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package	   MetaData
 */

/**
 * @package MetaData
 */
interface MetaData_ObjectChange_Factory_Interface
{
	/**
	 * Creates a new instance of ObjectChange
	 *
	 * @param string			$szSystemId			The SystemId
	 * @param string			$szObjectProperty	The objectproperty
	 * @param mixed	    		$vtOldValue			The old value
	 * @param mixed 			$vtNewValue			The new value
	 * @return MetaData_ObjectChange	A new instance of ObjectChange
	 */
	public static function createObjectChange( $szSystemId, $szObjectProperty, $vtOldValue, $vtNewValue );
	/**
	 * Creates a new instance of ObjectChange
	 *
	 * @param int				        $nAction	The Action
	 * @param Patterns_Idable_Interface	$objObject	The object
	 * @return MetaData_ObjectChange	A new instance of CollectionChange
	 */
	public static function createCollectionChange( $nAction, $objObject );
}
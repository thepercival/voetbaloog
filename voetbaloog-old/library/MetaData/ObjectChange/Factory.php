<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Factory.php 4157 2015-05-06 12:17:47Z thepercival $
 *
 * @package    MetaData
 */

/**
 * @package    MetaData
 */
class MetaData_ObjectChange_Factory extends Object_Factory_Db implements MetaData_ObjectChange_Factory_Interface
{
	protected static $m_objSingleton;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

	/**
	 * @see MetaData_ObjectChange_Factory_Interface::createObjectChange()
	 */
	public static function createObjectChange( $szSystemId, $szObjectProperty, $vtOldValue, $vtNewValue )
	{
		$objObjectChange = new MetaData_ObjectChange();
		$objObjectChange->putId( $szObjectProperty . "-Update-" . $szSystemId );
		$objObjectChange->putObjectProperty( $szObjectProperty );

		$objObjectChange->putEntityName( MetaData_Factory::getClassName( $szObjectProperty ) );

		$objObjectChange->putActionName( Source_Db::ACTION_UPDATE );
		$objObjectChange->putSystemId( $szSystemId );
		$objObjectChange->putOldValue( $vtOldValue );
		$objObjectChange->putNewValue( $vtNewValue );

		return $objObjectChange;
	}

	/**
	 * @see MetaData_ObjectChange_Factory_Interface::createCollectionChange()
	 */
	public static function createCollectionChange( $nAction, $objObject )
	{
		$szEntityName = get_class( $objObject );
		$objObjectChange = new MetaData_ObjectChange();
		$szId = $szEntityName;
		if ( $objObject instanceof Patterns_Idable_Interface )
			$szId = $objObject->getId();
		$objObjectChange->putId( $szEntityName."-".$nAction."-".$szId );
		$objObjectChange->putEntityName( $szEntityName );
		$objObjectChange->putActionName( $nAction );
		$objObjectChange->putSystemId( $szId );
		$objObjectChange->putObject( $objObject );
		return $objObjectChange;
	}

	/**
	 * @see Object_Factory_Interface::createObjects()
	 */
	public static function createObjects()
	{
		return Patterns_Factory::createCollection();
	}

	/**
	 * @see Object_Factory_Db_Interface::createDbWriter()
	 */
	public static function createDbWriter()
	{
		throw new Exception( "IS NOT YET IMPLEMENTED", E_ERROR );
	}
}
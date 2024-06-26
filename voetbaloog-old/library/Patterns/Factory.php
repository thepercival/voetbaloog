<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Factory.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    Patterns
 */

/**
 * @package    Patterns
 */
class Patterns_Factory implements Patterns_Factory_Interface, Patterns_Singleton_Interface
{
	private static $m_objSingleton;

	protected function __construct() {}

	/**
	 * Defined by Patterns_Singleton_Interface; Prevent users to clone the instance
	 *
	 * @see Patterns_Singleton_Interface::__clone()
	 */
	public function __clone()
	{
		trigger_error("Cloning is not allowed.", E_USER_ERROR);
	}

	/**
	 * @see Patterns_Singleton_Interface::getInstance()
	 */
	public static function getInstance()
	{
		if ( self::$m_objSingleton === null )
		{
			$MySelf = __CLASS__;
			self::$m_objSingleton = new $MySelf();
		}
		return self::$m_objSingleton;
	}

	/**
	 * @see Patterns_Factory_Interface::createIdable()
	 */
	public static function createIdable( $vtId )
	{
		$objIdable = new Patterns_Idable_Object();
		$objIdable->putId( $vtId );
		return $objIdable;
	}

	/**
	 * @see Patterns_Factory_Interface::createValuable()
	 * @return Patterns_Valuable
	 */
	public static function createValuable( $vtId, $vtValue )
	{
		$objValuable = new Patterns_Valuable();
		$objValuable->putId( $vtId );
		$objValuable->putValue( $vtValue );
		return $objValuable;
	}

	/**
	 * @see Patterns_Factory_Interface::createCollection()
	 * @return Patterns_Collection
	 */
	public static function createCollection()
	{
		return new Patterns_Collection();
	}

	/**
	 * @see Patterns_Factory_Interface::createIdableCollection()
	 * @return Patterns_Collection_Idable
	 */
	public static function createIdableCollection( Patterns_Idable_Interface $oIdable )
	{
		$oIdableCollection = new Patterns_Collection_Idable();
		$oIdableCollection->putId( $oIdable->getId() );
		return $oIdableCollection;
	}

	/**
	 * @see Patterns_Factory_Interface::createObservableCollection()
	 * @return Patterns_ObservableObject_Collection
	 */
	public static function createObservableCollection()
	{
		return new Patterns_ObservableObject_Collection();
	}

	/**
	 * @see Patterns_Factory_Interface::createCollectionExt()
	 * @return Patterns_Collection
	 */
	public static function createCollectionExt( /* variable param list */ )
	{
		$objCollection = Patterns_Factory::createCollection();
		for ( $nI = 0 ; $nI < func_num_args() ; $nI++ )
		{
			$objIdable = self::createIdable( func_get_arg( $nI ) );
			$objCollection->add( $objIdable );
		}
		return $objCollection;
	}

	/**
	 * @see Patterns_Factory_Interface::createValuables()
	 * @return Patterns_Collection
	 */
	public static function createValuables( /* variable param list */ )
	{
		$oCollection = self::createCollection();
		for ( $nI = 0 ; $nI < func_num_args() ; $nI++, $nI++ )
		{
			$oCollection->add( self::createValuable( func_get_arg( $nI ), func_get_arg( $nI + 1 ) ) );
		}
		return $oCollection;
	}

	/**
	 * @see Patterns_Factory_Interface::createNumbers()
	 * @return Patterns_Collection
	 */
	public static function createNumbers( $nStart, $nEnd, $nModulus = 1 )
	{
		$objNumberCollection = self::createCollection();

		$nStart = (int) $nStart;
		$nIterations = abs( (int) ( $nEnd - $nStart ) );
		$nI = 0;
		while ( $nI <= $nIterations )
		{
			if ( $nStart % $nModulus === 0 )
			{
				$objIdable = Patterns_Factory::createIdable( $nStart );
				$objNumberCollection->add( $objIdable );
			}
			$nStart++;
			$nI++;
		}

		return $objNumberCollection;
	}

	/**
	 * @see Patterns_Factory_Interface::createLetters()
	 * @return Patterns_Collection
	 */
	public static function createLetters( $szStart, $szEnd )
	{
		$nStart = ord ( $szStart );
		$nEnd = ord ( $szEnd );

		$objLetterCollection = self::createCollection();

		$nIterations = abs( (int) ( $nEnd - $nStart ) );
		$nI = 0;
		while ( $nI <= $nIterations )
		{
			$objIdable = Patterns_Factory::createIdable( chr( $nStart ) );
			$objLetterCollection->add( $objIdable );

			$nStart++;
			$nI++;
		}

		return $objLetterCollection;
	}

	/**
	 * @see Patterns_Factory_Interface::createIndex()
	 * @return Patterns_Collection
	 */
	public static function createIndex( $p_oCollection, array $arrObjectProperties )
	{
		$objCollection = self::createCollection();
		foreach ( $p_oCollection as $oObject )
		{
			$sId = "";
			foreach( $arrObjectProperties as $sObjectProperty )
				$sId .= MetaData_Factory::getValue( $oObject, $sObjectProperty );

			$objCollection->add( self::createValuable( $sId, $oObject ) );
		}
		return $objCollection;
	}
}

<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Db.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Object
 */

/**
 * @package Object
 */
abstract class Object_Factory_Db extends Object_Factory implements Object_Factory_Db_Interface
{
	protected $m_objPersistance;
	protected $m_objReader;
	protected $m_oWriter;

	/**
	 * Call parent
	 */
    protected function __construct(){ parent::__construct(); }

	/**
	 * @see Object_Factory_Interface::createObjects()
	 */
	public static function createObjects()
	{
		return Patterns_Factory::createObservableCollection();
	}

	/**
	 * @see Object_Factory_Db_Interface::createArrayFromDatabase()
	 * @return Patterns_ObservableObject_Collection
	 */
	public static function createArrayFromDatabase( $oOptions = null, $bLowerCase = false )
	{
		return static::createDbReader()->createArray( $oOptions, $bLowerCase );
	}

	/**
	 * @see Object_Factory_Db_Interface::createObjectFromDatabase()
	 */
	public static function createObjectFromDatabase( $vtOptions = null )
	{
		if ( $vtOptions !== null and ( $vtOptions instanceof Construction_Option_Collection ) === false )
		{
			$vtId = null;
			if ( $vtOptions instanceof Patterns_Idable_Interface )
				$vtId = $vtOptions->getId();
			else
				$vtId = $vtOptions;

			if ( is_int( $vtId ) and $vtId === 0 )
				return null;

			$objPool = static::getPool();
			if ( $objPool[ $vtId ] !== null )
				return $objPool[ $vtId ];

			$vtOptions = Construction_Factory::createOptions();
			$vtOptions->addFilter( static::getInstance()->getIdProperty(), "EqualTo", $vtId );
		}

		$objCollection = static::createObjectsFromDatabase( $vtOptions );
		if ( $objCollection !== null and $objCollection->count() === 1 )
			return $objCollection->first();
		return null;
	}

	/**
	 * @see Object_Factory_Db_Interface::createObjectsFromDatabase()
	 */
	public static function createObjectsFromDatabase( $objOptions = null )
	{
		return static::createDbReader()->createObjects( $objOptions );
	}

	/**
	 * @see Object_Factory_Db_Interface::removeObjectsFromDatabase()
	 */
	public static function removeObjectsFromDatabase( $vtOptions = null )
	{
		$objDbWriter = static::createDbWriter();
		$objDbReader = static::createDbReader();
		$arrBindVars = array();
		$szWhereClause = $vtOptions ? $objDbReader->toWhereClause( $vtOptions, $arrBindVars ) : "";
		return $objDbWriter->removeObjects( $szWhereClause, $arrBindVars );
	}

	/**
	 * @see Object_Factory_Db_Interface::getNrOfObjectsFromDatabase()
	 */
	public static function getNrOfObjectsFromDatabase( $oOptions = null )
	{
		return static::createDbReader()->getNrOfObjects( $oOptions );
	}

	/**
	 * @see Object_Factory_Db_Interface::createDbPersistance()
	 */
	public static function createDbPersistance()
	{
		return static::getInstance()->createDbPersistanceHelper();
	}

	protected function createDbPersistanceHelper()
	{
		if ( $this->m_objPersistance === null )
		{
			$szClassName = $this->getClassName()."_Db_Persistance";
			$this->m_objPersistance = new $szClassName();
		}
		return $this->m_objPersistance;
	}

	/**
	 * @see Object_Factory_Db_Interface::createDbReader()
	 */
	public static function createDbReader()
	{
		return static::getInstance()->createDbReaderHelper();
	}

	protected function createDbReaderHelper()
	{
		if ( $this->m_objReader === null )
		{
			$szClassName = $this->getClassName()."_Db_Reader";
			if ( self::classExists( $szClassName ) )
				$this->m_objReader = new $szClassName( $this );
			else
				$this->m_objReader = new Source_Db_Reader( $this );
		}
		return $this->m_objReader;
	}

	/**
	 * @see Object_Factory_Db_Interface::resetDbReader()
	 */
	public function resetDbReader()
	{
		$this->m_objReader = null;
	}

	private function classExists( $szClassName )
	{
		$nCurrent = error_reporting();
		error_reporting( $nCurrent & ~E_WARNING ); // suppress warnings
		$bRetVal = class_exists( $szClassName );
		error_reporting( $nCurrent ); // undo suppress
		return $bRetVal;
	}

	/**
	 * @see Object_Factory_Db_Interface::createDbWriter()
	 */
	public static function createDbWriter()
	{
		return static::getInstance()->createDbWriterHelper();
	}

	protected function createDbWriterHelper()
	{
		if ( $this->m_oWriter === null )
		{
			$szClassName = $this->getClassName()."_Db_Writer";
			if ( self::classExists( $szClassName ) )
				$this->m_oWriter = new $szClassName( $this );
			else
				$this->m_oWriter = new Source_Db_Writer( $this );
		}
		return $this->m_oWriter;
	}

	/**
	 * @see Object_Factory_Db_Interface::getIdProperty()
	 */
	public function getIdProperty()
	{
		return $this->getClassName()."::Id";
	}
}

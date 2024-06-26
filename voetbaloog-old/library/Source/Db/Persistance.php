<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Persistance.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
abstract class Source_Db_Persistance extends Patterns_Collection_Idable
{
    /**
     * @var Source_Db_Object_Table|string
     */
    protected static $m_oTable;

	public function __construct()
	{
        parent::__construct();
		$this->putId( get_called_class() );
		$this->setObjectProperties();
	}

	public static function getTableName(): string
	{
		return static::getTable()->getName();
	}

	public static function getTable(): Source_Db_Object_Table
	{
		if ( is_string( static::$m_oTable ) )
			static::$m_oTable = Source_Db_Object_Factory::createTable( static::$m_oTable );
		return static::$m_oTable;
	}

	/**
	 *
	 */
	protected function setObjectProperties()
	{
		// lees de classname uit maak per property een mapping aan.
	}
}
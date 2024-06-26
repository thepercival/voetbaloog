<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 984 2015-01-16 12:26:46Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Team_Factory extends Object_Factory_Db_JSON implements Voetbal_Team_Factory_Interface, Object_Factory_Db_Ext_Nr_Interface
{
	protected static $m_objSingleton;
	protected static $m_oLines;

	/**
	 * Call parent
	 */
	protected function __construct(){ parent::__construct(); }

    /**
     * @param Voetbal_Extern_Team $oExternTeam
     * @return Voetbal_Team|null
     */
    public static function createObjectFromDatabaseByExtern( Voetbal_Extern_Team $oExternTeam  ): ?Voetbal_Team
    {
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter("Voetbal_Team::ExternId", "EqualTo", Import_Factory::$m_szExternPrefix . $oExternTeam->getId() );
        return static::createDbReader()->createObjects( $oOptions )->first();
    }

    /**
     * @see Object_Factory_Db_Ext_Nr_Interface::getNrOfObjectsFromDatabaseExt()
     */
    public static function getNrOfObjectsFromDatabaseExt( $oObject, Construction_Option_Collection $oOptions = null, string $sClassName = null ): int
    {
        return static::createDbReader()->getNrOfObjectsExt( $oObject, $oOptions, $sClassName );
    }

	/**
	 * @see Voetbal_Team_Factory_Interface::createLine()
	 */
	public static function createLine( $nLine )
	{
	    return static::getAvailableLines()[$nLine];
	}

	/**
	 * @see Voetbal_Team_Factory_Interface::getAvailableLines()
	 */
	public static function getAvailableLines()
	{
		if ( static::$m_oLines === null )
		{
			$oClass = new ReflectionClass("Voetbal_Team_Line");
			$arrConstants = $oClass->getConstants();
			static::$m_oLines = Patterns_Factory::createCollection();
			foreach( $arrConstants as $nLine )
			{
				$oLine = new Voetbal_Team_Line();
				$oLine->putId( $nLine );
				static::$m_oLines->add( $oLine );
			}
		}
		return static::$m_oLines;
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON()
	 */
	public static function convertObjectToJSON( $oObject, $nDataFlag = null )
	{
		if ( $oObject === null )
			return "null";

		if ( static::isInPoolJSON( $oObject ) )
			return $oObject->getId();
		static::addToPoolJSON( $oObject );

		return
		"{".
			"\"Id\":".$oObject->getId().",".
			"\"Name\":\"".$oObject->getName()."\",".
			"\"Abbreviation\":\"".$oObject->getAbbreviation()."\",".
			"\"ImageName\":\"".$oObject->getImageName()."\"".
		"}";
	}

	/**
	 * @see JSON_Factory_Interface::convertObjectToJSON2()
	 */
	public static function convertObjectToJSON2( $oObject, $nDataFlag = null )
	{
		if ( $oObject === null )
			return null;

		if ( static::isInPoolJSON( $oObject ) ) {
			// return $oObject->getId();
			return array(
				"cacheid" => $oObject->getId(),
				"class" => "team"
			);

		}
		static::addToPoolJSON( $oObject );

		return array(
			"class" => "team",
			"id" => $oObject->getId(),
			"name" => $oObject->getName(),
			"abbreviation" => $oObject->getAbbreviation(),
			"imagename" => $oObject->getImageName()
		);
	}
}
<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 740 2014-02-25 19:08:21Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Location_Factory extends Object_Factory_Db_JSON
{
    protected static $m_objSingleton;

    /**
     * Call parent
     */
    protected function __construct(){ parent::__construct(); }

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
            "\"Name\":\"".$oObject->getName()."\"".
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
                "class" => "location"
            );

        }
        static::addToPoolJSON( $oObject );

        $arrJSON = array(
            "class" => "location",
            "id" => $oObject->getId(),
            "name" => $oObject->getName(),
            "competitionseason" => Voetbal_CompetitionSeason_Factory::convertObjectToJSON2( $oObject->getCompetitionSeason(), $nDataFlag ),
        );

        return $arrJSON;
    }
}
<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Factory.php 772 2014-03-04 20:03:28Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_QualifyRule_PoulePlace_Factory extends Object_Factory_Db_JSON
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
            "\"FromPoulePlace\":".Voetbal_PoulePlace_Factory::convertObjectToJSON( $oObject->getFromPoulePlace()).",".
            "\"ToPoulePlace\":".Voetbal_PoulePlace_Factory::convertObjectToJSON( $oObject->getToPoulePlace()).",".
            "\"QualifyRule\":".Voetbal_QualifyRule_Factory::convertObjectToJSON( $oObject->getQualifyRule(), $nDataFlag ).
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
                "class" => "quilifyrulepp"
            );
        }
        static::addToPoolJSON( $oObject );

        return array(
            "class" => "quilifyrulepp",
            "id" => $oObject->getId(),
            "frompouleplace" => Voetbal_PoulePlace_Factory::convertObjectToJSON2( $oObject->getFromPoulePlace(), $nDataFlag ),
            "topouleplace" => Voetbal_PoulePlace_Factory::convertObjectToJSON2( $oObject->getToPoulePlace(), $nDataFlag ),
            "qualifyrule" => Voetbal_QualifyRule_Factory::convertObjectToJSON2( $oObject->getQualifyRule(), $nDataFlag )
        );
    }
}
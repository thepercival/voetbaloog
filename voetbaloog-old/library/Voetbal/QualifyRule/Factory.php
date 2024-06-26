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
class Voetbal_QualifyRule_Factory extends Object_Factory_Db_JSON implements Voetbal_QualifyRule_Factory_Interface
{
    protected static $m_objSingleton;

    /**
     * Call parent
     */
    protected function __construct(){ parent::__construct(); }

    /**
     * @see JSON_Factory_Interface::getConfig()
     */
    public static function getConfig( $nNrOfFromPoulePlaces, $nNrOfToPoulePlaces, $nConfigNr = 1 )
    {
        if ( $nNrOfFromPoulePlaces === 6 and $nNrOfToPoulePlaces === 4 and $nConfigNr === 1 )
        {
            // display = mogelijke pouleplekken per pouleplek volgende ronde
            return array( // CDE(4+8+16), ACD(1+4+8), ABF(1+2+32), BEF(2+16+32)
                "display" => array( 28, 13, 35, 50 ), // distinct pouleplaces
                15 => array( 4, 8, 1, 2 ),      // CDAB
                23 => array( 4, 1, 2, 16 ),     // CABE
                27 => array( 8, 1, 2, 16 ),     // DABE
                29 => array( 4, 8, 1, 16 ),     // CDAE
                30 => array( 4, 8, 2, 16 ),     // CDBE
                39 => array( 4, 1, 2, 32 ),     // CABF
                43 => array( 8, 1, 2, 32 ),     // DABF
                45 => array( 4, 8, 1, 32 ),     // CDAF
                46 => array( 4, 8, 2, 32 ),     // CDBF
                51 => array( 16, 1, 2, 32 ),    // EABF
                53 => array( 4, 1, 32, 16 ),    // CAFE
                54 => array( 16, 4, 2, 32 ),    // ECBF
                57 => array( 8, 1, 32, 16 ),    // DAFE
                58 => array( 16, 8, 2, 32 ),    // EDBF
                60 => array( 4, 8, 32, 16 )     // CDFE
            );
        } else if ( $nNrOfFromPoulePlaces === 6 and $nNrOfToPoulePlaces === 4 and $nConfigNr === 2 )
        {
            // display = mogelijke pouleplekken per pouleplek volgende ronde
            return array( // ABCD(1+2+4+8), ABC(1+2+4), DEF(8+16+32), ADEF(1+8+16+32)
                "display" => array( 15, 7, 56, 57 ), // distinct pouleplaces
                15 => array( 2, 4, 8, 1 ),      // BCDA
                23 => array( 2, 4, 16, 1 ),     // BCEA
                27 => array( 1, 2, 16, 8 ),     // ABED
                29 => array( 4, 1, 8, 16 ),     // CADE
                30 => array( 2, 4, 8, 16 ),     // BCDE
                39 => array( 2, 4, 32, 1 ),     // BCFA
                43 => array( 1, 2, 32, 8 ),     // ABFD
                45 => array( 4, 1, 8, 32 ),     // CADF
                46 => array( 4, 2, 8, 32 ),     // CBDF
                51 => array( 2, 1, 32, 16 ),    // BAFE
                53 => array( 4, 1, 32, 16 ),    // CAFE
                54 => array( 4, 2, 16, 32 ),    // CBEF
                57 => array( 8, 1, 32, 16 ),    // DAFE
                58 => array( 8, 2, 16, 32 ),    // DBEF
                60 => array( 8, 4, 16, 32 )     // DCEF
            );
        }

        $arrConfig = array( "display" => array() );
        {
            $arrFromPoulePlaces = array();
            for ( $nI = 0; $nI < $nNrOfFromPoulePlaces; $nI ++ ) {
                $arrFromPoulePlaces[] = $nI;
            }

            for( $nI = 0 ; $nI < $nNrOfToPoulePlaces ; $nI++ ) {  $arrConfig["display"][$nI] = 0; }

            // var_dump( $nNrOfFromPoulePlaces );
            // if ( count( $arrFromPoulePlaces) > 1 ) {
            //    var_dump( $arrFromPoulePlaces );
              //   var_dump( $nNrOfToPoulePlaces );
                // die();
            // }

            $arrCombinations = new CDKCombinations( $arrFromPoulePlaces, $nNrOfToPoulePlaces );
            // if ( count( $arrFromPoulePlaces) > 1 ) {
               //  var_dump( $arrCombinations );
                // die();
            // }
            foreach ( $arrCombinations as $arrCombination ) {
                $nIndex = 0;
                array_walk( $arrCombination, function ( &$item, $key) use ( &$arrConfig, &$nIndex ) {
                        $item = pow( 2, $item );
                        $nIndex += $item;
                        $arrConfig["display"][$key] += ( ( $item &  $arrConfig["display"][$key] ) < $item ) ? $item : 0;
                    }
                );
                $arrConfig[ $nIndex ] = $arrCombination;

            }
        }
        return $arrConfig;
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
            "\"FromPoulePlaces\" : ". Voetbal_PoulePlace_Factory::convertObjectsToJSON( $oObject->getFromPoulePlaces(), $nDataFlag ).",".
            "\"ToPoulePlaces\" : ". Voetbal_PoulePlace_Factory::convertObjectsToJSON( $oObject->getToPoulePlaces(), $nDataFlag ).",".
            "\"ConfigNr\":".$oObject->getConfigNr().",".
            "\"Config\":".json_encode( $oObject->getConfig() ).
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
                "class" => "quilifyrule"
            );

        }
        static::addToPoolJSON( $oObject );

        return array(
            "class" => "quilifyrule",
            "id" => $oObject->getId(),
            "frompouleplaces" => Voetbal_PoulePlace_Factory::convertObjectsToJSON2( $oObject->getFromPoulePlaces(), $nDataFlag ),
            "topouleplaces" => Voetbal_PoulePlace_Factory::convertObjectsToJSON2( $oObject->getToPoulePlaces(), $nDataFlag ),
            "confignr" => $oObject->getConfigNr(),
            "config" => $oObject->getConfig()
        );
    }
}

class CDKCombinations implements Iterator
{
    protected $c = null;
    protected $s = null;
    protected $n = 0;
    protected $k = 0;
    protected $pos = 0;

    function __construct($s, $k) {
        if(is_array($s)) {
            $this->s = array_values($s);
            $this->n = count($this->s);
        } else {
            $this->s = (string) $s;
            $this->n = strlen($this->s);
        }
        $this->k = $k;
        $this->rewind();
    }
    function key() {
        return $this->pos;
    }
    function current() {
        $r = array();
        for($i = 0; $i < $this->k; $i++)
            $r[] = $this->s[$this->c[$i]];
        return is_array($this->s) ? $r : implode('', $r);
    }
    function next() {
        if($this->_next())
            $this->pos++;
        else
            $this->pos = -1;
    }
    function rewind() {
        $this->c = range(0, $this->k);
        $this->pos = 0;
    }
    function valid() {
        return $this->pos >= 0;
    }

    protected function _next() {
        $i = $this->k - 1;
        while ($i >= 0 && $this->c[$i] == $this->n - $this->k + $i)
            $i--;
        if($i < 0)
            return false;
        $this->c[$i]++;
        while($i++ < $this->k - 1)
            $this->c[$i] = $this->c[$i - 1] + 1;
        return true;
    }
}
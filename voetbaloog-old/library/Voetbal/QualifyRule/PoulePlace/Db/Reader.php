<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Reader.php 627 2013-12-15 20:18:35Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_QualifyRule_PoulePlace_Db_Reader extends Source_Db_Reader
{
    public function __construct( $oFactory )
    {
        parent::__construct( $oFactory );

        $this->addPersistance( Voetbal_QualifyRule_Factory::createDbPersistance() );
    }

    /**
     * @see Source_Db_Reader::getSelectFrom()
     */
    protected function getSelectFrom( $bCount = false )
    {
        $oSelect = parent::getSelectFrom( $bCount );

        $sTableQualifyRules = Voetbal_QualifyRule_Db_Persistance::getTable()->getName();

        $oSelect
            ->join(array( $sTableQualifyRules ), $this->getTableName() . ".QualifyRuleId = ".$sTableQualifyRules.".Id", array() )
        ;

        return $oSelect;
    }
}
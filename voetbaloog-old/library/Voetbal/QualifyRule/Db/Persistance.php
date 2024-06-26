<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
final class Voetbal_QualifyRule_Db_Persistance extends Source_Db_Persistance
{
    protected static $m_oTable = "QualifyRules";

    public function __construct()
    {
        parent::__construct();
    }

    protected function setObjectProperties()
    {
        $oTable = static::getTable();

        $this["Voetbal_QualifyRule::Id"] = $oTable->createColumn( "Id" );
        $this["Voetbal_QualifyRule::FromRound"] = $oTable->createColumn( "FromRoundId" );
        $this["Voetbal_QualifyRule::ToRound"] = $oTable->createColumn( "ToRoundId" );
        $this["Voetbal_QualifyRule::ConfigNr"] = $oTable->createColumn( "ConfigNr" );
    }
}
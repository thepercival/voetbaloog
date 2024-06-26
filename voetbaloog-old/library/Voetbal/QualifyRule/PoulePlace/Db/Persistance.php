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
final class Voetbal_QualifyRule_PoulePlace_Db_Persistance extends Source_Db_Persistance
{
    protected static $m_oTable = "PoulePlaceQualifyRules";

    public function __construct()
    {
        parent::__construct();
    }

    protected function setObjectProperties()
    {
        $oTable = static::getTable();

        $this["Voetbal_QualifyRule_PoulePlace::Id"] = $oTable->createColumn( "Id" );
        $this["Voetbal_QualifyRule_PoulePlace::FromPoulePlace"] = $oTable->createColumn( "FromPoulePlaceId" );
        $this["Voetbal_QualifyRule_PoulePlace::ToPoulePlace"] = $oTable->createColumn( "ToPoulePlaceId" );
        $this["Voetbal_QualifyRule_PoulePlace::QualifyRule"] = $oTable->createColumn( "QualifyRuleId" );
    }
}
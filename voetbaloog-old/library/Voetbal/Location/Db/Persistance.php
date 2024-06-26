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
final class Voetbal_Location_Db_Persistance extends Source_Db_Persistance
{
    protected static $m_oTable = "Locations";

    public function __construct()
    {
        parent::__construct();
    }

    protected function setObjectProperties()
    {
        $oTable = static::getTable();

        $this["Voetbal_Location::Id"] = $oTable->createColumn( "Id" );
        $this["Voetbal_Location::Name"] = $oTable->createColumn( "Name" );
        $this["Voetbal_Location::CompetitionSeason"] = $oTable->createColumn( "CompetitionsPerSeasonId" );
    }
}
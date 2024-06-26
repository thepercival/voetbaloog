<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Persistance.php 929 2014-08-31 18:12:20Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
final class Voetbal_Formation_Db_Persistance extends Source_Db_Persistance
{
    protected static $m_oTable = "Formations";

    public function __construct()
    {
        parent::__construct();
    }

    protected function setObjectProperties()
    {
        $oTable = static::getTable();

        $this["Voetbal_Formation::Id"] = $oTable->createColumn( "Id" );
    }
}
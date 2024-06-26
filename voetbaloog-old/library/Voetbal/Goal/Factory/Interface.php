<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 871 2014-06-30 16:07:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Goal_Factory_Interface
{
    /**
     * create gamefilters
     *
    * @param 	Voetbal_Team		$oTeam  	    the team
     * @param 	Voetbal_Team		$oTeamVersus	the opponent
     * @return 	Patterns_Collection     the goals
     */
    public static function createHomeAwayFilters( $oTeam, $oTeamVersus );
}
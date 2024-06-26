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
interface Voetbal_PoulePlace_Factory_Interface
{
    /**
     * create game
     *
     * @param 	Voetbal_CompetitionSeason	$oCompetitionSeason		the competitionseason
     * @param 	string					    $sTeamExternId		    the external id
     * @return 	Voetbal_PoulePlace	        the pouleplace
     */
    public static function createObjectByExternTeamId( $oCompetitionSeason, $sTeamExternId );
    /**
     * create ranked collection
     *
     * @return 	Voetbal_PoulePlace_Collection	        the ranked collection of pouleplaces
     */
    public static function createObjectsRanked();
}
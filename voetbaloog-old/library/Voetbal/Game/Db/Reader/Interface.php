<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 907 2014-08-21 11:11:20Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_Game_Db_Reader_Interface
{
    /**
     * @param Voetbal_Poule $oPoule
     * @param int $nStates
     * @param Agenda_DateTime|null $oStartDateTime
     * @param Agenda_DateTime|null $oEndDateTime
     * @return RAD_Range|null
     */
    public function getNumberRange( Voetbal_Poule $oPoule, int $nStates = null, Agenda_DateTime $oStartDateTime = null, Agenda_DateTime $oEndDateTime = null ): ?RAD_Range;
    /**
     * gets the state of the gamerounds within a poule
     *
     * @param 	Voetbal_Poule	$oPoule			the poule
     * @return 	array	the states
     */
    public function getStateGameRounds( Voetbal_Poule $oPoule );
}
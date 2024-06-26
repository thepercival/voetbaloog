<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 580 2013-11-20 15:28:51Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Voetbal_QualifyRule_Interface
{
    /**
     * gets the FromRound
     *
     * @return 	Voetbal_Round_Interface	the FromRound
     */
    public function getFromRound();
    /**
     * puts the FromRound
     *
     * @param 	Voetbal_Round_Interface $oFromRound the FromRound which will be set
     * @return 	null
     */
    public function putFromRound( $oFromRound );
    /**
     * gets the ToRound
     *
     * @return 	Voetbal_Round_Interface	the ToRound
     */
    public function getToRound();
    /**
     * puts the ToRound
     *
     * @param 	Voetbal_Round_Interface $oToRound the ToRound which will be set
     * @return 	null
     */
    public function putToRound( $oToRound );
    /**
     * gets the ConfigNr
     *
     * @return 	int	the ConfigNr
     */
    public function getConfigNr();
    /**
     * puts the ConfigNr
     *
     * @param int $nConfigNr the ConfigNr which will be set
     * @return 	null
     */
    public function putConfigNr( $nConfigNr );
    /**
     * gets the pouleplacerules
     *
     * @return 	Patterns_Collection the pouleplacerules
     */
    public function getPoulePlaceRules();
    /**
     * gets the Config
     *
     * @return 	array	the Config
     */
    public function getConfig();
    /**
     * gets if single
     *
     * @return 	bool    if single
     */
    public function isSingle();
}
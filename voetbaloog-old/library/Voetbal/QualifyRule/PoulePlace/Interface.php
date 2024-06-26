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
interface Voetbal_QualifyRule_PoulePlace_Interface
{
    /**
     * gets the FromPoulePlace
     *
     * @return 	Voetbal_PoulePlace_Interface	the FromPoulePlace
     */
    public function getFromPoulePlace();
    /**
     * puts the FromPoulePlace
     *
     * @param 	Voetbal_PoulePlace_Interface $oFromPoulePlace the FromPoulePlace which will be set
     * @return 	null
     */
    public function putFromPoulePlace( $oFromPoulePlace );
    /**
     * gets the ToPoulePlace
     *
     * @return 	Voetbal_PoulePlace_Interface	the ToPoulePlace
     */
    public function getToPoulePlace();
    /**
     * puts the ToPoulePlace
     *
     * @param 	Voetbal_PoulePlace_Interface $oToPoulePlace the ToPoulePlace which will be set
     * @return 	null
     */
    public function putToPoulePlace( $oToPoulePlace );
    /**
     * gets the QualifyRule
     *
     * @return 	Voetbal_QualifyRule_Interface	the QualifyRule
     */
    public function getQualifyRule();
    /**
     * puts the QualifyRule
     *
     * @param 	Voetbal_QualifyRule_Interface $oQualifyRule the QualifyRule which will be set
     * @return 	null
     */
    public function putQualifyRule( $oQualifyRule );
}
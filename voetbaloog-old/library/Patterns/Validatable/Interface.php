<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 914 2014-08-24 16:32:52Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
interface Patterns_Validatable_Interface
{
    /**
     * checks if valid
     *
     * @return 	bool	if Valid
     */
    public function isValid();
    /**
     * gets the validated datetime
     *
     * @return 	Agenda_DateTime 	the validated datetime
     */
    public function getValidatedDateTime();
    /**
     * puts the validated datetime
     *
     * @param   Agenda_DateTime     $oValidatedDateTime     the validated datetime which will be set
     * @return 	null
     */
    public function putValidatedDateTime( $oValidatedDateTime );
}
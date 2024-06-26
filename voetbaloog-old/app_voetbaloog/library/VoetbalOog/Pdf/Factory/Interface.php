<?php

/**
 *
 *
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
interface VoetbalOog_Pdf_Factory_Interface
{
	/**
	 * gets the input form for the pool
	 * 
	 * @param	VoetbalOog_Pool_User		$oPoolUser
	 * @return	Zend_Pdf
	 */
	public static function createPoolForm( $oPoolUser );
	/**
	 * gets the total pool pdf
	 * 
	 * @param	VoetbalOog_Pool			$oPool
	 * @param	VoetbalOog_Pool_User		$oPoolUser
	 * @return	Zend_Pdf
	 */
	public static function createPoolTotal( $oPool, $oPoolUser );
	/**
	* gets the competitionseason pdf
	*
	* @param	Voetbal_CompetitionSeason		$oCompetitionSeason the CompetitionSeason
	* @return	Zend_Pdf
	*/
	public static function createCompetitionSeason( $oCompetitionSeason );
}
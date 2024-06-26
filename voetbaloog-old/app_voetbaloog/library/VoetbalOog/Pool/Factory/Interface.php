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
 *
 * @package VoetbalOog
 */
interface VoetbalOog_Pool_Factory_Interface
{
	/**
	 * creates objects with sameroundbetconfig
	 *
	 * @param	VoetbalOog_Pool_User			$oPoolUser	the pooluser
	 * @param 	Construction_Option_Collection	$oOptions	the options
	 * @return 	Patterns_Collection
	 */
	public static function createObjectsWithSameRoundBetConfigFromDatabase( $oPoolUser, $oOptions = null );
	/**
	 * creates available pools for a user
	 * 
	 * from : all pools which user has participated and competitionseason is not equal to current 
	 * filter : from current competitionseason all pools
	 * 
	 * @param	Voetbal_CompetitionSeason 	$oCompetitionSeason	the competitionseason
	 * @param 	VoetbalOog_User					$oUser				the user
	 * @return 	Patterns_Collection
	 */ 
	public static function createObjectsAvailable( $oCompetitionSeason, $oUser );
	/**
	* checks if name is available for a certain competitionseason, user and name.
	*
	* if name is not used in current competitionseason
	* if name is not used in other competitionseasons which the user has not participated
	*
	* @param	Voetbal_CompetitionSeason 	$oCompetitionSeason	the competitionseason
	* @param 	VoetbalOog_User					$oUser				the user
	* @param	string							$sName				the poolname
	* @return 	boolean 											true | false
	*/
	public static function isNameAvailable( $oCompetitionSeason, $oUser, $sName );
}
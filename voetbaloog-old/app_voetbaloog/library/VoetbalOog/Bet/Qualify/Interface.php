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
interface VoetbalOog_Bet_Qualify_Interface
{
	/**
	 * gets the PoulePlace
	 *
	 * @return 	Voetbal_PoulePlace_Interface	the PoulePlace
	 */
	public function getPoulePlace();
	/**
	 * puts the PoulePlace
	 *
	 * @param Voetbal_PoulePlace_Interface $oPoulePlace the PoulePlace which will be set
	 * @return 	null
	 */
	public function putPoulePlace( $oPoulePlace );
	/**
	 * gets the Team
	 *
	 * @return 	Voetbal_Team_Interface	the Team
	 */
	public function getTeam();
	/**
	 * puts the Team
	 *
	 * @param Voetbal_Team_Interface $oTeam the Team which will be set
	 * @return 	null
	 */
	public function putTeam( $oTeam );
}
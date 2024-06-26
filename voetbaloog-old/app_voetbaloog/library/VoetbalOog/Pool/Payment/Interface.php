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
interface VoetbalOog_Pool_Payment_Interface
{
	/**
	 * gets the Pool
	 *
	 * @return 	VoetbalOog_Pool_Interface	the Pool
	 */
	public function getPool();
	/**
	 * puts the Pool
	 *
	 * @param VoetbalOog_Pool_Interface $oPool the Pool which will be set
	 * @return 	null
	 */
	public function putPool( $oPool );
	/**
	 * gets the Place
	 *
	 * @return 	int	the Place
	 */
	public function getPlace();
	/**
	 * puts the Place
	 *
	 * @param int $nPlace the Place which will be set
	 * @return 	null
	 */
	public function putPlace( $nPlace );
	/**
	 * gets the Times of the stake
	 *
	 * @return 	int	the Times of the stake
	 */
	public function getTimesStake();
	/**
	 * puts the Times of the Stake
	 *
	 * @param int $nTimesStake the Times of the Stake which will be set
	 * @return 	null
	 */
	public function putTimesStake( $nTimesStake );
}
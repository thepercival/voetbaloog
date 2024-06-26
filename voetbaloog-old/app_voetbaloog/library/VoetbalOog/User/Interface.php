<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Interface.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package		VoetbalOog
 */
interface VoetbalOog_User_Interface
{
	/**
	 * gets the poolusers
	 *
	 * @param 	Construction_Option_Collection	$p_objOptions		the options
	 * @return Patterns_Collection The pooluserss
	 */
	public function getPoolUsers( $p_objOptions = null );
	/**
	 * gets the Picture
	 *
	 * @return 	string|null	The Picture
	 */
	public function getPicture();
	/**
	 * puts the Picture
	 *
	 * @param 	string	$vtPicture	The Picture
	 * @return 	null
	 */
	public function putPicture( $vtPicture );
	/**
	 * gets the gender
	 *
	 * @return 	string	The gender
	 */
	public function getGender();
	/**
	 * puts the Gender
	 *
	 * @param 	string	$sGender	The gender
	 * @return 	null
	 */
	public function putGender( $sGender );
	/**
	 * gets the dateofbirth
	 *
	 * @return 	DateTime	The date of birth
	 */
	public function getDateOfBirth();
	/**
	 * puts the DateOfBirth
	 *
	 * @param 	DateTime	$oDateOfBirth	The DateOfBirth
	 * @return 	null
	 */
	public function putDateOfBirth( $oDateOfBirth );
	/**
	* gets the hashtype
	*
	* @return 	string	The hashtype
	*/
	public function getHashType();
	/**
	 * puts the type of hash
	 *
	 * @param 	string	$sHashType	The HashType
	 * @return 	null
	 */
	public function putHashType( $sHashType );
	/**
	* gets the Salted
	*
	* @return 	bool	the Salted
	*/
	public function getSalted();
	/**
	 * puts the Salted
	 *
	 * @param bool $bSalted the Salted which will be set
	 * @return 	null
	 */
	public function putSalted( $bSalted );
	/**
	* gets the activation key
	*
	* @return 	string	The activation key
	*/
	public function getActivationKey();
	/**
	 * puts the activation key
	 *
	 * @param 	string	$sActivationKey	The Activation Key
	 * @return 	null
	 */
	public function putActivationKey( $sActivationKey );
	/**
	* gets the facebookid
	*
	* @return 	string	The facebookid
	*/
	public function getFacebookId();
	/**
	 * puts the facebookid
	 *
	 * @param 	string	$sFacebookId	The facebookid
	 * @return 	null
	 */
	public function putFacebookId( $sFacebookId );
	/**
	 * gets the PeriodicEmail
	 *
	 * @return 	bool	the PeriodicEmail
	 */
	public function getPeriodicEmail();
	/**
	 * puts the PeriodicEmail
	 *
	 * @param bool $bPeriodicEmail the PeriodicEmail which will be set
	 * @return 	null
	*/
	public function putPeriodicEmail( $bPeriodicEmail );
}